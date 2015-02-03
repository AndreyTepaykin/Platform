<?php

/**
 * Autogenerated base class representing mobile rows
 * in the Users database.
 *
 * Don't change this file, since it can be overwritten.
 * Instead, change the Users_Mobile.php file.
 *
 * @module Users
 */
/**
 * Base class representing 'Mobile' rows in the 'Users' database
 * @class Base_Users_Mobile
 * @extends Db_Row
 *
 * @property string $number
 * @property string|Db_Expression $insertedTime
 * @property string|Db_Expression $updatedTime
 * @property string $userId
 * @property string $extension
 * @property mixed $carrier
 * @property mixed $state
 * @property mixed $capabilities
 * @property string $activationCode
 * @property string|Db_Expression $activationCodeExpires
 * @property string $authCode
 */
abstract class Base_Users_Mobile extends Db_Row
{
	/**
	 * @property $number
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
	 * @property $userId
	 * @type string
	 */
	/**
	 * @property $extension
	 * @type string
	 */
	/**
	 * @property $carrier
	 * @type mixed
	 */
	/**
	 * @property $state
	 * @type mixed
	 */
	/**
	 * @property $capabilities
	 * @type mixed
	 */
	/**
	 * @property $activationCode
	 * @type string
	 */
	/**
	 * @property $activationCodeExpires
	 * @type string|Db_Expression
	 */
	/**
	 * @property $authCode
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
			  0 => 'number',
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
		if (Q_Config::get('Db', 'connections', 'Users', 'indexes', 'Mobile', false)) {
			return new Db_Expression(($with_db_name ? '{$dbname}.' : '').'{$prefix}'.'mobile');
		} else {
			$conn = Db::getConnection('Users');
  			$prefix = empty($conn['prefix']) ? '' : $conn['prefix'];
  			$table_name = $prefix . 'mobile';
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
		$q->className = 'Users_Mobile';
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
		$q->className = 'Users_Mobile';
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
		$q->className = 'Users_Mobile';
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
		$q->className = 'Users_Mobile';
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
	 * @method beforeSet_number
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_number($value)
	{
		if ($value instanceof Db_Expression) {
			return array('number', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".number");
		if (strlen($value) > 255)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".number");
		return array('number', $value);			
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
	 * @method beforeSet_userId
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_userId($value)
	{
		if ($value instanceof Db_Expression) {
			return array('userId', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".userId");
		if (strlen($value) > 31)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".userId");
		return array('userId', $value);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_extension
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_extension($value)
	{
		if ($value instanceof Db_Expression) {
			return array('extension', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".extension");
		if (strlen($value) > 7)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".extension");
		return array('extension', $value);			
	}

	/**
	 * Method is called before setting the field and verifies if value belongs to enum values list
	 * @method beforeSet_carrier
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value does not belong to enum values list
	 */
	function beforeSet_carrier($value)
	{
		if ($value instanceof Db_Expression) {
			return array('carrier', $value);
		}
		if (!in_array($value, array('unverified','att','cingular','sprint','tmobile','verizon')))
			throw new Exception("Out-of-range value '$value' being assigned to ".$this->getTable().".carrier");
		return array('carrier', $value);			
	}

	/**
	 * Method is called before setting the field and verifies if value belongs to enum values list
	 * @method beforeSet_state
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value does not belong to enum values list
	 */
	function beforeSet_state($value)
	{
		if ($value instanceof Db_Expression) {
			return array('state', $value);
		}
		if (!in_array($value, array('unverified','active','suspended','unsubscribed')))
			throw new Exception("Out-of-range value '$value' being assigned to ".$this->getTable().".state");
		return array('state', $value);			
	}

	/**
	 * Method is called before setting the field and verifies if value belongs to enum values list
	 * @method beforeSet_capabilities
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value does not belong to enum values list
	 */
	function beforeSet_capabilities($value)
	{
		if ($value instanceof Db_Expression) {
			return array('capabilities', $value);
		}
		if (!in_array($value, array('unverified','sms','wap','internet','sms_and_wap','sms_and_internet')))
			throw new Exception("Out-of-range value '$value' being assigned to ".$this->getTable().".capabilities");
		return array('capabilities', $value);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_activationCode
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_activationCode($value)
	{
		if ($value instanceof Db_Expression) {
			return array('activationCode', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".activationCode");
		if (strlen($value) > 255)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".activationCode");
		return array('activationCode', $value);			
	}

	/**
	 * Method is called before setting the field and normalize the DateTime string
	 * @method beforeSet_activationCodeExpires
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value does not represent valid DateTime
	 */
	function beforeSet_activationCodeExpires($value)
	{
		if ($value instanceof Db_Expression) {
			return array('activationCodeExpires', $value);
		}
		$date = date_parse($value);
		if (!empty($date['errors'])) {
			throw new Exception("DateTime $value in incorrect format being assigned to ".$this->getTable().".activationCodeExpires");
		}
		foreach (array('year', 'month', 'day', 'hour', 'minute', 'second') as $v) {
			$$v = $date[$v];
		}
		$value = sprintf("%04d-%02d-%02d %02d:%02d:%02d", $year, $month, $day, $hour, $minute, $second);
		return array('activationCodeExpires', $value);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_authCode
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_authCode($value)
	{
		if ($value instanceof Db_Expression) {
			return array('authCode', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".authCode");
		if (strlen($value) > 255)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".authCode");
		return array('authCode', $value);			
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
			foreach (array('number','activationCode','authCode') as $name) {
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
		$field_names = array('number', 'insertedTime', 'updatedTime', 'userId', 'extension', 'carrier', 'state', 'capabilities', 'activationCode', 'activationCodeExpires', 'authCode');
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