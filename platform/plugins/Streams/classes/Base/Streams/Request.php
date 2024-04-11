<?php

/**
 * Autogenerated base class representing request rows
 * in the Streams database.
 *
 * Don't change this file, since it can be overwritten.
 * Instead, change the Streams_Request.php file.
 *
 * @module Streams
 */
/**
 * Base class representing 'Request' rows in the 'Streams' database
 * @class Base_Streams_Request
 * @extends Db_Row
 *
 * @param {array} [$fields=array()] The fields values to initialize table row as 
 * an associative array of $column => $value pairs
 * @param {string} [$fields.publisherId] defaults to ""
 * @param {string} [$fields.streamName] defaults to ""
 * @param {string} [$fields.userId] defaults to ""
 * @param {integer} [$fields.readLevel] defaults to 0
 * @param {integer} [$fields.writeLevel] defaults to 0
 * @param {integer} [$fields.adminLevel] defaults to 0
 * @param {string} [$fields.permissions] defaults to null
 * @param {string} [$fields.state] defaults to "pending"
 * @param {string} [$fields.actions] defaults to null
 * @param {string|Db_Expression} [$fields.insertedTime] defaults to new Db_Expression("CURRENT_TIMESTAMP")
 * @param {string|Db_Expression} [$fields.expireTime] defaults to null
 */
abstract class Base_Streams_Request extends Db_Row
{
	/**
	 * @property $publisherId
	 * @type string
	 * @default ""
	 * id of user who publishes the stream
	 */
	/**
	 * @property $streamName
	 * @type string
	 * @default ""
	 * local to shard of publisherId
	 */
	/**
	 * @property $userId
	 * @type string
	 * @default ""
	 * id of user who is requesting access to the stream
	 */
	/**
	 * @property $readLevel
	 * @type integer
	 * @default 0
	 * <0=ignored, 0='none', 10='see', 20='content', 30='participants', 40='messages'
	 */
	/**
	 * @property $writeLevel
	 * @type integer
	 * @default 0
	 * <0=ignored, 0='none', 10=join, 13=vote, 15=suggest, 18=contribute, 20=post, 23=relate, 30=edit, 40=close
	 */
	/**
	 * @property $adminLevel
	 * @type integer
	 * @default 0
	 * <0=ignored, 0='none', 10='publish', 20='invite', 30='manage', 40='own'
	 */
	/**
	 * @property $permissions
	 * @type string
	 * @default null
	 * JSON array of permission names
	 */
	/**
	 * @property $state
	 * @type string
	 * @default "pending"
	 * the state of the request
	 */
	/**
	 * @property $actions
	 * @type string
	 * @default null
	 * JSON array of strings naming actions to take after request is granted, e.g. "Streams/subscribe"
	 */
	/**
	 * @property $insertedTime
	 * @type string|Db_Expression
	 * @default new Db_Expression("CURRENT_TIMESTAMP")
	 * saved on shard of publisherId
	 */
	/**
	 * @property $expireTime
	 * @type string|Db_Expression
	 * @default null
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
			  0 => 'publisherId',
			  1 => 'streamName',
			  2 => 'userId',
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
		return Db::connect('Streams');
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
		if (Q_Config::get('Db', 'connections', 'Streams', 'indexes', 'Request', false)) {
			return new Db_Expression(($with_db_name ? '{{dbname}}.' : '').'{{prefix}}'.'request');
		} else {
			$conn = Db::getConnection('Streams');
  			$prefix = empty($conn['prefix']) ? '' : $conn['prefix'];
  			$table_name = $prefix . 'request';
  			if (!$with_db_name)
  				return $table_name;
  			$db = Db::connect('Streams');
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
		return 'Streams';
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
		$q->className = 'Streams_Request';
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
		$q->className = 'Streams_Request';
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
		$q->className = 'Streams_Request';
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
		$q->className = 'Streams_Request';
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
			array_merge($options, array('className' => 'Streams_Request'))
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
		$q->className = 'Streams_Request';
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
		$q->className = 'Streams_Request';
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
		$q->className = 'Streams_Request';
		return $q;
	}
	
	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_publisherId
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_publisherId($value)
	{
		if (!isset($value)) {
			$value='';
		}
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
			return array('publisherId', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".publisherId");
		if (strlen($value) > 31)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".publisherId");
		return array('publisherId', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the publisherId field
	 * @return {integer}
	 */
	function maxSize_publisherId()
	{

		return 31;			
	}

	/**
	 * Returns schema information for publisherId column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_publisherId()
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
	 * @method beforeSet_streamName
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_streamName($value)
	{
		if (!isset($value)) {
			$value='';
		}
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
			return array('streamName', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".streamName");
		if (strlen($value) > 255)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".streamName");
		return array('streamName', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the streamName field
	 * @return {integer}
	 */
	function maxSize_streamName()
	{

		return 255;			
	}

	/**
	 * Returns schema information for streamName column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_streamName()
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
  2 => 'PRI',
  3 => NULL,
);			
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
	 * Method is called before setting the field and verifies if integer value falls within allowed limits
	 * @method beforeSet_readLevel
	 * @param {integer} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not integer or does not fit in allowed range
	 */
	function beforeSet_readLevel($value)
	{
		if (!isset($value)) {
			return array('readLevel', $value);
		}
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
			return array('readLevel', $value);
		}
		if (!is_numeric($value) or floor($value) != $value)
			throw new Exception('Non-integer value being assigned to '.$this->getTable().".readLevel");
		$value = intval($value);
		if ($value < -2147483648 or $value > 2147483647) {
			$json = json_encode($value);
			throw new Exception("Out-of-range value $json being assigned to ".$this->getTable().".readLevel");
		}
		return array('readLevel', $value);			
	}

	/**
	 * @method maxSize_readLevel
	 * Returns the maximum integer that can be assigned to the readLevel field
	 * @return {integer}
	 */
	function maxSize_readLevel()
	{

		return 2147483647;			
	}

	/**
	 * Returns schema information for readLevel column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_readLevel()
	{

return array (
  0 => 
  array (
    0 => 'int',
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
	 * Method is called before setting the field and verifies if integer value falls within allowed limits
	 * @method beforeSet_writeLevel
	 * @param {integer} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not integer or does not fit in allowed range
	 */
	function beforeSet_writeLevel($value)
	{
		if (!isset($value)) {
			return array('writeLevel', $value);
		}
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
			return array('writeLevel', $value);
		}
		if (!is_numeric($value) or floor($value) != $value)
			throw new Exception('Non-integer value being assigned to '.$this->getTable().".writeLevel");
		$value = intval($value);
		if ($value < -2147483648 or $value > 2147483647) {
			$json = json_encode($value);
			throw new Exception("Out-of-range value $json being assigned to ".$this->getTable().".writeLevel");
		}
		return array('writeLevel', $value);			
	}

	/**
	 * @method maxSize_writeLevel
	 * Returns the maximum integer that can be assigned to the writeLevel field
	 * @return {integer}
	 */
	function maxSize_writeLevel()
	{

		return 2147483647;			
	}

	/**
	 * Returns schema information for writeLevel column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_writeLevel()
	{

return array (
  0 => 
  array (
    0 => 'int',
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
	 * Method is called before setting the field and verifies if integer value falls within allowed limits
	 * @method beforeSet_adminLevel
	 * @param {integer} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not integer or does not fit in allowed range
	 */
	function beforeSet_adminLevel($value)
	{
		if (!isset($value)) {
			return array('adminLevel', $value);
		}
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
			return array('adminLevel', $value);
		}
		if (!is_numeric($value) or floor($value) != $value)
			throw new Exception('Non-integer value being assigned to '.$this->getTable().".adminLevel");
		$value = intval($value);
		if ($value < -2147483648 or $value > 2147483647) {
			$json = json_encode($value);
			throw new Exception("Out-of-range value $json being assigned to ".$this->getTable().".adminLevel");
		}
		return array('adminLevel', $value);			
	}

	/**
	 * @method maxSize_adminLevel
	 * Returns the maximum integer that can be assigned to the adminLevel field
	 * @return {integer}
	 */
	function maxSize_adminLevel()
	{

		return 2147483647;			
	}

	/**
	 * Returns schema information for adminLevel column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_adminLevel()
	{

return array (
  0 => 
  array (
    0 => 'int',
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
	 * @method beforeSet_permissions
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_permissions($value)
	{
		if (!isset($value)) {
			return array('permissions', $value);
		}
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
			return array('permissions', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".permissions");
		if (strlen($value) > 255)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".permissions");
		return array('permissions', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the permissions field
	 * @return {integer}
	 */
	function maxSize_permissions()
	{

		return 255;			
	}

	/**
	 * Returns schema information for permissions column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_permissions()
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
	 * Method is called before setting the field and verifies if value belongs to enum values list
	 * @method beforeSet_state
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value does not belong to enum values list
	 */
	function beforeSet_state($value)
	{
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
			return array('state', $value);
		}
		if (!in_array($value, array('pending','granted','rejected','forwarded','expired')))
			throw new Exception("Out-of-range value '$value' being assigned to ".$this->getTable().".state");
		return array('state', $value);			
	}

	/**
	 * Returns schema information for state column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_state()
	{

return array (
  0 => 
  array (
    0 => 'enum',
    1 => '\'pending\',\'granted\',\'rejected\',\'forwarded\',\'expired\'',
    2 => '',
    3 => false,
  ),
  1 => false,
  2 => '',
  3 => 'pending',
);			
	}

	/**
	 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
	 * Optionally accept numeric value which is converted to string
	 * @method beforeSet_actions
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value is not string or is exceedingly long
	 */
	function beforeSet_actions($value)
	{
		if (!isset($value)) {
			return array('actions', $value);
		}
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
			return array('actions', $value);
		}
		if (!is_string($value) and !is_numeric($value))
			throw new Exception('Must pass a string to '.$this->getTable().".actions");
		if (strlen($value) > 255)
			throw new Exception('Exceedingly long value being assigned to '.$this->getTable().".actions");
		return array('actions', $value);			
	}

	/**
	 * Returns the maximum string length that can be assigned to the actions field
	 * @return {integer}
	 */
	function maxSize_actions()
	{

		return 255;			
	}

	/**
	 * Returns schema information for actions column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_actions()
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
	 * @method beforeSet_expireTime
	 * @param {string} $value
	 * @return {array} An array of field name and value
	 * @throws {Exception} An exception is thrown if $value does not represent valid DateTime
	 */
	function beforeSet_expireTime($value)
	{
		if (!isset($value)) {
			return array('expireTime', $value);
		}
		if ($value instanceof Db_Expression
               or $value instanceof Db_Range) {
			return array('expireTime', $value);
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
		return array('expireTime', $value);			
	}

	/**
	 * Returns schema information for expireTime column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
	static function column_expireTime()
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
			foreach (array('publisherId','streamName','userId') as $name) {
				if (!isset($value[$name])) {
					throw new Exception("the field $table.$name needs a value, because it is NOT NULL, not auto_increment, and lacks a default value.");
				}
			}
		}
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
		$field_names = array('publisherId', 'streamName', 'userId', 'readLevel', 'writeLevel', 'adminLevel', 'permissions', 'state', 'actions', 'insertedTime', 'expireTime');
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