<?php
/**
 * @module Users
 */
/**
 * Class representing 'Contact' rows in the 'Users' database
 * You can create an object of this class either to
 * access its non-static methods, or to actually
 * represent a contact row in the Users database.
 *
 * @class Users_Contact
 * @extends Base_Users_Contact
 */
class Users_Contact extends Base_Users_Contact
{
	/**
	 * The setUp() method is called the first time
	 * an object of this class is constructed.
	 * @method setUp
	 */
	function setUp()
	{
		parent::setUp();
	}
	
	/**
	 * Add contact with one or more labels
	 * @method addContact
	 * @static
	 * @param {string} $userId
	 *  The id of the user whose contact will be added
	 * @param {string|array} $label
	 *  The label of the contact. This can be a string or an array of strings, in which case
	 *  multiple contact rows are saved.
	 * @param {string} $contactUserId
	 *  The id of the user who is the contact
	 * @param {string} [$nickname='']
	 *  Optional nickname to assign to the contact
	 * @param {string} [$asUserId=null] The user to do this operation as.
	 *   Defaults to the logged-in user. Pass false to skip access checks.
	 * @param boolean [$unlessExists=false] If true, skips adding contact if it already exists
	 *   in the database.
	 * @throws {Q_Exception_RequiredField}
	 *	if $label is missing
	 * @return {array} Array of contacts that are saved
	 */
	static function addContact(
		$userId, 
		$label, 
		$contactUserId, 
		$nickname = '', 
		$asUserId = null,
		$unlessExists = false)
	{
		$canAddContact = Q::event('Users/Contact/addContact',
			@compact('userId', 'asUserId', 'contactUserId', 'label'),
			'before'
		);

		foreach (array('userId', 'label', 'contactUserId') as $field) {
			if (empty($$field)) {
				throw new Q_Exception_RequiredField($field);
			}
		}
		if (!isset($userId)) {
			$user = Users::loggedInUser(true);
			$userId = $user->id;
		}
		if (!isset($asUserId)) {
			$user = Users::loggedInUser(true);
			$asUserId = $user->id;
		}
		$canAddContact !== true && Users::canManageContacts($asUserId, $userId, $label, true);
		Users_User::fetch($userId, true);
		Users_User::fetch($contactUserId, true);
		$labels = is_array($label) ? $label : array($label);
		// Insert the contacts one by one to trigger the hooks
		$contacts = array();
		foreach ($labels as $l) {
			$contact = new Users_Contact();
			$contact->userId = $userId;
			$contact->label = $l;
			$contact->contactUserId = $contactUserId;
			if ($contact->retrieve() and $unlessExists) {
				$contacts[] = $contact;
				continue;
			}
			$contact->nickname = isset($nickname) ? $nickname : '';
			$contact->save(true);
			$contacts[] = $contact;
		}
		/**
		 * @event Users/Contact/addContact {after}
		 * @param {string} contactUserId
		 * @param {string} label
		 * @param {array} contacts
		 */
		Q::event('Users/Contact/addContact', 
			@compact('contactUserId', 'label', 'contacts'), 
			'after'
		);
		return $contacts;
	}
	
	/**
	 * Update a particular contact with a given userId, label, contactId.
	 * @method updateContact
	 * @static
	 * @param {string} $userId
	 * @param {string} $label
	 * @param {string} $contactUserId
	 * @param {array} $updates should be an array with only one key: "nickname"
	 * @param {string} [$asUserId=null] The user to do this operation as.
	 *   Defaults to the logged-in user. Pass false to skip access checks.
	 * @throws {Users_Exception_NotAuthorized}
	 * @return {Db_Query_Mysql}
	 */
	static function updateContact($userId, $label, $contactUserId, $updates, $asUserId = null)
	{
		foreach (array('userId', 'label', 'contactUserId', 'updates') as $field) {
			if (empty($$field)) {
				throw new Q_Exception_RequiredField(@compact($field));
			}
		}
		Users::canManageContacts($asUserId, $userId, $label, true);
		$contact = new Users_Contact();
		$contact->userId = $userId;
		$contact->label = $label;
		$contact->contactUserId = $contactUserId;
		if (!$contact->retrieve()) {
			throw new Q_Exception_MissingRow(array(
				'table' => 'Users_Contact',
				'criteria' => Q::json_encode($contact->fields)
			));
		}
		if (isset($updates['nickname'])) {
			$contact->nickname = $updates['nickname'];
		}
		$contact->save();
		return $contact;
	}
	
	/**
	 * Remove contact from label
	 * @method removeContact
	 * @static
	 * @param {string} $userId
	 * @param {string} $label
	 * @param {string} $contactUserId
	 * @param {string} [$asUserId=null] The user to do this operation as.
	 *   Defaults to the logged-in user. Pass false to skip access checks.
	 * @throws {Users_Exception_NotAuthorized}
	 * @return {Db_Query_Mysql}
	 */
	static function removeContact($userId, $label, $contactUserId, $asUserId = null)
	{
		$canRemoveContact = Q::event('Users/Contact/removeContact',
			@compact('userId', 'contactUserId', 'label'),
			'before'
		);

		foreach (array('userId', 'label', 'contactUserId') as $field) {
			if (empty($$field)) {
				throw new Q_Exception_RequiredField(array(
					'field' => $field
				));
			}
		}
		$canRemoveContact !== true && Users::canManageContacts($asUserId, $userId, $label, true);
		$contact = new Users_Contact();
		$contact->userId = $userId;
		$contact->label = $label;
		$contact->contactUserId = $contactUserId;
		return $contact->remove();
	}

	/**
	 * Retrieve array of contacts of userId under a label.
	 * Now supports externalLabels of the form "<<< {{platform}}_{{appId}}/{{suffix}}"
	 * @method fetch
	 * @static
	 * @param {string} $userId The user whose contacts to fetch
	 * @param {string|array|Db_Range|Db_Expression} $label Optionally filter contacts by label
	 * @param {array} [$options=array()]
	 * @param {integer} [$options.limit=false]
	 * @param {integer} [$options.offset=0] 
	 * @param {boolean} [$options.skipAccess] whether to skip access checks
	 * @param {string} [$options.asUserId] the user to do access checks as
	 * @param {string|array} [$options.contactUserId=null] Optionally filter by contactUserId
	 * @return {array} array of Users_Contact rows
	 */
	static function fetch($userId, $label = null, $options = array())
	{
		if (empty($userId)) {
			throw new Q_Exception_RequiredField(array('field' => 'userId'));
		}
		if (empty($options['skipAccess'])) {
			$asUserId = isset($options['asUserId'])
				? $options['asUserId']
				: Users::loggedInUser(true)->id;
			Users::canManageContacts($asUserId, $userId, $label, true, true);
		}
		$limit = isset($options['limit']) ? $options['limit'] : false;
		$offset = isset($options['offset']) ? $options['offset'] : 0;
		
		if (isset($options['contactUserId'])) {
			$contactUserId = $options['contactUserId'];
		}
		
		$criteria = @compact('userId', 'contactUserId');
		
		if ($label) {
			if (is_string($label) and substr($label, -1) === '/') {
				$label = new Db_Range($label, true, false, true);
			}
			$criteria['label'] = $label; // can be array, string, or range
		}

		$query = Users_Contact::select()->where($criteria);
		if ($limit) {
			$query = $query->limit($limit, $offset);
		}
		$nativeContacts = $query->fetchDbRows();

		$results = Users_ExternalTo::fetchXidsByLabels($userId, $label);
		$contacts = array();
		foreach ($results as $platform => $platformResults) {
			foreach ($platformResults as $appId => $xidsByLabels) {
				foreach ($xidsByLabels as $label => $xids) {
					$contactUserIds = Users_User::idsFromPlatformXids(
						$platform,
						$appId,
						$xids,
						true
					);
					foreach ($contactUserIds as $contactUserId) {
						$externalContacts[] = new Users_Contact(compact(
							'userId', 'label', 'contactUserId'
						));
					}
				}
			}
		}
		return array_merge($nativeContacts, $externalContacts);
	}
	
	/**
	 * Check if a contact with this primary key exists, and return it if so
	 * @method fetchOne
	 * @static
	 * @param {string} $userId
	 * @param {string} $label
	 * @param {string} $contactId
	 * @return {Db_Row|false}
	 */
	static function fetchOne($userId, $label, $contactId, $throwIfMissing = false)
	{
		if (!$userId or !$contactId) {
			return null;
		}
		if ($userId instanceof Users_User) {
			$userId = $userId->id;
		}
		if ($contactId instanceof Users_User) {
			$contactId = $contactId->id;
		}
		$contact = new Users_Contact();
		$contact->userId = $userId;
		$contact->label = $label;
		$contact->contactUserId = $contactId;
		$result = $contact->retrieve();
		if (!$result and $throwIfMissing) {
			throw new Q_Exception_MissingRow(array(
				'table' => 'contact',
				'criteria' => "userId $userId, label $label, contactId $contactId"
			));
		}
		return $result;
	}

	/* * * */
	/**
	 * Implements the __set_state method, so it can work with
	 * with var_export and be re-imported successfully.
	 * @method __set_state
	 * @param {array} $array
	 * @return {Users_Contact} Class instance
	 */
	static function __set_state(array $array) {
		$result = new Users_Contact();
		foreach($array as $k => $v)
			$result->$k = $v;
		return $result;
	}
};