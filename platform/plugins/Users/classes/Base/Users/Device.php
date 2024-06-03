<?php

/**
 * Autogenerated base class representing device rows
 * in the Users database.
 *
 * Don't change this file, since it can be overwritten.
 * Instead, change the Users_Device.php file.
 *
 * @module Users
 */
/**
 * Base class representing 'Device' rows in the 'Users' database
 * @class Base_Users_Device
 * @extends Db_Row
 *
 * @param {array} [$fields=array()] The fields values to initialize table row as 
 * an associative array of $column => $value pairs
 * @param {string} [$fields.userId] defaults to ""
 * @param {string} [$fields.deviceId] defaults to ""
 * @param {string} [$fields.platform] defaults to ""
 * @param {string} [$fields.version] defaults to null
 * @param {string} [$fields.appId] defaults to null
 * @param {string} [$fields.sessionId] defaults to ""
 * @param {string} [$fields.formFactor] defaults to null
 * @param {string|Db_Expression} [$fields.insertedTime] defaults to new Db_Expression("CURRENT_TIMESTAMP")
 * @param {string|Db_Expression} [$fields.updatedTime] defaults to null
 * @param {string} [$fields.auth] defaults to ""
 * @param {string} [$fields.p256dh] defaults to null
 */
abstract class Base_Users_Device extends Db_Row
{
	/**
	 * @property $userId
	 * @type string
	 * @default ""
	 * 
	 */
	/**
	 * @property $deviceId
	 * @type string
	 * @default ""
	 * The push registration id or URL endpoint on the platform
	 */
	/**
	 * @property $platform
	 * @type string
	 * @default ""
	 * A platform like ios or android
	 */
	/**
	 * @property $version
	 * @type string
	 * @default null
	 * The version of the platform
	 */
	/**
	 * @property $appId
	 * @type string
	 * @default null
	 * An external app id registered with the platform
	 */
	/**
	 * @property $sessionId
	 * @type string
	 * @default ""
	 * 
	 */
	/**
	 * @property $formFactor
	 * @type string
	 * @default null
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
	 * @property $auth
	 * @type string
	 * @default ""
	 * used in standard Web Push
	 */
	/**
	 * @property $p256dh
	 * @type string
	 * @default null
	 * used in standard Web Push
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
			  0 => 'userId',
			  1 => 'deviceId',
			  2 => 'auth',
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
	 * @param {string} [$alias=null] You can optionally provide an alias for the table to be used in queries
 	 * @return {string|Db_Expression} The table name as string optionally without database name if no table sharding
	 * was started or Db_Expression class with prefix and database name templates is table was sharded
	 */
	static function table($with_db_name = true, $alias = null)
	{
		if (class_exists('Q_Config') and Q_Config::get('Db', 'connections', 'Users', 'indexes', 'Device', false)) {
			return new Db_Expression(($with_db_name ? '{{dbname}}.' : '').'{{prefix}}'.'device');
		} else {
			$conn = Db::getConnection('Users');
  			$prefix = empty($conn['prefix']) ? '' : $conn['prefix'];
  			$table_name = $prefix . 'device';
  			if (!$with_db_name)
  				return $table_name;
  			$db = Db::connect('Users');
			$alias = isset($alias) ? ' '.$alias : '';
  			return $db->dbName().'.'.$table_name.$alias;
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
	 * @param {string} [$alias=null] Table alias.
	 * @return {Db_Query_Mysql} The generated query
	 */
	static function select($fields=null, $alias = null)
	{
		if (!isset($fields)) {
			$fieldNames = array();
			$a = isset($alias) ? $alias.'.' : '';
			foreach (self::fieldNames() as $fn) {
				$fieldNames[] = $a .  $fn;
			}
			$fields = implode(',', $fieldNames);
		}
		$alias = isset($alias) ? ' '.$alias : '';
		$q = self::db()->select($fields, self::table(true, $alias));
		$q->className = 'Users_Device';
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
		$alias = isset($alias) ? ' '.$alias : '';
		$q = self::db()->update(self::table(true, $alias));
		$q->className = 'Users_Device';
		return $q;
	}

	/**
	 * Create DELETE query to the class table
	 * @method delete
	 * @static
	 * @param {string} [$table_using=null] If set, adds a USING clause with this table
	 * @param {string} [$alias=null] Table alias
	 * @return {Db_Query_Mysql} The generated query
	 */
	static function delete($table_using = null, $alias = null)
	{
		$alias = isset($alias) ? ' '.$alias : '';
		$q = self::db()->delete(self::table(true, $alias), $table_using);
		$q->className = 'Users_Device';
		return $q;
	}

	/**
	 * Create INSERT query to the class table
	 * @method insert
	 * @static
	 * @param {array} [$fields=array()] The fields as an associative array of column => value pairs
	 * @param {string} [$alias=null] Table alias
	 * @return {Db_Query_Mysql} The generated query
	 */
	static function insert($fields = array(), $alias = null)
	{
		$alias = isset($alias) ? ' '.$alias : '';
		$q = self::db()->insert(self::table(true, $alias), $fields);
		$q->className = 'Users_Device';
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
			array_merge($options, array('className' => 'Users_Device'))
		);
	}
	
	/**
	 * Create raw query with begin clause
	 * You'll have to specify shards yourself when calling execute().
	 * @method begin
	 * @static
	 * @param {string} [$lockType=null] First parameter to pass to query->begin() function
	 * @param {string} [$transactionKey=null] Pass a transactionKey here to "resolve" a previously
	 *  executed that began a transaction with ->begin(). This is to guard against forgetting
	 *  to "resolve" a begin() query with a corresponding commit() or rollback() query
	 *  from code that knows about this transactionKey. Passing a transactionKey that doesn't
	 *  match the latest one on the transaction "stack" also generates an error.
	 *  Passing "*" here matches any transaction key that may have been on the top of the stack.
	 * @return {Db_Query_Mysql} The generated query
	 */
	static function begin($lockType = null, $transactionKey = null)
	{
		$q = self::db()->rawQuery('')->begin($lockType, $transactionKey);
		$q->className = 'Users_Device';
		return $q;
	}
	
	/**
	 * Create raw query with commit clause
	 * You'll have to specify shards yourself when calling execute().
	 * @method commit
	 * @static
	 * @param {string} [$transactionKey=null] Pass a transactionKey here to "resolve" a previously
	 *  executed that began a transaction with ->begin(). This is to guard against forgetting
	 *  to "resolve" a begin() query with a corresponding commit() or rollback() query
	 *  from code that knows about this transactionKey. Passing a transactionKey that doesn't
	 *  match the latest one on the transaction "stack" also generates an error.
	 *  Passing "*" here matches any transaction key that may have been on the top of the stack.
	 * @return {Db_Query_Mysql} The generated query
	 */
	static function commit($transactionKey = null)
	{
		$q = self::db()->rawQuery('')->commit($transactionKey);
		$q->className = 'Users_Device';
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
		$q->className = 'Users_Device';
		return $q;
	}
	
	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_userId
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_userId($value)
	{
		if (!isset($value)) {
			$value='';
		}
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
			return array('userId', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".userId");
		if (strlen($value) > 31)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".userId");
		return array('userId', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the userId field
	 * @return {integer}
	 */
	function maxSize_userId()
	{

		return 31;			
	}

	/**
	 * Returns schema information for userId column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_userId()
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
  3 => NULL,
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_deviceId
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_deviceId($value)
	{
		if (!isset($value)) {
			$value='';
		}
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
			return array('deviceId', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".deviceId");
		if (strlen($value) > 700)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".deviceId");
		return array('deviceId', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the deviceId field
	 * @return {integer}
	 */
	function maxSize_deviceId()
	{

		return 700;			
	}

	/**
	 * Returns schema information for deviceId column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_deviceId()
	{

return array (
  0 => 
  array (
    0 => 'varbinary',
    1 => '700',
    2 => '',
    3 => false,
  ),
  1 => false,
  2 => 'PRI',
  3 => NULL,
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_platform
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_platform($value)
	{
		if (!isset($value)) {
			$value='';
		}
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
			return array('platform', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".platform");
		if (strlen($value) > 31)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".platform");
		return array('platform', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the platform field
	 * @return {integer}
	 */
	function maxSize_platform()
	{

		return 31;			
	}

	/**
	 * Returns schema information for platform column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_platform()
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
	 * @method beforeSet_version
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_version($value)
	{
		if (!isset($value)) {
			return array('version', $value);
		}
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
			return array('version', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".version");
		if (strlen($value) > 45)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".version");
		return array('version', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the version field
	 * @return {integer}
	 */
	function maxSize_version()
	{

		return 45;			
	}

	/**
	 * Returns schema information for version column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_version()
	{

return array (
  0 => 
  array (
    0 => 'varchar',
    1 => '45',
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
	 * @method beforeSet_appId
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_appId($value)
	{
		if (!isset($value)) {
			return array('appId', $value);
		}
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
			return array('appId', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".appId");
		if (strlen($value) > 200)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".appId");
		return array('appId', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the appId field
	 * @return {integer}
	 */
	function maxSize_appId()
	{

		return 200;			
	}

	/**
	 * Returns schema information for appId column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_appId()
	{

return array (
  0 => 
  array (
    0 => 'varchar',
    1 => '200',
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
			$value='';
		}
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
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
  1 => false,
  2 => '',
  3 => NULL,
);			
	}

	/**
	 * Method is called before setting the field and verifies if value belongs to enum values list
	 * @method beforeSet_formFactor
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value does not belong to enum values list
	 */
	function beforeSet_formFactor($value)
	{
		if (!isset($value)) {
			return array('formFactor', $value);
		}
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
			return array('formFactor', $value);
		}
		if (!in_array($value, array('mobile','tablet','desktop')))
			throw new Exception("Out-of-range value '$value' being assigned to ".$this->getTable().".formFactor");
		return array('formFactor', $value);			
	}

	/**
	 * Returns schema information for formFactor column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_formFactor()
	{

return array (
  0 => 
  array (
    0 => 'enum',
    1 => '\'mobile\',\'tablet\',\'desktop\'',
    2 => '',
    3 => false,
  ),
  1 => true,
  2 => '',
  3 => NULL,
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
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
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
    1 => NULL,
    2 => NULL,
    3 => NULL,
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
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
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
    1 => NULL,
    2 => NULL,
    3 => NULL,
  ),
  1 => true,
  2 => '',
  3 => NULL,
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_auth
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_auth($value)
	{
		if (!isset($value)) {
			$value='';
		}
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
			return array('auth', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".auth");
		if (strlen($value) > 31)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".auth");
		return array('auth', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the auth field
	 * @return {integer}
	 */
	function maxSize_auth()
	{

		return 31;			
	}

	/**
	 * Returns schema information for auth column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_auth()
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
  2 => 'PRI',
  3 => '',
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_p256dh
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_p256dh($value)
	{
		if (!isset($value)) {
			return array('p256dh', $value);
		}
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
			return array('p256dh', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".p256dh");
		if (strlen($value) > 1023)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".p256dh");
		return array('p256dh', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the p256dh field
	 * @return {integer}
	 */
	function maxSize_p256dh()
	{

		return 1023;			
	}

	/**
	 * Returns schema information for p256dh column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_p256dh()
	{

return array (
  0 => 
  array (
    0 => 'varchar',
    1 => '1023',
    2 => '',
    3 => false,
  ),
  1 => true,
  2 => '',
  3 => NULL,
);			
	}

	/**
	 * Check if mandatory fields are set and updates 'magic fields' with appropriate values
	 * @method beforeSave
	 * @param {array} $value The array of fields
	 * @return {array}
	 * @throws {Exception} If mandatory field is not set
	 */
	function beforeSave($value)
	{
		if (!$this->retrieved) {
			$table = $this->getTable();
			foreach (array('userId','deviceId') as $name) {
				if (!isset($value[$name])) {
					throw new Exception("the field $table.$name needs a value, because it is NOT NULL, not auto_increment, and lacks a default value.");
				}
			}
		}						
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
		$field_names = array('userId', 'deviceId', 'platform', 'version', 'appId', 'sessionId', 'formFactor', 'insertedTime', 'updatedTime', 'auth', 'p256dh');
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