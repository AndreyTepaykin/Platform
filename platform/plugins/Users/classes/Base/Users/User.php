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
 * @property string $id
 * @property string|Db_Expression $insertedTime
 * @property string|Db_Expression $updatedTime
 * @property string $sessionId
 * @property integer $sessionCount
 * @property integer $fb_uid
 * @property integer $tw_uid
 * @property string $g_uid
 * @property string $y_uid
 * @property string $passphraseHash
 * @property string $emailAddress
 * @property string $mobileNumber
 * @property string $emailAddressPending
 * @property string $mobileNumberPending
 * @property mixed $signedUpWith
 * @property string $username
 * @property string $icon
 * @property string $url
 * @property string $pincodeHash
 */
abstract class Base_Users_User extends Db_Row
{
	/**
	 * @property $id
	 * @type string
	 */
	/**
	 * @property $insertedTime
	 * @type string|Db_Expression
	 */
	/**
	 * @property $updatedTime
	 * @type string|Db_Expression
	 */
	/**
	 * @property $sessionId
	 * @type string
	 */
	/**
	 * @property $sessionCount
	 * @type integer
	 */
	/**
	 * @property $fb_uid
	 * @type integer
	 */
	/**
	 * @property $tw_uid
	 * @type integer
	 */
	/**
	 * @property $g_uid
	 * @type string
	 */
	/**
	 * @property $y_uid
	 * @type string
	 */
	/**
	 * @property $passphraseHash
	 * @type string
	 */
	/**
	 * @property $emailAddress
	 * @type string
	 */
	/**
	 * @property $mobileNumber
	 * @type string
	 */
	/**
	 * @property $emailAddressPending
	 * @type string
	 */
	/**
	 * @property $mobileNumberPending
	 * @type string
	 */
	/**
	 * @property $signedUpWith
	 * @type mixed
	 */
	/**
	 * @property $username
	 * @type string
	 */
	/**
	 * @property $icon
	 * @type string
	 */
	/**
	 * @property $url
	 * @type string
	 */
	/**
	 * @property $pincodeHash
	 * @type string
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
	 * @return {iDb} The database object
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
	 * @param $fields {array} The field values to use in WHERE clauseas as 
	 * an associative array of `column => value` pairs
	 * @param [$alias=null] {string} Table alias
	 * @return {Db_Query_Mysql} The generated query
	 */
	static function select($fields, $alias = null)
	{
		if (!isset($alias)) $alias = '';
		$q = self::db()->select($fields, self::table().' '.$alias);
		$q->className = 'Users_User';
		return $q;
	}

	/**
	 * Create UPDATE query to the class table
	 * @method update
	 * @static
	 * @param [$alias=null] {string} Table alias
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
	 * @param [$table_using=null] {object} If set, adds a USING clause with this table
	 * @param [$alias=null] {string} Table alias
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
	 * @param [$fields=array()] {object} The fields as an associative array of `column => value` pairs
	 * @param [$alias=null] {string} Table alias
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
	 * Inserts multiple records into a single table, preparing the statement only once,
	 * and executes all the queries.
	 * @method insertManyAndExecute
	 * @static
	 * @param {array} [$records=array()] The array of records to insert. 
	 * (The field names for the prepared statement are taken from the first record.)
	 * You cannot use Db_Expression objects here, because the function binds all parameters with PDO.
	 * @param {array} [$options=array()]
	 *   An associative array of options, including:
	 *
	 * * "chunkSize" {integer} The number of rows to insert at a time. defaults to 20.<br/>
	 * * "onDuplicateKeyUpdate" {array} You can put an array of fieldname => value pairs here,
	 * 		which will add an ON DUPLICATE KEY UPDATE clause to the query.
	 *
	 */
	static function insertManyAndExecute($records = array(), $options = array())
	{
		self::db()->insertManyAndExecute(self::table(), $records, $options);
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
		$date = date_parse($value);
		if (!empty($date['errors'])) {
			throw new Exception("DateTime $value in incorrect format being assigned to ".$this->getTable().".insertedTime");
		}
		foreach (array('year', 'month', 'day', 'hour', 'minute', 'second') as $v) {
			$$v = $date[$v];
		}
		$value = sprintf("%04d-%02d-%02d %02d:%02d:%02d", $year, $month, $day, $hour, $minute, $second);
		return array('insertedTime', $value);			
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
		$date = date_parse($value);
		if (!empty($date['errors'])) {
			throw new Exception("DateTime $value in incorrect format being assigned to ".$this->getTable().".updatedTime");
		}
		foreach (array('year', 'month', 'day', 'hour', 'minute', 'second') as $v) {
			$$v = $date[$v];
		}
		$value = sprintf("%04d-%02d-%02d %02d:%02d:%02d", $year, $month, $day, $hour, $minute, $second);
		return array('updatedTime', $value);			
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
		if ($value < -2147483648 or $value > 2147483647)
			throw new Exception("Out-of-range value '$value' being assigned to ".$this->getTable().".sessionCount");
		return array('sessionCount', $value);			
	}

	/**
	 * Method is called before setting the field and verifies if integer value falls within allowed limits
	 * @method beforeSet_fb_uid
	 * @param {integer} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not integer or does not fit in allowed range
	 */
	function beforeSet_fb_uid($value)
	{
		if ($value instanceof Db_Expression) {
			return array('fb_uid', $value);
		}
		if (!is_numeric($value) or floor($value) != $value)
			throw new Exception('Non-integer value being assigned to '.$this->getTable().".fb_uid");
		if ($value < -9.2233720368548E+18 or $value > 9223372036854775807)
			throw new Exception("Out-of-range value '$value' being assigned to ".$this->getTable().".fb_uid");
		return array('fb_uid', $value);			
	}

	/**
	 * Method is called before setting the field and verifies if integer value falls within allowed limits
	 * @method beforeSet_tw_uid
	 * @param {integer} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not integer or does not fit in allowed range
	 */
	function beforeSet_tw_uid($value)
	{
		if ($value instanceof Db_Expression) {
			return array('tw_uid', $value);
		}
		if (!is_numeric($value) or floor($value) != $value)
			throw new Exception('Non-integer value being assigned to '.$this->getTable().".tw_uid");
		if ($value < -9.2233720368548E+18 or $value > 9223372036854775807)
			throw new Exception("Out-of-range value '$value' being assigned to ".$this->getTable().".tw_uid");
		return array('tw_uid', $value);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_g_uid
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_g_uid($value)
	{
		if (!isset($value)) {
			return array('g_uid', $value);
		}
		if ($value instanceof Db_Expression) {
			return array('g_uid', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".g_uid");
		if (strlen($value) > 255)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".g_uid");
		return array('g_uid', $value);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_y_uid
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_y_uid($value)
	{
		if (!isset($value)) {
			return array('y_uid', $value);
		}
		if ($value instanceof Db_Expression) {
			return array('y_uid', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".y_uid");
		if (strlen($value) > 255)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".y_uid");
		return array('y_uid', $value);			
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
		if (strlen($value) > 64)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".passphraseHash");
		return array('passphraseHash', $value);			
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
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_emailAddressPending
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_emailAddressPending($value)
	{
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
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_mobileNumberPending
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_mobileNumberPending($value)
	{
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
	 * Method is called before setting the field and verifies if value belongs to enum values list
	 * @method beforeSet_signedUpWith
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value does not belong to enum values list
	 */
	function beforeSet_signedUpWith($value)
	{
		if ($value instanceof Db_Expression) {
			return array('signedUpWith', $value);
		}
		if (!in_array($value, array('none','email','mobile','facebook','twitter','remote')))
			throw new Exception("Out-of-range value '$value' being assigned to ".$this->getTable().".signedUpWith");
		return array('signedUpWith', $value);			
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
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_icon
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_icon($value)
	{
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
			foreach (array('username','icon') as $name) {
				if (!isset($value[$name])) {
					throw new Exception("the field $table.$name needs a value, because it is NOT NULL, not auto_increment, and lacks a default value.");
				}
			}
		}
		if (!$this->retrieved and !isset($value['insertedTime']))
			$value['insertedTime'] = new Db_Expression('CURRENT_TIMESTAMP');
		//if ($this->retrieved and !isset($value['updatedTime']))
		// convention: we'll have updatedTime = insertedTime if just created.
		$value['updatedTime'] = new Db_Expression('CURRENT_TIMESTAMP');
		return $value;			
	}

	/**
	 * Retrieves field names for class table
	 * @method fieldNames
	 * @static
	 * @param {string} [$table_alias=null] If set, the alieas is added to each field
	 * @param {string} [$field_alias_prefix=null] If set, the method returns associative array of `'prefixed field' => 'field'` pairs
	 * @return {array} An array of field names
	 */
	static function fieldNames($table_alias = null, $field_alias_prefix = null)
	{
		$field_names = array('id', 'insertedTime', 'updatedTime', 'sessionId', 'sessionCount', 'fb_uid', 'tw_uid', 'g_uid', 'y_uid', 'passphraseHash', 'emailAddress', 'mobileNumber', 'emailAddressPending', 'mobileNumberPending', 'signedUpWith', 'username', 'icon', 'url', 'pincodeHash');
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