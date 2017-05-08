<?php
/**
 * Users plugin
 * @module Users
 * @main Users
 */
/**
 * Static methods for the Users models.
 * @class Users
 * @extends Base_Users
 * @abstract
 */
abstract class Users extends Base_Users
{
	/*
	 * This is where you would place all the static methods for the models,
	 * the ones that don't strongly pertain to a particular row or table.

	 * * * */

	/**
	 * The facebook object that would be instantiated
	 * during the "Q/objects" event, if the request
	 * warrants it.
	 * @property $facebook
	 * @type Facebook
	 * @static
	 */
	static $facebook = null;

	/**
	 * Facebook objects that would be instantiated
	 * from cookies during the "Q/objects" event,
	 * if there are cookies for them.
	 * @property $facebooks
	 * @type array
	 * @static
	 */
	static $facebooks = array();
	
	/**
	 * Get the id of the main community from the config. Defaults to the app name.
	 * @return {string} The id of the main community for the installed app.
	 */
	static function communityId()
	{
		$communityId = Q_Config::get('Users', 'community', 'id', null);
		return $communityId ? $communityId : Q_Config::expect('Q', 'app');
	}
	
	/**
	 * Get the name of the main community from the config. Defaults to the app name.
	 * @return {string} The name of the main community for the installed app.
	 */
	static function communityName()
	{
		$communityName = Q_Config::get('Users', 'community', 'name', null);
		return $communityName ? $communityName : Q_Config::expect('Q', 'app');
	}
	
	/**
	 * Get the suffix of the main community from the config, such as "Inc." or "LLC"
	 * @return {string|null} The suffix of the main community for the installed app.
	 */
	static function communitySuffix()
	{
		return Q_Config::get('Users', 'community', 'suffix', null);
	}

	/**
	 * @param string [$publisherId] The id of the publisher relative to whom to calculate the roles. Defaults to the app name.
	 * @param {string|array|Db_Expression} [$filter=null] 
	 *  You can pass additional criteria here for the label field
	 *  in the `Users_Contact::select`, such as an array or Db_Range
	 * @param {array} [$options=array()] Any additional options to pass to the query, such as "ignoreCache"
	 * @param {string} [$userId=null] If not passed, the logged in user is used, if any
	 * @return {array} An associative array of $roleName => $contactRow pairs
	 * @throws {Users_Exception_NotLoggedIn}
	 */
	static function roles(
		$publisherId = null,
		$filter = null,
		$options = array(),
		$userId = null)
	{
		if (empty($publisherId)) {
			$publisherId = Users::communityId();
		}
		if (!isset($userId)) {
			$user = Users::loggedInUser(false, false);
			if (!$user) {
				return array();
			}
			$userId = $user->id;
		}
		$contacts = Users_Contact::select('*')
			->where(array(
				'userId' => $publisherId,
				'contactUserId' => $userId
			))->andWhere($filter ? array('label' => $filter) : null)
			->options($options)
			->fetchDbRows(null, null, 'label');
		return $contacts;
	}

	/**
	 * Intelligently retrieves user by id
	 * @method fetch
	 * @static
	 * @param {string} $userId
	 * @param {boolean} [$throwIfMissing=false] If true, throws an exception if the user can't be fetched
	 * @return {Users_User|null}
	 * @throws {Users_Exception_NoSuchUser} If the URI contains an invalid "username"
	 */
	static function fetch ($userId, $throwIfMissing = false)
	{
		return Users_User::fetch($userId, $throwIfMissing);
	}

	/**
	 * @method oAuth
	 * @static
	 * @param {string} $platform The name of the oAuth platform, under Users/apps config
	 * @param {string} [$appId=Q::app()] Only needed if you have multiple apps on platform
	 * @return {Zend_Oauth_Client}
	 * @throws {Users_Exception_NotLoggedIn} If user is not logged in
	 */
	static function oAuth($platform, $appId = null)
	{
		$nativeuser = self::loggedInUser();

		if(!$nativeuser)
			throw new Users_Exception_NotLoggedIn();

		if (!isset($appId)) {
			$appId = Q::app();
		}

		#Set up oauth options
		$oauthOptions = Q_Config::expect('Users', 'apps', $platform, $appId, 'oauth');
		$customOptions = Q_Config::get('Users', 'apps', $platform, $appId, 'options', null);

		#If the user already has a token in our DB:
		$appuser = new Users_AppUser();
		$appuser->userId = $nativeuser->id;
		$appuser->platform = $platform;
		$appuser->appId = $appId;

		if($appuser->retrieve('*', true))
		{
				$zt = new Zend_Oauth_Token_Access();
				$zt->setToken($appuser->access_token);
				$zt->setTokenSecret($appuser->session_secret);

				return $zt->getHttpClient($oauthOptions);
		}

		#Otherwise, obtain a token from platform:
		$consumer = new Zend_Oauth_Consumer($oauthOptions);

		if(isset($_GET['oauth_token']) && isset($_SESSION[$platform.'_request_token'])) //it's a redirect back from google
		{
			$token = $consumer->getAccessToken($_GET, unserialize($_SESSION[$platform.'_request_token']));

			$_SESSION[$platform.'_access_token'] = serialize($token);
			$_SESSION[$platform.'_request_token'] = null;

			#Save tokens to database
			$appuser->access_token = $token->getToken();
			$appuser->session_secret = $token->getTokenSecret();
			$appuser->save();

			return $token->getHttpClient($oauthOptions);
		}
		else //it's initial pop-up load
		{
			$token = $consumer->getRequestToken($customOptions);

			$_SESSION[$platform.'_request_token'] = serialize($token);

			$consumer->redirect();

			return null;
		}

	}

	/**
	 * @method oAuthClear
	 * @static
	 * @param {string} $platform The name of the oAuth platform, under Users/apps config
	 * @param {string} [$appId=Q::app()] Only needed if you have multiple apps on platform
	 * @throws {Users_Exception_NotLoggedIn} If user is not logged in
	 */
	static function oAuthClear($platform, $appId = null)
	{
		$nativeuser = self::loggedInUser();

		if(!$nativeuser)
			throw new Users_Exception_NotLoggedIn();
		
		if (!isset($appId)) {
			$app = Q::app();
			$appId = Q_Config::expect('Users', 'apps', $platform, $app, 'appId');
		}

		$appuser = new Users_AppUser();
		$appuser->userId = $nativeuser->id;
		$appuser->platform = $platform;
		$appuser->appId = $appId;
		$appuser->remove();
	}

	/**
	 * Retrieves the currently logged-in user from the session.
	 * If the user was not originally retrieved from the database,
	 * inserts a new one.
	 * Thus, this can also be used to turn visitors into registered
	 * users.
	 * @method authenticate
	 * @static
	 * @param {string} $platform Currently only supports the value "facebook".
	 * @param {string} [$appId=null] The id of the app within the specified platform.
	 * @param {&boolean} [$authenticated=null] If authentication fails, puts false here.
	 *  Otherwise, puts one of the following:
	 *  * 'registered' if user just registered,
	 *  * 'adopted' if a futureUser was just adopted,
	 *  * 'connected' if a logged-in user just connected the platform account for the first time,
	 *  * 'authorized' if a logged-in user was connected to platform but just authorized this app for the first time
	 *  or true otherwise.
	 * @param {array} [$import=Q_Config::get('Users', 'import', $platform)]
	 *  Array of things to import from platform if they are not already set.
	 *  Can include "emailAddress", "firstName", "lastName" and "username".
	 *  If the email address is imported, it is set without requiring verification, and
	 *  any email under Users/transactional/authenticated is set
	 *  If true, and the user's email address is not set yet,
	 *  imports the email address from the platform if it is available,
	 *  and sets it as the user's email address without requiring verification.
	 * @return {Users_User}
	 */
	static function authenticate(
		$platform,
		$appId = null,
		&$authenticated = null,
		$import = null)
	{
		if (!isset($import)) {
			$import = Q_Config::get('Users', 'import', $platform, array(
				'emailAddress', 'firstName', 'lastName'
			));
		}
		if (!isset($appId)) {
			$appId = Q_Config::expect('Users', 'apps', 'facebook', Q::app(), 'appId');
		} else {
			list($appId, $appInfo) = Users::appInfo($platform, $appId);
		}
		$authenticated = null;

		$during = 'authenticate';

		$return = null;
		/**
		 * @event Users/authenticate {before}
		 * @param {string} platform
		 * @param {string} appId
		 * @return {Users_User}
		 */
		$return = Q::event('Users/authenticate', compact('platform', 'appId'), 'before');
		if (isset($return)) {
			return $return;
		}

		if (!isset($platform) or $platform != 'facebook') {
			throw new Q_Exception_WrongType(array('field' => 'platform', 'type' => '"facebook"'));
		}

		if (!isset($appId)) {
			throw new Q_Exception_WrongType(array('field' => 'appId', 'type' => 'a valid facebook app id'));
		}

		Q_Session::start();

		// First, see if we've already logged in somehow
		if ($user = self::loggedInUser()) {
			// Get logged in user from session
			$userWasLoggedIn = true;
			$retrieved = true;
		} else {
			// Get an existing user or create a new one
			$userWasLoggedIn = false;
			$retrieved = false;
			$user = new Users_User();
		}
		$authenticated = false;
		$emailAddress = null;

		// Try authenticating the user with the specified platform
		switch ($platform) {
		case 'facebook':
			$facebook = Users::facebook($appId);
			$fb_uid = Users_AppUser::loggedInUid($facebook);
			if (!$facebook or !$fb_uid) {
				// no facebook authentication is happening
				return $userWasLoggedIn ? $user : false;
			}
			$dn = isset($user->id) && $user->displayName();
			$importEmail = in_array('emailAddress', $import) && empty($user->emailAddress);
			if (!$dn or $importEmail) {
				$map = array(
					'firstName' => 'first_name',
					'lastName' => 'last_name'
				);
				$fields = array('email');
				foreach ($map as $k => $v) {
					if (in_array($k, $import)) {
						$fields[] = $v;
					}
				}
				$response = $facebook->get('/me?fields='.implode(',', $fields));
				$userNode = $response->getGraphUser();
				$emailAddress = $userNode->getField('email');
				if (!$dn) {
					Users::$cache['facebookUserData'] = $userNode->uncastItems();
				}
			}

			$authenticated = true;
			if ($retrieved) {
				if (empty($user->fb_uid)) {
					// this is a logged-in user who was never authenticated with facebook.
					// First, let's find any other user who has authenticated with this facebook uid,
					// and set their fb_uid to NULL.
					$authenticated = 'connected';
					$ui = Users::identify('facebook', $fb_uid);
					if ($ui) {
						Users_User::update()->set(array(
							'fb_uid' => 0
						))->where(array('id' => $ui->userId))->execute();
						$ui->remove();
					}

					// Now, let's associate their account with this facebook uid.
					$user->fb_uid = $fb_uid;
					$user->save();

					// Save the identifier in the quick lookup table
					$ui = new Users_Identify();
					$ui->identifier = "facebook:$fb_uid";
					$ui->state = 'verified';
					$ui->userId = $user->id;
					$ui->save(true);
				} else if ($user->fb_uid !== $fb_uid) {
					// The logged-in user was authenticated with facebook already,
					// and associated with a different facebook id.
					// Most likely, a completely different person has logged into facebook
					// at this computer. So rather than changing the associated facebook uid
					// for the logged-in user, simply log out and essentially run this function
					// from the beginning again.
					Users::logout();
					$userWasLoggedIn = false;
					$user = new Users_User();
					$retrieved = false;
				}
			}
			if (!$retrieved) {
				$ui = Users::identify('facebook', $fb_uid, null);
				if ($ui) {
					$user = new Users_User();
					$user->id = $ui->userId;
					$exists = $user->retrieve();
					if (!$exists) {
						throw new Q_Exception("Users_Identify for fb_uid $fb_uid exists but not user with id {$ui->userId}");
					}
					$retrieved = true;
					if ($ui->state === 'future') {
						$authenticated = 'adopted';

						$user->fb_uid = $fb_uid;
						$user->signedUpWith = 'facebook'; // should have been "none" before this
						/**
						 * @event Users/adoptFutureUser {before}
						 * @param {Users_User} user
						 * @param {string} during
						 * @return {Users_User}
						 */
						$ret = Q::event('Users/adoptFutureUser', compact('user', 'during'), 'before');
						if ($ret) {
							$user = $ret;
						}
						$user->save();

						$ui->state = 'verified';
						$ui->save();
						/**
						 * @event Users/adoptFutureUser {after}
						 * @param {Users_User} user
						 * @param {array} links
						 * @param {string} during
						 * @return {Users_User}
						 */
						Q::event('Users/adoptFutureUser', compact('user', 'links', 'during'), 'after');
					} else {
						// If we are here, that simply means that we already verified the
						// $fb_uid => $userId mapping for some existing user who signed up
						// and has been using the system. So there is nothing more to do besides
						// setting this user as the logged-in user below.
					}
				} else {
					// user is logged out and no user corresponding to $fb_uid yet

					$authenticated = 'registered';

					if (!empty($emailAddress)) {
						$ui = Users::identify('email', $emailAddress, 'verified');
						if ($ui) {
							// existing user  identified from verified email address
							// load it into $user
							$user = new Users_User();
							$user->id = $ui->userId;
							$user->retrieve(null, null, true)
							->caching()
							->resume();
						}
					}

					$user->fb_uid = $fb_uid;
					/**
					 * @event Users/insertUser {before}
					 * @param {Users_User} user
					 * @param {string} during
					 * @return {Users_User}
					 */
					$ret = Q::event('Users/insertUser', compact('user', 'during'), 'before');
					if (isset($ret)) {
						$user = $ret;
					}
					if (!$user->wasRetrieved()) {
						// Register a new user basically and give them an empty username for now
						$user->username = "";
						$user->icon = 'default';
						$user->signedUpWith = 'facebook';
						$user->save();

						// Save the identifier in the quick lookup table
						$ui = new Users_Identify();
						$ui->identifier = "facebook:$fb_uid";
						$ui->state = 'verified';
						$ui->userId = $user->id;
						$ui->save(true);

						// Download and save facebook icon for the user
						$sizes = Q_Config::expect('Users', 'icon', 'sizes');
						sort($sizes);
						$icon = array();
						foreach ($sizes as $size) {
							$parts = explode('x', $size);
							$width = Q::ifset($parts, 0, '');
							$height = Q::ifset($parts, 1, '');
							$width = $width ? $width : $height;
							$height = $height ? $height : $width;
							$icon["$size.png"] = "https://graph.facebook.com/$fb_uid/picture?width=$width&height=$height";
						}
						if (!Q_Config::get('Users', 'register', 'icon', 'leaveDefault', false)) {
							self::importIcon($user, $icon);
							$user->save();
						}
					}
			 	}
			}
			Users::$cache['user'] = $user;
			Users::$cache['authenticated'] = $authenticated;
			break;
		default:
			// not sure how to log this user in
			return $userWasLoggedIn ? $user : false;
		}
		// Check we should import an email address from the platform
		if (in_array('emailAddress', $import) and !empty($emailAddress) and empty($user->emailAddress)) {
			// We automatically set their email as verified, without a confirmation message,
			// because we trust the authentication platform.
			$user->setEmailAddress($emailAddress, true, $email);
			// But might send a welcome email to the users who just authenticated
			$emailSubject = Q_Config::get('Users', 'transactional', 'authenticated', 'subject', false);
			$emailView = Q_Config::get('Users', 'transactional', 'authenticated', 'body', false);
			if ($emailSubject !== false and $emailView) {
				$email->sendMessage($emailAddress, $emailSubject, $emailView);
			}
		}
		if (!$userWasLoggedIn) {
			self::setLoggedInUser($user);
		}

		if ($retrieved) {
			/**
			 * @event Users/updateUser {after}
			 * @param {Users_User} user
			 * @param {string} during
			 */
			Q::event('Users/updateUser', compact('user', 'during'), 'after');
		} else {
			/**
			 * @event Users/insertUser {after}
			 * @param {string} during
			 * @param {Users_User} 'user'
			 */
			Q::event('Users/insertUser', compact('user', 'during'), 'after');
		}

		// Now make sure our master session contains the
		// session info for the platform app.
		if ($platform == 'facebook') {
			$accessToken = $facebook->getDefaultAccessToken();
			$at = $accessToken->getValue();
			if (isset($_SESSION['Users']['appUsers']['facebook_'.$appId])) {
				// Facebook app user exists. Do we need to update it? (Probably not!)
				$pk = $_SESSION['Users']['appUsers']['facebook_'.$appId];
				$app_user = Users_AppUser::select('*')->where($pk)->fetchDbRow();
				if (empty($app_user)) {
					// somehow this app_user disappeared from the database
					throw new Q_Exception_MissingRow(array(
						'table' => 'AppUser',
						'criteria' => http_build_query($pk, null, ' & ')
					));
				}
				if (empty($app_user->state) or $app_user->state !== 'added') {
					$app_user->state = 'added';
				}

				if (!isset($app_user->access_token) or ($at and $at != $app_user->access_token)) {
					/**
					 * @event Users/authenticate/updateAppUser {before}
					 * @param {Users_User} user
					 */
					Q::event('Users/authenticate/updateAppUser', compact('user', 'app_user'), 'before');
					$app_user->access_token = $at;
					$app_user->session_expires = $accessToken->getExpiresAt()->getTimestamp();
					$app_user->save(); // update access_token in app_user
					/**
					 * @event Users/authenticate/updateAppUser {after}
					 * @param {Users_User} user
					 */
					Q::event('Users/authenticate/updateAppUser', compact('user', 'app_user'), 'after');
				}
			} else {
				// We have to put the session info in
				$app_user = new Users_AppUser();
				$app_user->userId = $user->id;
				$app_user->platform = 'facebook';
				$app_user->appId = $appId;
				if ($app_user->retrieve()) {
					// App user exists in database. Do we need to update it?
					if (!isset($app_user->access_token) or $app_user->access_token != $at) {
						/**
						 * @event Users/authenticate/updateAppUser {before}
						 * @param {Users_User} user
						 */
						Q::event('Users/authenticate/updateAppUser', compact('user', 'app_user'), 'before');
						$app_user->access_token = $at;
						$app_user->save(); // update access_token in app_user
						/**
						 * @event Users/authenticate/updateAppUser {after}
						 * @param {Users_User} user
						 */
						Q::event('Users/authenticate/updateAppUser', compact('user', 'app_user'), 'after');
					}
				} else {
					if (empty($app_user->state) or $app_user->state !== 'added') {
						$app_user->state = 'added';
					}
					$app_user->access_token = $at;
					$app_user->session_expires = $accessToken->getExpiresAt()->getTimestamp();
					$app_user->platform_uid = $user->fb_uid;
					/**
					 * @event Users/insertAppUser {before}
					 * @param {Users_User} user
					 * @param {string} 'during'
					 */
					Q::event('Users/insertAppUser', compact('user', 'during'), 'before');
					// The following may update an existing app_user row
					// in the rare event that someone tries to tie the same
					// platform account to two different accounts.
					// A platform account can only reference one account, so the
					// old connection will be dropped, and the new connection saved.
					$app_user->save(true);
					/**
					 * @event Users/authenticate/insertAppUser {after}
					 * @param {Users_User} user
					 */
					Q::event('Users/authenticate/insertAppUser', compact('user'), 'after');
					$authenticated = 'authorized';
				}
			}

			$_SESSION['Users']['appUsers']['facebook_'.$appId] = $app_user->getPkValue();
		}

		Users::$cache['authenticated'] = $authenticated;

		/**
		 * @event Users/authenticate {after}
		 * @param {string} platform
		 * @param {string} appId
		 */
		Q::event('Users/authenticate', compact('platform', 'appId'), 'after');

		// At this point, $user is set.
		return $user;
	}

	/**
	 * Logs a user in using a login identifier and a pasword
	 * @method login
	 * @static
	 * @param {string} $identifier Could be an email address, a mobile number, or a user id.
	 * @param {string} $passphrase The passphrase to hash, etc.
	 * @param {boolean} $isHashed Whether the first passphrase hash iteration occurred, e.g. on the client
	 * @return {Users_User}
	 * @throws {Q_Exception_RequiredField} If 'identifier' field is not defined
	 * @throws {Q_Exception_WrongValue} If identifier is not e-mail or modile
	 * @throws {Users_Exception_NoSuchUser} If user does not exists
	 * @throws {Users_Exception_WrongPassphrase} If passphrase is wrong
	 */
	static function login(
		$identifier,
		$passphrase,
		$isHashed)
	{
		$return = null;
		/**
		 * @event Users/login {before}
		 * @param {string} identifier
		 * @param {string} passphrase
		 * @return {Users_User}
		 */
		$return = Q::event('Users/login', compact('identifier', 'passphrase'), 'before');
		if (isset($return)) {
			return $return;
		}

		if (!isset($identifier)) {
			throw new Q_Exception_RequiredField(array('field' => 'identifier'), 'identifier');
		}

		Q_Session::start();
		$sessionId = Q_Session::id();

		if (Q_Valid::email($identifier, $emailAddress)) {
			$user = Users::userFromContactInfo('email', $emailAddress);
		} else if (Q_Valid::phone($identifier, $mobileNumber)) {
			$user = Users::userFromContactInfo('mobile', $mobileNumber);
		} else {
			throw new Q_Exception_WrongValue(array(
				'field' => 'identifier',
				'range' => 'email address or mobile number'
			), array('identifier', 'emailAddress', 'mobileNumber'));
		}
		if (!$user) {
			throw new Users_Exception_NoSuchUser(compact('identifier'));
		}

		// First, see if we've already logged in somehow
		if ($logged_in_user = self::loggedInUser()) {
			// Get logged in user from session
			if ($logged_in_user->id === $user->id) {
				return $logged_in_user;
			}
		}

		// User exists in database. Now check the passphrase.
		$passphraseHash = $user->computePassphraseHash($passphrase, $isHashed);
		if ($passphraseHash != $user->passphraseHash) {
			// Passphrases don't match!
			throw new Users_Exception_WrongPassphrase(compact('identifier'), 'passphrase');
		}

		/**
		 * @event Users/login {after}
		 * @param {string} identifier
		 * @param {string} passphrase
		 * @param {Users_User} 'user'
		 */
		Q::event('Users/login', compact(
			'identifier', 'passphrase', 'user'
		), 'after');
		// Now save this user in the session as the logged-in user
		self::setLoggedInUser($user);
		return $user;

	}

	/**
	 * Logs a user out
	 * @method logout
	 * @static
	 */
	static function logout()
	{
		// Access the session, if we haven't already.
		$user = self::loggedInUser();
		$sessionId = Q_Session::id();

		/**
		 * One last chance to do something.
		 * Hooks shouldn't be able to cancel the logout, though.
		 * @event Users/logout {before}
		 * @param {Users_User} user
		 */
		Q::event('Users/logout', compact('user'), 'before');

		$deviceId = null;
		if ($session = Q_Session::row()) {
			$deviceId = $session->deviceId;
		}
		
		if ($user) {
			Q_Utils::sendToNode(array(
				"Q/method" => "Users/logout",
				"sessionId" => $sessionId,
				"userId" => $user->id,
				"deviceId" => $deviceId
			));

			// forget the device for this user/session
			Users_Device::delete()->where(array(
				'userId' => $user->id,
				'sessionId' => $sessionId
			))->execute();
		}

		// Destroy the current session, which clears the $_SESSION and all notices, etc.
		Q_Session::destroy();
		
		/**
		 * After the logout has taken place
		 * @event Users/logout {after}
		 * @param {Users_User} user
		 */
		Q::event('Users/logout', compact('user'), 'after');
	}

	/**
	 * Get the logged-in user's information
	 * @method loggedInUser
	 * @static
	 * @param {boolean} [$throwIfNotLoggedIn=false]
	 *   Whether to throw a Users_Exception_NotLoggedIn if no user is logged in.
	 * @param {boolean} [$startSession=true]
	 *   Whether to start a PHP session if one doesn't already exist.
	 * @return {Users_User|null}
	 * @throws {Users_Exception_NotLoggedIn} If user is not logged in and
	 *   $throwIfNotLoggedIn is true
	 */
	static function loggedInUser(
		$throwIfNotLoggedIn = false,
		$startSession = true)
	{
		if ($startSession === false and !Q_Session::id()) {
			return null;
		}
		Q_Session::start();

		$nonce = Q_Session::$nonceWasSet or Q_Valid::nonce($throwIfNotLoggedIn, true);

		if (!$nonce or !isset($_SESSION['Users']['loggedInUser']['id'])) {
			if ($throwIfNotLoggedIn) {
				throw new Users_Exception_NotLoggedIn();
			}
			return null;
		}
		$id = $_SESSION['Users']['loggedInUser']['id'];
		$user = Users_User::fetch($id);
		if (!$user and $throwIfNotLoggedIn) {
			throw new Users_Exception_NotLoggedIn();
		}
		return $user;
	}

	/**
	 * Use with caution! This bypasses the usual methods of authentication.
	 * This functionality should not be exposed externally.
	 * @method setLoggedInUser
	 * @static
	 * @param {Users_User|string} $user The user object or user id
	 * @param {array} [$options] Some options for the method
	 * @param {string} [$options.notice=Q_Config::expect('Users','login','notice')]
	 *  A notice to show to the newly logged-in user that they have been
	 *  logged in. This notice only appears if another user was logged in
	 *  before this method was called, to draw their attention to the sudden
	 *  switch. To turn off this notice, pass null here.
	 * @param {boolean} [$options.keepSessionId=false]
	 *  Set to true to skip regenerating the session id, perhaps because you just
	 *  generated your own session id and you are sure that 
	 *  there cannot be any session fixation attacks.
	 * @return {boolean} Whether logged in user id was changed.
	 */
	static function setLoggedInUser($user = null, $options = array())
	{
		if ($user and is_string($user)) {
			$user = Users_User::fetch($user, true);
		}
		$loggedInUserId = Q::ifset($_SESSION, 'Users', 'loggedInUser', 'id', null);
		if (!$user and $user->id === $loggedInUserId) {
			// This user is already the logged-in user. Do nothing.
			return false;
		}
		
		/**
		 * @event Users/setLoggedInUser {before}
		 * @param {Users_User} user
		 * @param {string} loggedInUserId
		 */
		Q::event('Users/setLoggedInUser', compact('user', 'loggedInUserId'), 'before');
		
		if ($loggedInUserId) {
			// always log out existing user, so their session data isn't carried over
			Users::logout();
		} else {
			// Otherwise the session data of the logged-out user is merged
			// into the logged-in user's session, so it can be used!
		}
		if (!$user) {
			// nothing more to do, this is essentially a call to log out
			return;
		}

		// Change the session id to prevent session fixation attacks
		if (empty($options['keepSessionId'])) {
			$duration = null;
			$session = new Users_Session();
			$session->id = Q_Session::id();
			if ($session->id and $session->retrieve()) {
				$duration = $session->duration;
			}
			$sessionId = Q_Session::regenerateId(true, $duration);
		}

		// Store the new information in the session
		$snf = Q_Config::get('Q', 'session', 'nonceField', 'nonce');
		$_SESSION['Users']['loggedInUser']['id'] = $user->id;
		Q_Session::setNonce(true);
		
		$user->sessionCount = isset($user->sessionCount)
			? $user->sessionCount + 1
			: 1;

		/**
		 * @event Users/setLoggedInUser/updateSessionId {before}
		 * @param {Users_User} user
		 */
		Q::event('Users/setLoggedInUser/updateSessionId', compact('user'), 'before');
		
		$user->sessionId = $sessionId;
		$user->save(); // update sessionId in user
		
		/**
		 * @event Users/setLoggedInUser/updateSessionId {after}
		 * @param {Users_User} user
		 */
		Q::event('Users/setLoggedInUser/updateSessionId', compact('user'), 'after');
		
		$votes = Users_Vote::select('*')
			->where(array(
				'userId' => $user->id,
				'forType' => 'Users/hinted'
			))->fetchDbRows(null, null, 'forId');
		
		// Cache already shown hints in the session.
		// The consistency of this mechanism across sessions is not perfect, i.e.
		// the same hint may repeat in multiple concurrent sessions, but it's ok.
		$_SESSION['Users']['hinted'] = array_keys($votes);
		
		if ($loggedInUserId) {
			// Set a notice for the user to alert them that the account has changed
			$template = Q_Config::expect('Users', 'login', 'notice');
			$displayName = $user->displayName();
			$html = Q_Handlebars::renderSource($template, compact(
				'user', 'displayName'
			));
			Q_Response::setNotice('Users::setLoggedInUser', $html, true);
		}

		/**
		 * @event Users/setLoggedInUser {after}
		 * @param {Users_User} user
		 */
		Q::event('Users/setLoggedInUser', compact('user'), 'after');
		self::$loggedOut = false;
		
		return true;
	}

	/**
	 * Registers a user in the system.
	 * @method register
	 * @static
	 * @param {string} $username The name of the user
	 * @param {string|array} $identifier Can be an email address or mobile number. Or it could be an array of $type => $info
	 * @param {string} [$identifier.identifier] an email address or phone number
	 * @param {array} [$identifier.device] an array with keys
	 *   "deviceId", "platform", "appId", "version", "formFactor"
	 *   to store in the Users_Device table for sending notifications
	 * @param {array} [$identifier.app] an array with "platform" key, and optional "appId"
	 * @param {array|string|true} [$icon=true] Array of filename => url pairs, or true to generate an icon
	 * @param {array} [$options=array()] An array of options that could include:
	 * @param {string} [$options.activation] The key under "Users"/"transactional" config to use for sending an activation message. Set to false to skip sending the activation message for some reason.
	 * @return {Users_User}
	 * @throws {Q_Exception_WrongType} If identifier is not e-mail or modile
	 * @throws {Q_Exception} If user was already verified for someone else
	 * @throws {Users_Exception_AlreadyVerified} If user was already verified
	 * @throws {Users_Exception_UsernameExists} If username exists
	 */
	static function register(
		$username, 
		$identifier, 
		$icon = array(), 
		$options = array())
	{
		/**
		 * @event Users/register {before}
		 * @param {string} username
		 * @param {string|array} identifier
		 * @param {string} icon
		 * @param {string} platform
		 * @return {Users_User}
		 */
		$return = Q::event('Users/register', compact('username', 'identifier', 'icon', 'platform', 'options'), 'before');
		if (isset($return)) {
			return $return;
		}

		$during = 'register';
		$platform = null;
		$appId = null;

		if (is_array($identifier)) {
			reset($identifier);
			switch (key($identifier)) {
				case 'app':
					$app = $identifier['app'];
					$fields = array('platform');
					Q_Valid::requireFields($fields, $app, true);
					$platform = $identifier['app']['platform'];
					$appId = Q::ifset($app, 'appId', null);
					break;
				case 'device':
					$device = $identifier['device'];
					$fields = array('deviceId', 'platform', 'appId', 'version', 'formFactor');
					Q_Valid::requireFields($fields, $device, true);
					$identifier = Q::ifset($identifier, 'identifier', null);
					if (empty($device['platform'])) {
						throw new Q_Exception_RequiredField(array('field' => 'identifier.device.platform'));
					}
					$signedUpWith = $device['platform'];
					break;
				default:
					throw new Q_Exception_WrongType(array(
						'field' => 'identifier', 
						'type' => 'an array with entry named "device"'
					));
			}
		} else if (!$identifier) {
			throw new Q_Exception_RequiredField(array('field' => 'identifier'));
		}
		$ui_identifier = null;
		if ($identifier) {
			if (Q_Valid::email($identifier, $emailAddress)) {
				$ui_identifier = $emailAddress;
				$key = 'email address';
				$signedUpWith = 'email';
			} else if (Q_Valid::phone($identifier, $mobileNumber)) {
				$ui_identifier = $mobileNumber;
				$key = 'mobile number';
				$signedUpWith = 'mobile';
			} else {
				throw new Q_Exception_WrongType(array(
					'field' => 'identifier',
					'type' => 'email address or mobile number'
				), array('emailAddress', 'mobileNumber'));
			}
		}

		$user = false;
		if ($platform) {
			if ($platform != 'facebook') {
				throw new Q_Exception_WrongType(array(
					'field' => 'platform', 
					'type' => '"facebook"'
				));
			}
			if ($facebook = Users::facebook()) {
				$uid = Users_AppUser::loggedInUid($facebook);
				try {
					// authenticate (and possibly adopt) an existing platform user
					// or insert a new user during this authentication
					$user = Users::authenticate($platform, $appId, $authenticated, true);
				} catch (Exception $e) {

				}
				if ($user) {
					// the user is also logged in
					$adopted = true;

					// Adopt this platform user
					/**
					 * @event Users/adoptFutureUser {before}
					 * @param {Users_User} user
					 * @param {string} during
					 * @return {Users_User}
					 */
					$ret = Q::event('Users/adoptFutureUser', compact('user', 'during'), 'before');
					if ($ret) {
						$user = $ret;
					}
				}
			}
		}
		if (!$user) {
			$user = new Users_User(); // the user we will save in the database
		}
		if (empty($adopted) and $ui_identifier) {
			// We will be inserting a new user into the database, so check if
			// this identifier was already verified for someone else.
			$ui = Users::identify($signedUpWith, $ui_identifier);
			if ($ui) {
				throw new Users_Exception_AlreadyVerified(compact('key'), array(
					'emailAddress', 'mobileNumber', 'identifier'
				));
			}
		}

		// Insert a new user into the database, or simply modify an existing (adopted) user
		$user->username = $username;
		if (!isset($user->signedUpWith) or $user->signedUpWith == 'none') {
			$user->signedUpWith = $signedUpWith;
		}
		$user->icon = 'default';
		$user->passphraseHash = '';
		$url_parts = parse_url(Q_Request::baseUrl());
		if (isset($url_parts['host'])) {
			// By default, the user's url would be this:
			$user->url = $username ? "http://$username.".$url_parts['host'] : "";
		}
		/**
		 * @event Users/insertUser {before}
		 * @param {string} during
		 * @param {Users_User} user
		 */
		Q::event('Users/insertUser', compact('user', 'during'), 'before');

		$user->id = Users_User::db()->uniqueId(Users_User::table(), 'id', null, array(
			'filter' => array('Users_User', 'idFilter')
		));

		// the following code could throw exceptions
		if (empty($user->emailAddress) and empty($user->mobileNumber)
			and ($signedUpWith === 'email' or $signedUpWith === 'mobile')) {
			// Add an email address or mobile number to the user, that they'll have to verify
			$activation = Q::ifset($options, 'activation', 'activation');
			if ($activation) {
				$subject = Q_Config::get('Users', 'transactional', $activation, "subject", null);
				$body = Q_Config::get('Users', 'transactional', $activation, "body", null);
			} else {
				$subject = $body = null;
			}
			if ($signedUpWith === 'email') {
				$user->addEmail($identifier, $subject, $body, array(), $options);
			} else if ($signedUpWith === 'mobile') {
				$p = $options;
				if ($delay = Q_Config::get('Users', 'register', 'delaySms', 0)) {
					$p['delay'] = $delay;
				}
				$sms = Q_Config::get('Users', 'transactional', $activation, "sms", null);
				$user->addMobile($mobileNumber, $sms, array(), $p);
			}
		}
		if (!empty($device)) {
			$device['userId'] = $user->id;
			Users_Device::add($device);
		}

		$user->save(); // saves the user with the id

		/**
		 * @event Users/insertUser {after}
		 * @param {string} during
		 * @param {Users_User} user
		 */
		Q::event('Users/insertUser', compact('user', 'during'), 'after');

		$sizes = Q_Config::expect('Users', 'icon', 'sizes');
		sort($sizes);

		if (empty($icon)) {
			switch ($platform) {
			case 'facebook':
				// let's get this user's icon on facebook
				if (empty($uid)) {
					break;
				}
				$icon = array();
				foreach ($sizes as $size) {
					$icon["$size.png"] = "https://graph.facebook.com/$uid/picture?width=$size&height=$size";
				}
				break;
			}
		} else {
			// Import the user's icon and save it
			if (is_string($icon)) {
				// assume it's from gravatar
				$iconString = $icon;
				$icon = array();
				foreach ($sizes as $size) {
					$icon["$size.png"] = "$iconString&s=$size";
				}
			} else {
				// locally generated icons
				$hash = md5(strtolower(trim($identifier)));
				$icon = array();
				foreach ($sizes as $size) {
					$icon["$size.png"] = array('hash' => $hash, 'size' => $size);
				}
			}
		}
		if (!Q_Config::get('Users', 'register', 'icon', 'leaveDefault', false)) {
			self::importIcon($user, $icon);
			$user->save();
		}

		/**
		 * @event Users/register {after}
		 * @param {string} username
		 * @param {string|array} identifier
		 * @param {string} icon
		 * @param {Users_User} user
		 * @param {string} platform
		 * @return {Users_User}
		 */
		$return = Q::event('Users/register', compact(
			'username', 'identifier', 'icon', 'user', 'platform', 'options', 'device'
		), 'after');

		return $user;
	}

	/**
	 * Returns a user in the database that corresponds to the contact info, if any.
	 * @method userFromContactInfo
	 * @static
	 * @param {string} $type Could be one of "email", "mobile", "email_hashed", "mobile_hashed", "facebook", "twitter" or "token"
	 * @param {string} $value The value corresponding to the type. If $type is:
	 *
	 * * "email" - this is one of the user's email addresses
	 * * "mobile" - this is one of the user's mobile numbers
	 * * "email_hashed" - this is the standard hash of the user's email address
	 * * "mobile_hashed" - this is the standard hash of the user's mobile number
	 * * "facebook" - this is the user's id on facebook
	 * * "twitter": - this is the user's id on twitter
	 *
	 * @return {Users_User|null}
	 */
	static function userFromContactInfo($type, $value)
	{
		$ui = Users::identify($type, $value, null, $normalized);
		if (!$ui) {
			return null;
		}
		$user = new Users_User();
		$user->id = $ui->userId;
		if (!$user->retrieve()) {
			return null;
		}
		$user->set('identify', $ui);
		return $user;
	}

	/**
	 * Returns Users_Identifier rows that correspond to the identifier in the database, if any.
	 * @method identify
	 * @static
	 * @param {string|array} $type Could be one of "email", "mobile", "email_hashed", "mobile_hashed", "facebook" or "twitter"
	 *    It could also be an array of ($type => $value) pairs. Then $state should be null.
	 * @param {string} $value The value corresponding to the type. If $type is
	 *
	 * * "email" - this is one of the user's email addresses
	 * * "mobile" - this is one of the user's mobile numbers
	 * * "email_hashed" - this is the standard hash of the user's email address
	 * * "mobile_hashed" - this is the standard hash of the user's mobile number
	 * * "facebook" - this is the user's id on facebook
	 * * "twitter": - this is the user's id on twitter
	 *
	 * @param {string} [$state='verified'] The state of the identifier => userId mapping.
	 *  Could also be 'future' to find identifiers attached to a "future user",
	 *  and can also be null (in which case we find mappings in all states)
	 * @param {&string} [$normalized=null]
	 * @return {Users_Identify}
	 *  The row corresponding to this type and value, otherwise null
	 */
	static function identify($type, $value, $state = 'verified', &$normalized=null)
	{
		$identifiers = array();
		$expected_array = is_array($type);
		$types = is_array($type) ? $type : array($type => $value);
		foreach ($types as $type => $value) {
			switch ($type) {
			case 'email':
				// for efficiency, we check only the hashed version, and expect it to be there
				Q_Valid::email($value, $normalized);
				$ui_type = 'email_hashed';
				$ui_value = Q_Utils::hash($normalized);
				break;
			case 'mobile':
				// for efficiency, we check only the hashed version, and expect it to be there
				Q_Valid::phone($value, $normalized);
				$ui_type = 'mobile_hashed';
				$ui_value = Q_Utils::hash($normalized);
				break;
			default:
				$ui_type = $type;
				$ui_value = $value;
			}
			$supported = array(
				'email_hashed' => true,
				'mobile_hashed' => true,
				'facebook' => true,
				'twitter' => true
			);
			if (empty($supported[$ui_type])) {
				throw new Q_Exception_WrongValue(array('field' => 'type', 'range' => 'a supported type'));
			}
			$identifiers[] = "$ui_type:$ui_value";
		}
		$uis = Users_Identify::select('*')->where(array(
			'identifier' => $identifiers,
			'state' => isset($state) ? $state : array('verified', 'future')
		))->limit(1)->fetchDbRows();
		if ($expected_array) {
			return $uis;
		}
		return !empty($uis) ? reset($uis) : null;
	}

	/**
	 * Returns a user in the database that will correspond to a new user in the future
	 * once they authenticate or follow an invite.
	 * Inserts a new user if one doesn't already exist.
	 *
	 * @method futureUser
	 * @param {string} $type Could be one of "email", "mobile", "email_hashed", "mobile_hashed", "facebook", "twitter" or "none".
	 * @param {string} $value The value corresponding to the type. If $type is:
	 *
	 * * "email" - this is one of the user's email addresses
	 * * "mobile" - this is one of the user's mobile numbers
	 * * "email_hashed" - this is the email, already hashed with Q_Utils::hash()
	 * * "mobile_hashed" - this is the email, already hashed with Q_Utils::hash()
	 * * "facebook" - this is the user's id on facebook
	 * * "twitter" - this is the user's id on twitter
	 * * "none" - the type is ignored, no "identify" rows are inserted into the db, etc.
	 * 
	 * With every type except "none", the user will be 
	 *
	 * NOTE: If the person we are representing here comes and registers the regular way,
	 * and then later adds an email, mobile, or authenticates with a platform,
	 * which happens to match the "future" mapping we inserted in users_identify table, 
	 * then this futureUser will not be converted, since they already registered
	 * a different user. Later on, we may have some sort function to merge users together. 
	 *
	 * @param {&string} [$status=null] The status of the user - 'verified' or 'future'
	 * @return {Users_User}
	 * @throws {Q_Exception_WrongType} If $type is not supported
	 * @throws {Q_Exception_MissingRow} If identity for user exists but user does not exists
	 */
	static function futureUser($type, $value, &$status = null)
	{
		if (!array_key_exists($type, self::$types)) {
			throw new Q_Exception_WrongType(array(
				'field' => 'type', 
				'type' => 'one of the supported types'
			));
		}

		if ($type !== 'none') {
			$ui = Users::identify($type, $value, null);
			if ($ui && !empty($ui->userId)) {
				$user = new Users_User();
				$user->id = $ui->userId;
				if ($user->retrieve()) {
					$status = $ui->state;
					return $user;
				} else {
					$userId = $ui->userId;
					throw new Q_Exception_MissingRow(array(
						'table' => 'user',
						'criteria' => 'that id'
					), 'userId');
				}
			}
		}

		// Make a user row to represent a "future" user and give them an empty username
		$user = new Users_User();
		if ($field = self::$types[$type]) {
			$user->$field = $value;
		}
		$user->signedUpWith = 'none'; // this marks it as a future user for now
		$user->username = "";
		$user->icon = 'future';
		$during = 'future';
		/**
		 * @event Users/insertUser {before}
		 * @param {string} during
		 * @param {Users_User} 'user'
		 */
		Q::event('Users/insertUser', compact('user', 'during'), 'before');
		$user->save(); // sets the user's id
		/**
		 * @event Users/insertUser {after}
		 * @param {string} during
		 * @param {Users_User} user
		 */
		Q::event('Users/insertUser', compact('user', 'during'), 'after');

		if ($type != 'email' and $type != 'mobile') {
			if ($type !== 'none') {
				// Save an identifier => user pair for this future user
				$ui = new Users_Identify();
				$ui->identifier = "$type:$value";
				$ui->state = 'future';
				if (!$ui->retrieve()) {
					$ui->userId = $user->id;
					$ui->save();
				}
				$status = $ui->state;
			} else {
				$status = 'future';
			}
		} else {
			// Find existing identifier or save a new one
			$ui = new Users_Identify();
			$hashed = Q_Utils::hash($value);
			$ui->identifier = $type."_hashed:$hashed";
			$ui->state = 'future';
			if (!$ui->retrieve()) {
				$ui->userId = $user->id;
				$ui->save();
			}
			$status = $ui->state;
		}
		return $user;
	}

	/**
	 * Returns external data about the user
	 * @method external
	 * @param {string} $publisherId The id of the user corresponding to the publisher consuming the external data
	 * @param {string} $userId The id of the user whose external data is going to be consumed
	 * @return {Users_External}
	 */
	static function external($publisherId, $userId)
	{
		$ue = new User_External();
		$ue->publisherId = $publisherId;
		$ue->userId = $userId;
		if (!$ue->retrieve()) {
			$ue->save(); // should create a unique xid
		}
		return $ue;
	}

	/**
	 * Imports an icon and sets $user->icon to the url.
	 * @method importIcon
	 * @static
	 * @param {array} $user The user for whom the icon should be downloaded
	 * @param {array} [$urls=array()] Array of urls
	 * @param {string} [$directory=null] Defaults to APP/files/APP/uploads/Users/USERID/icon/imported
	 * @return {string} the path to the icon directory
	 */
	static function importIcon($user, $urls = array(), $directory = null)
	{
		if (empty($directory)) {
			$app = Q_Config::expect('Q', 'app');
			$directory = APP_FILES_DIR.DS.$app.DS.'uploads'.DS.'Users'
				.DS.Q_Utils::splitId($user->id).DS.'icon'.DS.'imported';
		}
		if (empty($urls)) {
			return $directory;
		}
		Q_Utils::canWriteToPath($directory, false, true);
		$type = Q_Config::get('Users', 'login', 'iconType', 'wavatar');
		$largestSize = 0;
		$largestUrl = null;
		$largestImage = null;
		foreach ($urls as $basename => $url) {
			if (!is_string($url)) continue;
			$filename = $directory.DS.$basename;
			$info = pathinfo($filename);
			$size = $info['filename'];
			if ((string)(int)$size !== $size) continue;
			if ($largestSize < (int)$size) {
				$largestSize = (int)$size;
				$largestUrl = $url;
			}
		}
		if ($largestSize) {
			$largestImage = imagecreatefromstring(file_get_contents($largestUrl));
		}
		foreach ($urls as $basename => $url) {
			if (is_string($url)) {
				$filename = $directory.DS.$basename;
				$info = pathinfo($filename);
				$size = $info['filename'];
				$success = false;
				if ($largestImage and (string)(int)$size === $size) {
					if ($size == $largestSize) {
						$image = $largestImage;
						$success = true;
					} else {
						$image = imagecreatetruecolor($size, $size);
						imagealphablending($image, false);
						$success = imagecopyresampled(
							$image, $largestImage, 
							0, 0, 
							0, 0, 
							$size, $size, 
							$largestSize, $largestSize
						);
					}
				}
				if (!$success) {
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					$data = curl_exec($ch);
					curl_close($ch);
					$image = imagecreatefromstring($data);
				}
				$info = pathinfo($filename);
				switch ($info['extension']) {
					case 'png':
						$func = 'imagepng';
						imagesavealpha($image, true);
						imagealphablending($image, true);
						break;
					case 'jpeg':
					case 'jpeg':
						$func = 'imagejpeg';
						break;
					case 'gif':
						$func = 'imagegif';
						break;
				}
				call_user_func($func, $image, $directory.DS.$info['filename'].'.png');
			} else {
				Q_Image::put(
					$directory.DS.$basename,
					$url['hash'],
					$url['size'],
					$type,
					Q_Config::get('Users', 'login', 'gravatar', false)
				);
			}
		}
		$head = APP_FILES_DIR.DS.$app.DS.'uploads';
		$tail = str_replace(DS, '/', substr($directory, strlen($head)));
		$user->icon = '{{baseUrl}}/uploads'.$tail;
		return $directory;
	}

	/**
	 * Hashes a passphrase
	 * @method hashPassphrase
	 * @static
	 * @param {string} $passphrase the passphrase to hash
	 * @param {string} [$existing_hash=null] must provide when comparing with a passphrase
	 * hash that has been already stored. It contains the salt for the passphrase.
	 * @return {string} the hashed passphrase, or "" if the passphrase was ""
	 */
	static function hashPassphrase ($passphrase, $existing_hash = null)
	{
		if ($passphrase === '') {
			return '';
		}

		$hash_function = Q_Config::get(
			'Users', 'passphrase', 'hashFunction', 'sha1'
		);
		$passphraseHash_iterations = Q_Config::get(
			'Users', 'passphrase', 'hashIterations', 1103
		);
		$salt_length = Q_Config::set('Users', 'passphrase', 'saltLength', 0);

		if ($salt_length > 0) {
			if (empty($existing_hash)) {
				$salt = substr(sha1(uniqid(mt_rand(), true)), 0,
					$salt_length);
			} else {
				$salt = substr($existing_hash, - $salt_length);
			}
		}

		$salt2 = isset($salt) ? '_'.$salt : '';
		$result = $passphrase;

		// custom hash function
		if (!is_callable($hash_function)) {
			throw new Q_Exception_MissingFunction(array(
				'function_name' => $hash_function
			));
		}
		$confounder = $passphrase . $salt2;
		$confounder_len = strlen($confounder);
		for ($i = 0; $i < $passphraseHash_iterations; ++$i) {
			$result = call_user_func(
				$hash_function,
				$result . $confounder[$i % $confounder_len]
			);
		}
		$result .= $salt2;

		return $result;
	}
	
	/**
	 * Get the internal app id and info
	 * @method appId
	 * @static
	 * @param {string} $platform The platform or platform for the app
	 * @param {string} $appId Can be either an internal or external app id
	 * @return {array} Returns array($appId, $appInfo)
	 */
	static function appInfo($platform, $appId)
	{
		$apps = Q_Config::get('Users', 'apps', $platform, array());
		if (isset($apps[$appId])) {
			$appInfo = $apps[$appId];
		} else {
			$id = $appInfo = null;
			foreach ($apps as $k => $v) {
				if ($v['appId'] === $appId) {
					$appInfo = $v;
					$id = $k;
					break;
				}
			}
			$appId = $id;
		}
		return array($appId, $appInfo);
	}

	/**
	 * Gets the facebook object constructed from request and/or cookies
	 * @method facebook
	 * @static
	 * @param {string} [$appId=Q::app()] Can either be an interal appId or a Facebook appId.
	 * @param {boolean} [$longLived=true] Get a long-lived access token, if necessary
	 * @param {boolean} [$setCookie=true] Whether to set fbsr_$appId cookie
	 * @return {Facebook|null} Facebook object
	 */
	static function facebook($appId = null, $longLived = true, $setCookie = true)
	{
		if (!isset($appId)) {
			$appId = Q::app();
		}
		if (isset(self::$facebooks[$appId])) {
			return self::$facebooks[$appId];
		}
		list($appId, $fbInfo) = Users::appInfo('facebook', $appId);
		if (!$appId) {
			return null;
		}
		$fbAppId = (isset($fbInfo['appId']) && isset($fbInfo['secret']))
			? $fbInfo['appId']
			: '';

		try {
			$params = array_merge(array(
				'app_id' => $fbAppId,
				'app_secret' => $fbInfo['secret']
			));
			$facebook = new Facebook\Facebook($params);
			Users::$facebooks[$fbAppId] = $facebook;
			Users::$facebooks[$appId] = $facebook;
		} catch (Exception $e) {
			return null;
		}

		$defaultAccessToken = null;
		if (isset($_POST['signed_request'])) {
			// This means that this is being requested from canvas page or page tab
			$fbsr = $_POST['signed_request'];
		} else {
			// Check the cookies for the signed request
			$fbsr = Q::ifset($_COOKIE, "fbsr_$appId", null);
		}
		if ($fbsr) {
			$sr = new Facebook\SignedRequest($facebook->getApp(), $fbsr);
			$result = array(
				'signedRequest' => $fbsr,
				'expires' => $sr->get('expires'),
				'accessToken' => $sr->get('oauth_token'),
				'userID' => $sr->get('user_id')
			);
		}
		if ($authResponse = Q_Request::special('Users.facebook.authResponse', null)) {
			// Users.js sent along Users.facebook.authResponse in the request
			$result = Q::take($authResponse, array(
				'signedRequest', 'accessToken', 'expires', 'expiresIn', 'userID'
			));
			if (!isset($result['expires']) and isset($result['expiresIn'])) {
				$result['expires'] = time() + $result['expiresIn']; // approximately
			}
			if (isset($result['signedRequest'])) {
				$fbsr = $result['signedRequest'];
			}
		}
		if (isset($result['accessToken'])) {
			$defaultAccessToken = $result['accessToken'];
			if ($longLived and isset($result['expires'])) {
				$accessToken = new Facebook\Authentication\AccessToken(
					$defaultAccessToken, $result['expires']
				);
				if (!$accessToken->isLongLived()) {
					$oa = $facebook->getOAuth2Client();
					$defaultAccessToken = $oa->getLongLivedAccessToken($defaultAccessToken);
				}
			}
		}
		if ($defaultAccessToken) {
			$facebook->setDefaultAccessToken($defaultAccessToken);
		}
		if ($fbsr and $setCookie) {
			Q_Response::setCookie("fbsr_$appId", $fbsr);
		}
		// If $defaultAccessToken was not, set, then
		// we will return a Facebook\Facebook object but
		// it will not have a default access token set, so
		// Facebook API requests will return an error unless
		// you provide your own access token at request time.
		return $facebook;
	}

	/**
	 * Adds a link to someone who is not yet a user
	 * @method addLink
	 * @static
	 * @param {string} $address Could be email address, mobile number, etc.
	 * @param {string} [$type=null] One of 'email', 'mobile', 'email_hashed', 'mobile_hashed', 'facebook', or 'twitter' for now.
	 * If not indicated, the function tries to guess by using Q_Valid functions.
	 * @param {array} [$extraInfo=array()] Associative array of information you have imported
	 * from the address book. Should contain at least the keys:
	 *
	 * * "firstName" => the imported first name
	 * * "lastName" => the imported last name
	 * * "labels" => array of the imported names of the contact groups to add this user to once they sign up
	 *
	 * @return {boolean|integer} Returns true if the link row was created
	 * Or returns a string $userId if user already exists and has verified this address.
	 * @throws {Q_Exception_WrongValue} If $address is not a valid id
	 * @throws {Users_Exception_NotLoggedIn} If user is not logged in
	 */
	static function addLink(
		$address,
		$type = null,
		$extraInfo = array())
	{
		// process the address first
		$address = trim($address);
		$ui_type = $type;
		switch ($type) {
			case 'email':
				if (!Q_Valid::email($address, $normalized)) {
					throw new Q_Exception_WrongValue(
						array('field' => 'address', 'range' => 'email address')
					);
				}
				$ui_type = 'email_hashed';
				$ui_value = 'email_hashed:'.Q_Utils::hash($normalized);
				break;
			case 'email_hashed':
				// Assume that the $address was already hashed in the standard way
				// see Q_Utils::hash
				$ui_value = "email_hashed:$address";
				break;
			case 'mobile':
				if (!isset($options['hashed']) and !Q_Valid::phone($address, $normalized)) {
					throw new Q_Exception_WrongValue(
						array('field' => 'address', 'range' => 'phone number')
					);
				}
				$ui_type = 'mobile_hashed';
				$ui_value = 'mobile_hashed:'.Q_Utils::hash($normalized);
				break;
			case 'mobile_hashed':
				// Assume that the $address was already hashed in the standard way
				// see Q_Utils::hash
				$ui_value = "mobile_hashed:$address";
				break;
			case 'facebook':
				if (!isset($options['hashed']) and !is_numeric($address)) {
					throw new Q_Exception_WrongValue(
						array('field' => 'address', 'range' => 'facebook uid')
					);
				}
				$ui_value = "facebook:$address";
				$normalized = $address;
				break;
			case 'twitter':
				if (!isset($options['hashed']) and !is_numeric($address)) {
					throw new Q_Exception_WrongValue(
						array('field' => 'address', 'range' => 'twitter uid')
					);
				}
				$ui_value = "twitter:$address";
				$normalized = $address;
				break;
			default:
				if (Q_Valid::email($address, $normalized)) {
					$ui_type = 'email_hashed';
					$ui_value = 'email_hashed:'.Q_Utils::hash($normalized);
				} else if (Q_Valid::phone($address, $normalized)) {
					$ui_type = 'mobile_hashed';
					$ui_value = 'mobile_hashed:'.Q_Utils::hash($normalized);
				} else {
					throw new Q_Exception_WrongValue(
						array('field' => 'type', 'range' => 'one of email, mobile, email_hashed, mobile_hashed, facebook, twitter')
					);
				}
				break;
		}

		$user = Users::loggedInUser(true);

		// Check if the contact user already exists, and if so, add a contact instead of a link
		$ui = Users::identify($ui_type, $ui_value);
		if ($ui) {
			// Add a contact instead of a link
			$user->addContact($ui->userId, Q::ifset($extraInfo, 'labels', null));
			return $user->id;
		}

		// Add a link if one isn't already there
		$link = new Users_Link();
		$link->identifier = $ui_value;
		$link->userId = $user->id;
		if ($link->retrieve()) {
			return false;
		}
		$link->extraInfo = Q::json_encode($extraInfo);
		$link->save();
		return true;
	}

	/**
	 * @method links
	 * @static
	 * @param {array} $contact_info An array of key => value pairs, where keys can be:
	 *
	 * * "email" => the user's email address
	 * * "mobile" => the user's mobile number
	 * * "email_hashed" => the standard hash of the user's email address
	 * * "mobile_hashed" => the standard hash of the user's mobile number
	 * * "facebook" => the user's facebook uid
	 * * "twitter" => the user's twitter uid
	 *
	 * @return {array}
	 *  Returns an array of all links to this user's contact info
	 */
	static function links($contact_info)
	{
		$links = array();
		$identifiers = array();
		if (!empty($contact_info['email'])) {
			Q_Valid::email($contact_info['email'], $emailAddress);
			$identifiers[] = "email_hashed:".Q_Utils::hash($emailAddress);
		}
		if (!empty($contact_info['mobile'])) {
			Q_Valid::phone($contact_info['mobile'], $mobileNumber);
			$identifiers[] = "mobile_hashed:".Q_Utils::hash($mobileNumber);
		}
		if (!empty($contact_info['email_hashed'])) {
			$identifiers[] = "email_hashed".$contact_info['email_hashed'];
		}
		if (!empty($contact_info['mobile_hashed'])) {
			$identifiers[] = "mobile_hashed:".$contact_info['mobile_hashed'];
		}
		if (!empty($contact_info['facebook'])) {
			$identifiers[] = "facebook:".$contact_info['facebook'];
		}
		if (!empty($contact_info['twitter'])) {
			$identifiers[] = "twitter:".$contact_info['twitter'];
		}
		return Users_Link::select('*')->where(array(
			'identifier' => $identifiers
		))->fetchDbRows();
	}

	/**
	 * Inserts some Users_Contact rows for the locally registered users
	 * who have added links to this particular contact information.
	 * Removes the links after successfully adding the Users_Contact rows.
	 * @method saveContactsFromLinks
	 * @static
	 * @param {array} $contact_info An array of key => value pairs, where keys can be:
	 *
	 * * "email" => the user's email address
	 * * "mobile" => the user's mobile number
	 * * "email_hashed" => the standard hash of the user's email address
	 * * "mobile_hashed" => the standard hash of the user's mobile number
	 * * "facebook" => the user's facebook uid
	 * * "twitter" => the user's twitter uid
	 *
	 * @param {string} $userId The id of the user who has verified these identifiers
	 */
	static function saveContactsFromLinks()
	{
		/**
		 * @event Users/saveContactsFromLinks {before}
		 */
		Q::event('Users/saveContactsFromLinks', array(), 'before');

		$user = self::loggedInUser();

		$contact_info = array();
		foreach (self::$types as $type => $field) {
			if (!empty($user->$field)) {
				$contact_info[$type] = $user->$field;
			}
		}
		$links = $contact_info
			? Users::links($contact_info)
			: array();

		$contacts = array();
		foreach ($links as $link) {
			$extraInfo = json_decode($link->extraInfo, true);
			$firstName = Q::ifset($extraInfo, 'firstName', '');
			$lastName = Q::ifset($extraInfo, 'lastName', '');
			$fullName = $firstName
				? ($lastName ? "$firstName $lastName" : $firstName)
				: ($lastName ? $lastName : "");
			if (!empty($extraInfo['labels']) and is_array($extraInfo['labels'])) {
				foreach ($extraInfo['labels'] as $label) {
					// Insert the contacts one by one, so if an error occurs
					// we can continue right on inserting the rest.
					$contact = new Users_Contact();
					$contact->userId = $link->userId;
					$contact->contactUserId = $user->id;
					$contact->label = $label;
					$contact->nickname = $fullName;
					$contact->save(true);
					$link->remove(); // we don't need this link anymore

					// TODO: Think about porting this to Node
					// and setting a flag when done.
					// Perhaps we should send a custom message through socket.io
					// which would cause Users.js to add a notice to the interface
				}
			}
		}
		/**
		 * @event Users/saveContactsFromLinks {after}
		 * @param {array} contacts
		 */
		Q::event('Users/saveContactsFromLinks', compact('contacts'), 'after');

		// TODO: Add a handler to this event in the Streams plugin, so that
		// we post this information to a stream on the hub, which will
		// update all its subscribers, who will also run saveContactsFromLinks
		// for their local users.
	}

	/**
	 * Get the email address or mobile number from the request, if it can be deduced.
	 * Note: it should still be tested for validity.
	 * @method requestedIdentifier
	 * @static
	 * @param {&string} [$type=null] The identifier's type will be filled here. Might be "email", "mobile", "facebook" etc.
	 * @return {string|null} The identifier, or null if one wasn't requested
	 */
	static function requestedIdentifier(&$type = null)
	{
		$identifier = null;
		$type = null;
		if (!empty($_REQUEST['identifier'])) {
			$identifier = $_REQUEST['identifier'];
			if (isset($identifier['app']['platform'])) {
				$type = $identifier['app']['platform'];
				$identifier = Q::ifset($identifier, 'identifier', null);
			} else if (Q_Valid::email($identifier, $normalized)) {
				$type = 'email';
			} else if (Q_Valid::phone($identifier, $normalized)) {
				$type = 'mobile';
			}
		}
		if (!empty($_REQUEST['emailAddress'])) {
			$identifier = $_REQUEST['emailAddress'];
			Q_Valid::email($identifier, $normalized);
			$type = 'email';
		}
		if (!empty($_REQUEST['mobileNumber'])) {
			$identifier = $_REQUEST['mobileNumber'];
			Q_Valid::phone($identifier, $normalized);
			$type = 'mobile';
		}
		return isset($normalized) ? $normalized : $identifier;
	}

	static function termsLabel($for = 'register')
	{
		$terms_uri = Q_Config::get('Users', $for, 'terms', 'uri', null);
		$terms_label = Q_Config::get('Users', $for, 'terms', 'label', null);
		$terms_title = Q_Config::get('Users', $for, 'terms', 'title', null);
		if (!$terms_uri or !$terms_title or !$terms_label) {
			return null;
		}
		$terms_link = Q_Html::a(
			Q::interpolate($terms_uri, array('baseUrl' => Q_Request::baseUrl())),
			array('target' => '_blank'),
			$terms_title
		);
		return Q::interpolate($terms_label, array('link' => $terms_link));
	}
	
	/**
	 * Get the url of a user's icon
	 * @param {string} [$icon] The contents of a user row's icon field
	 * @param {string} [$basename=""] The last part after the slash, such as "50.png"
	 * @return {string} The stream's icon url
	 */
	static function iconUrl($icon, $basename = null)
	{
		if (empty($icon)) {
			return null;
		}
		$url = Q::interpolate($icon, array('baseUrl' => Q_Request::baseUrl()));
		$url = Q_Valid::url($url) ? $url : "plugins/Users/img/icons/$url";
		if ($basename and strpos($basename, '.') === false) {
			$basename .= ".png";
		}
		if ($basename) {
			$url .= "/$basename";
		}
		return Q_Html::themedUrl($url);
	}
	
	/**
	 * Checks whether one user can manage contacts of another user
	 * @static
	 * @param {string} $asUserId The user who would be doing the managing
	 * @param {string} $userId The user whose contacts they are
	 * @param {string} $label The label of the contacts that will be managed
	 * @param {boolean} [$throwIfNotAuthorized=false] Throw an exception if not authorized
	 * @param {boolean} [$readOnly=false] Whether we just want to know if the user can view the labels
	 * @return {boolean} Whether a contact with this label is allowed to be managed
	 * @throws {Users_Exception_NotAuthorized}
	 */
	static function canManageContacts(
		$asUserId, 
		$userId, 
		$label, 
		$throwIfNotAuthorized = false,
		$readOnly = false
	) {
		if ($asUserId === false) {
			return true;
		}
		if (!isset($asUserId)) {
			$user = Users::loggedInUser();
			$asUserId = $user ? $user->id : '';
		}
		$authorized = false;
		$result = Q::event(
			"Users/canManageContacts",
			compact('asUserId', 'userId', 'label', 'throwIfNotAuthorized', 'readOnly'),
			'before'
		);
		if ($result) {
			$authorized = $result;
		} else if ($asUserId === $userId) {
			if ($readOnly or substr($label, 0, 6) === 'Users/') {
				$authorized = true;
			}
		}
		if (!$authorized and $throwIfNotAuthorized) {
			throw new Users_Exception_NotAuthorized();
		}
		return $authorized;
	}
	
	/**
	 * Checks whether one user can manage contact labels of another user
	 * @static
	 * @param {string} $asUserId The user who would be doing the managing
	 * @param {string} $userId The user whose contact labels they are
	 * @param {string} $label The label that will be managed
	 * @param {boolean} $throwIfNotAuthorized Throw an exception if not authorized
	 * @param {boolean} $readOnly Whether we just want to know if the user can view the labels
	 * @return {boolean} Whether this label is allowed to be managed
	 * @throws {Users_Exception_NotAuthorized}
	 */
	static function canManageLabels(
		$asUserId, 
		$userId, 
		$label, 
		$throwIfNotAuthorized = false,
		$readOnly = false
	) {
		if ($asUserId === false) {
			return true;
		}
		$authorized = false;
		$result = Q::event(
			"Users/canManageLabels",
			compact('asUserId', 'userId', 'label', 'throwIfNotAuthorized', 'readOnly'),
			'before'
		);
		if ($result) {
			$authorized = $result;
		} else if ($asUserId === $userId) {
			if ($readOnly or substr($label, 0, 6) === 'Users/') {
				$authorized = true;
			}
		}
		if (!$authorized and $throwIfNotAuthorized) {
			throw new Users_Exception_NotAuthorized();
		}
		return $authorized;
	}

	/**
	 * @property $fql_results
	 * @type array
	 * @protected
	 * @default array()
	 */
	protected static $fql_results = array();
	/**
	 * @property $users
	 * @type array
	 * @protected
	 * @default array()
	 */
	protected static $users = array();
	/**
	 * @property $email
	 * @type string
	 * @protected
	 * @default null
	 */
	protected static $email = null; // cached
	/**
	 * @property $types
	 * @type array
	 * @protected
	 */
	/**
	 * Type e-mail
	 * @config $types['email']
	 * @protected
	 * @default 'emailAddressPending'
	 */
	/**
	 * Type mobile
	 * @config $types['mobile']
	 * @protected
	 * @default 'mobileNumberPending'
	 */
	/**
	 * Type facebook
	 * @config $types['facebook']
	 * @protected
	 * @default 'fb_uid'
	 */
	/**
	 * Type twitter
	 * @config $types['twitter']
	 * @protected
	 * @default 'tw_uid'
	 */
	protected static $types = array(
		'none' => null,
		'email_hashed' => null,
		'mobile_hashed' => null,
		'email' => 'emailAddressPending',
		'mobile' => 'mobileNumberPending',
		'facebook' => 'fb_uid',
		'twitter' => 'tw_uid'
	);

	/**
	 * @property $loggedOut
	 * @type boolean
	 */
	public static $loggedOut;
	/**
	 * @property $cache
	 * @type array
	 * @default array()
	 */
	public static $cache = array();

	/* * * */
};

include_once(__DIR__.DS."Facebook".DS."polyfills.php");