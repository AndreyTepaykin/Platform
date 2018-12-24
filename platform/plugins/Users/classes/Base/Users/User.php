<?php

/**
 * Autogenerated base class representing user rows
 * in the Users database.
 *
 * Don't change this file, since it can be overwritten.
 * Instead, change the Users_User.php file.
 *
 * @module Users
 */
/**
 * Base class representing 'User' rows in the 'Users' database
 * @class Base_Users_User
 * @extends Db_Row
 *
 * @param {array} [$fields=array()] The fields values to initialize table row as 
 * an associative array of $column => $value pairs
 * @param {string} [$fields.id] defaults to "0"
 * @param {string|Db_Expression} [$fields.insertedTime] defaults to new Db_Expression("CURRENT_TIMESTAMP")
 * @param {string|Db_Expression} [$fields.updatedTime] defaults to null
 * @param {string} [$fields.sessionId] defaults to null
 * @param {integer} [$fields.sessionCount] defaults to 0
 * @param {string} [$fields.passphraseHash] defaults to null
 * @param {string} [$fields.emailAddress] defaults to null
 * @param {string} [$fields.mobileNumber] defaults to null
 * @param {string} [$fields.uids] defaults to "{}"
 * @param {string} [$fields.emailAddressPending] defaults to ""
 * @param {string} [$fields.mobileNumberPending] defaults to ""
 * @param {string} [$fields.signedUpWith] defaults to ""
 * @param {string} [$fields.username] defaults to ""
 * @param {string} [$fields.icon] defaults to ""
 * @param {string} [$fields.url] defaults to null
 * @param {string} [$fields.pincodeHash] defaults to null
 * @param {string} [$fields.preferredLanguage] defaults to "en"
 */
abstract class Base_Users_User extends Db_Row
{
	/**
	 * @property $id
	 * @type string
	 * @default "0"
	 * 
	 */
	/**
	 * @property $insertedTime
	 * @type string|Db_Expression
	 * @default new Db_Expression("CURRENT_TIMESTAMP")
	 * 
	 */
	/**
	 * @property $updatedTime
	 * @type string|Db_Expression
	 * @default null
	 * 
	 */
	/**
	 * @property $sessionId
	 * @type string
	 * @default null
	 * The session id from the most recent authenticated request from this user.
	 */
	/**
	 * @property $sessionCount
	 * @type integer
	 * @default 0
	 * 
	 */
	/**
	 * @property $passphraseHash
	 * @type string
	 * @default null
	 * 
	 */
	/**
	 * @property $emailAddress
	 * @type string
	 * @default null
	 * 
	 */
	/**
	 * @property $mobileNumber
	 * @type string
	 * @default null
	 * 
	 */
	/**
	 * @property $uids
	 * @type string
	 * @default "{}"
	 * JSON of {platformName: [uid1, ...]}
	 */
	/**
	 * @property $emailAddressPending
	 * @type string
	 * @default ""
	 * 
	 */
	/**
	 * @property $mobileNumberPending
	 * @type string
	 * @default ""
	 * 
	 */
	/**
	 * @property $signedUpWith
	 * @type string
	 * @default ""
	 * A platform like ios or android, or "none", "mobile", "email"
	 */
	/**
	 * @property $username
	 * @type string
	 * @default ""
	 * 
	 */
	/**
	 * @property $icon
	 * @type string
	 * @default ""
	 * relative path to user's icon folder, containing 48.png, 32.png and 16.png
	 */
	/**
	 * @property $url
	 * @type string
	 * @default null
	 * the url of this user's fm server
	 */
	/**
	 * @property $pincodeHash
	 * @type string
	 * @default null
	 * a smaller security code for when user is already logged in
	 */
	/**
	 * @property $preferredLanguage
	 * @type string
	 * @default "en"
	 * 
	 */
	/**
	 * The setUp() method is called the first time
	 * an object of this class is constructed.
	 * @method setUp
	 */
	function setUp()
	{
		$this->setDb(self::db());
		$this->setTable(self::table());
		$this->setPrimaryKey(
			array (
			  0 => 'id',
			)
		);
	}

	/**
	 * Connects to database
	 * @method db
	 * @static
	 * @return {Db_Interface} The database object
	 */
	static function db()
	{
		return Db::connect('Users');
	}

	/**
	 * Retrieve the table name to use in SQL statement
	 * @method table
	 * @static
	 * @param {boolean} [$with_db_name=true] Indicates wheather table name should contain the database name
 	 * @return {string|Db_Expression} The table name as string optionally without database name if no table sharding
	 * was started or Db_Expression class with prefix and database name templates is table was sharded
	 */
	static function table($with_db_name = true)
	{
		if (Q_Config::get('Db', 'connections', 'Users', 'indexes', 'User', false)) {
			return new Db_Expression(($with_db_name ? '{$dbname}.' : '').'{$prefix}'.'user');
		} else {
			$conn = Db::getConnection('Users');
  			$prefix = empty($conn['prefix']) ? '' : $conn['prefix'];
  			$table_name = $prefix . 'user';
  			if (!$with_db_name)
  				return $table_name;
  			$db = Db::connect('Users');
  			return $db->dbName().'.'.$table_name;
		}
	}
	/**
	 * The connection name for the class
	 * @method connectionName
	 * @static
	 * @return {string} The name of the connection
	 */
	static function connectionName()
	{
		return 'Users';
	}

	/**
	 * Create SELECT query to the class table
	 * @method select
	 * @static
	 * @param {string|array} [$fields=null] The fields as strings, or array of alias=>field.
	 *   The default is to return all fields of the table.
	 * @param {string|array} [$alias=null] The tables as strings, or array of alias=>table.
	 * @return {Db_Query_Mysql} The generated query
	 */
	static function select($fields=null, $alias = null)
	{
		if (!isset($fields)) {
			$fieldNames = array();
			foreach (self::fieldNames() as $fn) {
				$fieldNames[] = $fn;
			}
			$fields = implode(',', $fieldNames);
		}
		if (!isset($alias)) $alias = '';
		$q = self::db()->select($fields, self::table().' '.$alias);
		$q->className = 'Users_User';
		return $q;
	}

	/**
	 * Create UPDATE query to the class table
	 * @method update
	 * @static
	 * @param {string} [$alias=null] Table alias
	 * @return {Db_Query_Mysql} The generated query
	 */
	static function update($alias = null)
	{
		if (!isset($alias)) $alias = '';
		$q = self::db()->update(self::table().' '.$alias);
		$q->className = 'Users_User';
		return $q;
	}

	/**
	 * Create DELETE query to the class table
	 * @method delete
	 * @static
	 * @param {object} [$table_using=null] If set, adds a USING clause with this table
	 * @param {string} [$alias=null] Table alias
	 * @return {Db_Query_Mysql} The generated query
	 */
	static function delete($table_using = null, $alias = null)
	{
		if (!isset($alias)) $alias = '';
		$q = self::db()->delete(self::table().' '.$alias, $table_using);
		$q->className = 'Users_User';
		return $q;
	}

	/**
	 * Create INSERT query to the class table
	 * @method insert
	 * @static
	 * @param {object} [$fields=array()] The fields as an associative array of column => value pairs
	 * @param {string} [$alias=null] Table alias
	 * @return {Db_Query_Mysql} The generated query
	 */
	static function insert($fields = array(), $alias = null)
	{
		if (!isset($alias)) $alias = '';
		$q = self::db()->insert(self::table().' '.$alias, $fields);
		$q->className = 'Users_User';
		return $q;
	}
	
	/**
	 * Inserts multiple rows into a single table, preparing the statement only once,
	 * and executes all the queries.
	 * @method insertManyAndExecute
	 * @static
	 * @param {array} [$rows=array()] The array of rows to insert. 
	 * (The field names for the prepared statement are taken from the first row.)
	 * You cannot use Db_Expression objects here, because the function binds all parameters with PDO.
	 * @param {array} [$options=array()]
	 *   An associative array of options, including:
	 *
	 * * "chunkSize" {integer} The number of rows to insert at a time. defaults to 20.<br>
	 * * "onDuplicateKeyUpdate" {array} You can put an array of fieldname => value pairs here,
	 * 		which will add an ON DUPLICATE KEY UPDATE clause to the query.
	 *
	 */
	static function insertManyAndExecute($rows = array(), $options = array())
	{
		self::db()->insertManyAndExecute(
			self::table(), $rows,
			array_merge($options, array('className' => 'Users_User'))
		);
	}
	
	/**
	 * Create raw query with begin clause
	 * You'll have to specify shards yourself when calling execute().
	 * @method begin
	 * @static
	 * @param {string} [$lockType=null] First parameter to pass to query->begin() function
	 * @return {Db_Query_Mysql} The generated query
	 */
	static function begin($lockType = null)
	{
		$q = self::db()->rawQuery('')->begin($lockType);
		$q->className = 'Users_User';
		return $q;
	}
	
	/**
	 * Create raw query with commit clause
	 * You'll have to specify shards yourself when calling execute().
	 * @method commit
	 * @static
	 * @return {Db_Query_Mysql} The generated query
	 */
	static function commit()
	{
		$q = self::db()->rawQuery('')->commit();
		$q->className = 'Users_User';
		return $q;
	}
	
	/**
	 * Create raw query with rollback clause
	 * @method rollback
	 * @static
	 * @param {array} $criteria Can be used to target the rollback to some shards.
	 *  Otherwise you'll have to specify shards yourself when calling execute().
	 * @return {Db_Query_Mysql} The generated query
	 */
	static function rollback()
	{
		$q = self::db()->rawQuery('')->rollback();
		$q->className = 'Users_User';
		return $q;
	}
	
	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_id
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_id($value)
	{
		if (!isset($value)) {
			$value='';
		}
		if ($value instanceof Db_Expression) {
			return array('id', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".id");
		if (strlen($value) > 31)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".id");
		return array('id', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the id field
	 * @return {integer}
	 */
	function maxSize_id()
	{

		return 31;			
	}

	/**
	 * Returns schema information for id column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_id()
	{

return array (
  0 => 
  array (
    0 => 'varbinary',
    1 => '31',
    2 => '',
    3 => false,
  ),
  1 => false,
  2 => 'PRI',
  3 => '0',
);			
	}

	/**
	 * Method is called before setting the field and normalize the DateTime string
	 * @method beforeSet_insertedTime
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value does not represent valid DateTime
	 */
	function beforeSet_insertedTime($value)
	{
		if ($value instanceof Db_Expression) {
			return array('insertedTime', $value);
		}
		if ($value instanceof DateTime) {
			$value = $value->getTimestamp();
		}
		if (is_numeric($value)) {
			$newDateTime = new DateTime();
			$datetime = $newDateTime->setTimestamp($value);
		} else {
			$datetime = new DateTime($value);
		}
		$value = $datetime->format("Y-m-d H:i:s");
		return array('insertedTime', $value);			
	}

	/**
	 * Returns schema information for insertedTime column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_insertedTime()
	{

return array (
  0 => 
  array (
    0 => 'timestamp',
    1 => '31',
    2 => '',
    3 => false,
  ),
  1 => false,
  2 => '',
  3 => 'CURRENT_TIMESTAMP',
);			
	}

	/**
	 * Method is called before setting the field and normalize the DateTime string
	 * @method beforeSet_updatedTime
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value does not represent valid DateTime
	 */
	function beforeSet_updatedTime($value)
	{
		if (!isset($value)) {
			return array('updatedTime', $value);
		}
		if ($value instanceof Db_Expression) {
			return array('updatedTime', $value);
		}
		if ($value instanceof DateTime) {
			$value = $value->getTimestamp();
		}
		if (is_numeric($value)) {
			$newDateTime = new DateTime();
			$datetime = $newDateTime->setTimestamp($value);
		} else {
			$datetime = new DateTime($value);
		}
		$value = $datetime->format("Y-m-d H:i:s");
		return array('updatedTime', $value);			
	}

	/**
	 * Returns schema information for updatedTime column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_updatedTime()
	{

return array (
  0 => 
  array (
    0 => 'timestamp',
    1 => '31',
    2 => '',
    3 => false,
  ),
  1 => true,
  2 => '',
  3 => NULL,
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_sessionId
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_sessionId($value)
	{
		if (!isset($value)) {
			return array('sessionId', $value);
		}
		if ($value instanceof Db_Expression) {
			return array('sessionId', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".sessionId");
		if (strlen($value) > 255)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".sessionId");
		return array('sessionId', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the sessionId field
	 * @return {integer}
	 */
	function maxSize_sessionId()
	{

		return 255;			
	}

	/**
	 * Returns schema information for sessionId column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_sessionId()
	{

return array (
  0 => 
  array (
    0 => 'varbinary',
    1 => '255',
    2 => '',
    3 => false,
  ),
  1 => true,
  2 => '',
  3 => NULL,
);			
	}

	/**
	 * Method is called before setting the field and verifies if integer value falls within allowed limits
	 * @method beforeSet_sessionCount
	 * @param {integer} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not integer or does not fit in allowed range
	 */
	function beforeSet_sessionCount($value)
	{
		if ($value instanceof Db_Expression) {
			return array('sessionCount', $value);
		}
		if (!is_numeric($value) or floor($value) != $value)
			throw new Exception('Non-integer value being assigned to '.$this->getTable().".sessionCount");
		$value = intval($value);
		if ($value < -2147483648 or $value > 2147483647) {
			$json = json_encode($value);
			throw new Exception("Out-of-range value $json being assigned to ".$this->getTable().".sessionCount");
		}
		return array('sessionCount', $value);			
	}

	/**
	 * @method maxSize_sessionCount
	 * Returns the maximum integer that can be assigned to the sessionCount field
	 * @return {integer}
	 */
	function maxSize_sessionCount()
	{

		return 2147483647;			
	}

	/**
	 * Returns schema information for sessionCount column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_sessionCount()
	{

return array (
  0 => 
  array (
    0 => 'int',
    1 => '11',
    2 => '',
    3 => false,
  ),
  1 => false,
  2 => '',
  3 => '0',
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_passphraseHash
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_passphraseHash($value)
	{
		if (!isset($value)) {
			return array('passphraseHash', $value);
		}
		if ($value instanceof Db_Expression) {
			return array('passphraseHash', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".passphraseHash");
		if (strlen($value) > 255)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".passphraseHash");
		return array('passphraseHash', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the passphraseHash field
	 * @return {integer}
	 */
	function maxSize_passphraseHash()
	{

		return 255;			
	}

	/**
	 * Returns schema information for passphraseHash column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_passphraseHash()
	{

return array (
  0 => 
  array (
    0 => 'varchar',
    1 => '255',
    2 => '',
    3 => false,
  ),
  1 => true,
  2 => '',
  3 => NULL,
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_emailAddress
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_emailAddress($value)
	{
		if (!isset($value)) {
			return array('emailAddress', $value);
		}
		if ($value instanceof Db_Expression) {
			return array('emailAddress', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".emailAddress");
		if (strlen($value) > 255)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".emailAddress");
		return array('emailAddress', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the emailAddress field
	 * @return {integer}
	 */
	function maxSize_emailAddress()
	{

		return 255;			
	}

	/**
	 * Returns schema information for emailAddress column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_emailAddress()
	{

return array (
  0 => 
  array (
    0 => 'varbinary',
    1 => '255',
    2 => '',
    3 => false,
  ),
  1 => true,
  2 => '',
  3 => NULL,
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_mobileNumber
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_mobileNumber($value)
	{
		if (!isset($value)) {
			return array('mobileNumber', $value);
		}
		if ($value instanceof Db_Expression) {
			return array('mobileNumber', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".mobileNumber");
		if (strlen($value) > 255)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".mobileNumber");
		return array('mobileNumber', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the mobileNumber field
	 * @return {integer}
	 */
	function maxSize_mobileNumber()
	{

		return 255;			
	}

	/**
	 * Returns schema information for mobileNumber column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_mobileNumber()
	{

return array (
  0 => 
  array (
    0 => 'varbinary',
    1 => '255',
    2 => '',
    3 => false,
  ),
  1 => true,
  2 => '',
  3 => NULL,
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_uids
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_uids($value)
	{
		if (!isset($value)) {
			$value='';
		}
		if ($value instanceof Db_Expression) {
			return array('uids', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".uids");
		if (strlen($value) > 1023)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".uids");
		return array('uids', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the uids field
	 * @return {integer}
	 */
	function maxSize_uids()
	{

		return 1023;			
	}

	/**
	 * Returns schema information for uids column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_uids()
	{

return array (
  0 => 
  array (
    0 => 'varchar',
    1 => '1023',
    2 => '',
    3 => false,
  ),
  1 => false,
  2 => '',
  3 => '{}',
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_emailAddressPending
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_emailAddressPending($value)
	{
		if (!isset($value)) {
			$value='';
		}
		if ($value instanceof Db_Expression) {
			return array('emailAddressPending', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".emailAddressPending");
		if (strlen($value) > 255)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".emailAddressPending");
		return array('emailAddressPending', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the emailAddressPending field
	 * @return {integer}
	 */
	function maxSize_emailAddressPending()
	{

		return 255;			
	}

	/**
	 * Returns schema information for emailAddressPending column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_emailAddressPending()
	{

return array (
  0 => 
  array (
    0 => 'varbinary',
    1 => '255',
    2 => '',
    3 => false,
  ),
  1 => false,
  2 => '',
  3 => '',
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_mobileNumberPending
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_mobileNumberPending($value)
	{
		if (!isset($value)) {
			$value='';
		}
		if ($value instanceof Db_Expression) {
			return array('mobileNumberPending', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".mobileNumberPending");
		if (strlen($value) > 255)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".mobileNumberPending");
		return array('mobileNumberPending', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the mobileNumberPending field
	 * @return {integer}
	 */
	function maxSize_mobileNumberPending()
	{

		return 255;			
	}

	/**
	 * Returns schema information for mobileNumberPending column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_mobileNumberPending()
	{

return array (
  0 => 
  array (
    0 => 'varbinary',
    1 => '255',
    2 => '',
    3 => false,
  ),
  1 => false,
  2 => '',
  3 => '',
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_signedUpWith
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_signedUpWith($value)
	{
		if (!isset($value)) {
			$value='';
		}
		if ($value instanceof Db_Expression) {
			return array('signedUpWith', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".signedUpWith");
		if (strlen($value) > 31)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".signedUpWith");
		return array('signedUpWith', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the signedUpWith field
	 * @return {integer}
	 */
	function maxSize_signedUpWith()
	{

		return 31;			
	}

	/**
	 * Returns schema information for signedUpWith column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_signedUpWith()
	{

return array (
  0 => 
  array (
    0 => 'varchar',
    1 => '31',
    2 => '',
    3 => false,
  ),
  1 => false,
  2 => '',
  3 => NULL,
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_username
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_username($value)
	{
		if (!isset($value)) {
			$value='';
		}
		if ($value instanceof Db_Expression) {
			return array('username', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".username");
		if (strlen($value) > 63)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".username");
		return array('username', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the username field
	 * @return {integer}
	 */
	function maxSize_username()
	{

		return 63;			
	}

	/**
	 * Returns schema information for username column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_username()
	{

return array (
  0 => 
  array (
    0 => 'varchar',
    1 => '63',
    2 => '',
    3 => false,
  ),
  1 => false,
  2 => '',
  3 => NULL,
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_icon
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_icon($value)
	{
		if (!isset($value)) {
			$value='';
		}
		if ($value instanceof Db_Expression) {
			return array('icon', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".icon");
		if (strlen($value) > 255)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".icon");
		return array('icon', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the icon field
	 * @return {integer}
	 */
	function maxSize_icon()
	{

		return 255;			
	}

	/**
	 * Returns schema information for icon column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_icon()
	{

return array (
  0 => 
  array (
    0 => 'varbinary',
    1 => '255',
    2 => '',
    3 => false,
  ),
  1 => false,
  2 => '',
  3 => NULL,
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_url
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_url($value)
	{
		if (!isset($value)) {
			return array('url', $value);
		}
		if ($value instanceof Db_Expression) {
			return array('url', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".url");
		if (strlen($value) > 255)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".url");
		return array('url', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the url field
	 * @return {integer}
	 */
	function maxSize_url()
	{

		return 255;			
	}

	/**
	 * Returns schema information for url column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_url()
	{

return array (
  0 => 
  array (
    0 => 'varbinary',
    1 => '255',
    2 => '',
    3 => false,
  ),
  1 => true,
  2 => '',
  3 => NULL,
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_pincodeHash
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_pincodeHash($value)
	{
		if (!isset($value)) {
			return array('pincodeHash', $value);
		}
		if ($value instanceof Db_Expression) {
			return array('pincodeHash', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".pincodeHash");
		if (strlen($value) > 255)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".pincodeHash");
		return array('pincodeHash', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the pincodeHash field
	 * @return {integer}
	 */
	function maxSize_pincodeHash()
	{

		return 255;			
	}

	/**
	 * Returns schema information for pincodeHash column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_pincodeHash()
	{

return array (
  0 => 
  array (
    0 => 'varbinary',
    1 => '255',
    2 => '',
    3 => false,
  ),
  1 => true,
  2 => '',
  3 => NULL,
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_preferredLanguage
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_preferredLanguage($value)
	{
		if (!isset($value)) {
			return array('preferredLanguage', $value);
		}
		if ($value instanceof Db_Expression) {
			return array('preferredLanguage', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".preferredLanguage");
		if (strlen($value) > 3)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".preferredLanguage");
		return array('preferredLanguage', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the preferredLanguage field
	 * @return {integer}
	 */
	function maxSize_preferredLanguage()
	{

		return 3;			
	}

	/**
	 * Returns schema information for preferredLanguage column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_preferredLanguage()
	{

return array (
  0 => 
  array (
    0 => 'varchar',
    1 => '3',
    2 => '',
    3 => false,
  ),
  1 => true,
  2 => '',
  3 => 'en',
);			
	}

	function beforeSave($value)
	{
						
		// convention: we'll have updatedTime = insertedTime if just created.
		$this->updatedTime = $value['updatedTime'] = new Db_Expression('CURRENT_TIMESTAMP');
		return $value;			
	}

	/**
	 * Retrieves field names for class table
	 * @method fieldNames
	 * @static
	 * @param {string} [$table_alias=null] If set, the alieas is added to each field
	 * @param {string} [$field_alias_prefix=null] If set, the method returns associative array of ('prefixed field' => 'field') pairs
	 * @return {array} An array of field names
	 */
	static function fieldNames($table_alias = null, $field_alias_prefix = null)
	{
		$field_names = array('id', 'insertedTime', 'updatedTime', 'sessionId', 'sessionCount', 'passphraseHash', 'emailAddress', 'mobileNumber', 'uids', 'emailAddressPending', 'mobileNumberPending', 'signedUpWith', 'username', 'icon', 'url', 'pincodeHash', 'preferredLanguage');
		$result = $field_names;
		if (!empty($table_alias)) {
			$temp = array();
			foreach ($result as $field_name)
				$temp[] = $table_alias . '.' . $field_name;
			$result = $temp;
		} 
		if (!empty($field_alias_prefix)) {
			$temp = array();
			reset($field_names);
			foreach ($result as $field_name) {
				$temp[$field_alias_prefix . current($field_names)] = $field_name;
				next($field_names);
			}
			$result = $temp;
		}
		return $result;			
	}
};