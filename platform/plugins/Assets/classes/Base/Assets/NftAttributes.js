/**
 * Autogenerated base class representing nft_attributes rows
 * in the Assets database.
 *
 * Don't change this file, since it can be overwritten.
 * Instead, change the Assets/NftAttributes.js file.
 *
 * @module Assets
 */

var Q = require('Q');
var Db = Q.require('Db');
var Assets = Q.require('Assets');
var Row = Q.require('Db/Row');

/**
 * Base class representing 'NftAttributes' rows in the 'Assets' database
 * @namespace Base.Assets
 * @class NftAttributes
 * @extends Db.Row
 * @constructor
 * @param {Object} [fields={}] The fields values to initialize table row as 
 * an associative array of {column: value} pairs
 * @param {String} [fields.publisherId] defaults to ""
 * @param {String} [fields.display_type] defaults to ""
 * @param {String} [fields.trait_type] defaults to ""
 * @param {String} [fields.value] defaults to ""
 * @param {String|Db.Expression} [fields.insertedTime] defaults to new Db.Expression("CURRENT_TIMESTAMP")
 * @param {String|Db.Expression} [fields.updatedTime] defaults to null
 */
function Base (fields) {
	Base.constructors.apply(this, arguments);
}

Q.mixin(Base, Row);

/**
 * @property publisherId
 * @type String
 * @default ""
 * 
 */
/**
 * @property display_type
 * @type String
 * @default ""
 * 
 */
/**
 * @property trait_type
 * @type String
 * @default ""
 * 
 */
/**
 * @property value
 * @type String
 * @default ""
 * 
 */
/**
 * @property insertedTime
 * @type String|Db.Expression
 * @default new Db.Expression("CURRENT_TIMESTAMP")
 * 
 */
/**
 * @property updatedTime
 * @type String|Db.Expression
 * @default null
 * 
 */

/**
 * This method calls Db.connect() using information stored in the configuration.
 * If this has already been called, then the same db object is returned.
 * @method db
 * @return {Db} The database connection
 */
Base.db = function () {
	return Assets.db();
};

/**
 * Retrieve the table name to use in SQL statements
 * @method table
 * @param {boolean} [withoutDbName=false] Indicates wheather table name should contain the database name
 * @return {String|Db.Expression} The table name as string optionally without database name if no table sharding was started
 * or Db.Expression object with prefix and database name templates is table was sharded
 */
Base.table = function (withoutDbName) {
	if (Q.Config.get(['Db', 'connections', 'Assets', 'indexes', 'NftAttributes'], false)) {
		return new Db.Expression((withoutDbName ? '' : '{{dbname}}.')+'{{prefix}}nft_attributes');
	} else {
		var conn = Db.getConnection('Assets');
		var prefix = conn.prefix || '';
		var tableName = prefix + 'nft_attributes';
		var dbname = Base.table.dbname;
		if (!dbname) {
			var dsn = Db.parseDsnString(conn['dsn']);
			dbname = Base.table.dbname = dsn.dbname;
		}
		return withoutDbName ? tableName : dbname + '.' + tableName;
	}
};

/**
 * The connection name for the class
 * @method connectionName
 * @return {String} The name of the connection
 */
Base.connectionName = function() {
	return 'Assets';
};

/**
 * Create SELECT query to the class table
 * @method SELECT
 * @param {String|Object} [fields=null] The fields as strings, or object of {alias:field} pairs.
 *   The default is to return all fields of the table.
 * @param {String|Object} [alias=null] The tables as strings, or object of {alias:table} pairs.
 * @return {Db.Query.Mysql} The generated query
 */
Base.SELECT = function(fields, alias) {
	if (!fields) {
		fields = Base.fieldNames().map(function (fn) {
			return fn;
		}).join(',');
	}
	var q = Base.db().SELECT(fields, Base.table()+(alias ? ' '+alias : ''));
	q.className = 'Assets_NftAttributes';
	return q;
};

/**
 * Create UPDATE query to the class table. Use Db.Query.Mysql.set() method to define SET clause
 * @method UPDATE
 * @param {String} [alias=null] Table alias
 * @return {Db.Query.Mysql} The generated query
 */
Base.UPDATE = function(alias) {
	var q = Base.db().UPDATE(Base.table()+(alias ? ' '+alias : ''));
	q.className = 'Assets_NftAttributes';
	return q;
};

/**
 * Create DELETE query to the class table
 * @method DELETE
 * @param {Object}[table_using=null] If set, adds a USING clause with this table
 * @param {String} [alias=null] Table alias
 * @return {Db.Query.Mysql} The generated query
 */
Base.DELETE = function(table_using, alias) {
	var q = Base.db().DELETE(Base.table()+(alias ? ' '+alias : ''), table_using);
	q.className = 'Assets_NftAttributes';
	return q;
};

/**
 * Create INSERT query to the class table
 * @method INSERT
 * @param {Object} [fields={}] The fields as an associative array of {column: value} pairs
 * @param {String} [alias=null] Table alias
 * @return {Db.Query.Mysql} The generated query
 */
Base.INSERT = function(fields, alias) {
	var q = Base.db().INSERT(Base.table()+(alias ? ' '+alias : ''), fields || {});
	q.className = 'Assets_NftAttributes';
	return q;
};

/**
 * Create raw query with BEGIN clause.
 * You'll have to specify shards yourself when calling execute().
 * @method BEGIN
 * @param {string} [$lockType] First parameter to pass to query.begin() function
 * @return {Db.Query.Mysql} The generated query
 */
Base.BEGIN = function($lockType) {
	var q = Base.db().rawQuery('').begin($lockType);
	q.className = 'Assets_NftAttributes';
	return q;
};

/**
 * Create raw query with COMMIT clause
 * You'll have to specify shards yourself when calling execute().
 * @method COMMIT
 * @return {Db.Query.Mysql} The generated query
 */
Base.COMMIT = function() {
	var q = Base.db().rawQuery('').commit();
	q.className = 'Assets_NftAttributes';
	return q;
};

/**
 * Create raw query with ROLLBACK clause
 * @method ROLLBACK
 * @param {Object} criteria can be used to target the query to some shards.
 *   Otherwise you'll have to specify shards yourself when calling execute().
 * @return {Db.Query.Mysql} The generated query
 */
Base.ROLLBACK = function(criteria) {
	var q = Base.db().rawQuery('').rollback(crieria);
	q.className = 'Assets_NftAttributes';
	return q;
};

/**
 * The name of the class
 * @property className
 * @type string
 */
Base.prototype.className = "Assets_NftAttributes";

// Instance methods

/**
 * Create INSERT query to the class table
 * @method INSERT
 * @param {object} [fields={}] The fields as an associative array of {column: value} pairs
 * @param {string} [alias=null] Table alias
 * @return {Db.Query.Mysql} The generated query
 */
Base.prototype.setUp = function() {
	// does nothing for now
};

/**
 * Create INSERT query to the class table
 * @method INSERT
 * @param {object} [fields={}] The fields as an associative array of {column: value} pairs
 * @param {string} [alias=null] Table alias
 * @return {Db.Query.Mysql} The generated query
 */
Base.prototype.db = function () {
	return Base.db();
};

/**
 * Retrieve the table name to use in SQL statements
 * @method table
 * @param {boolean} [withoutDbName=false] Indicates wheather table name should contain the database name
 * @return {String|Db.Expression} The table name as string optionally without database name if no table sharding was started
 * or Db.Expression object with prefix and database name templates is table was sharded
 */
Base.prototype.table = function () {
	return Base.table();
};

/**
 * Retrieves primary key fields names for class table
 * @method primaryKey
 * @return {string[]} An array of field names
 */
Base.prototype.primaryKey = function () {
	return [
		"publisherId",
		"display_type",
		"trait_type",
		"value"
	];
};

/**
 * Retrieves field names for class table
 * @method fieldNames
 * @return {array} An array of field names
 */
Base.prototype.fieldNames = function () {
	return Base.fieldNames();
};

/**
 * Retrieves field names for class table
 * @method fieldNames
 * @static
 * @return {array} An array of field names
 */
Base.fieldNames = function () {
	return [
		"publisherId",
		"display_type",
		"trait_type",
		"value",
		"insertedTime",
		"updatedTime"
	];
};

/**
 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
 * Optionally accept numeric value which is converted to string
 * @method beforeSet_publisherId
 * @param {string} value
 * @return {string} The value
 * @throws {Error} An exception is thrown if 'value' is not string or is exceedingly long
 */
Base.prototype.beforeSet_publisherId = function (value) {
		if (value == null) {
			value='';
		}
		if (value instanceof Db.Expression) return value;
		if (typeof value !== "string" && typeof value !== "number")
			throw new Error('Must pass a String to '+this.table()+".publisherId");
		if (typeof value === "string" && value.length > 8)
			throw new Error('Exceedingly long value being assigned to '+this.table()+".publisherId");
		return value;
};

	/**
	 * Returns the maximum string length that can be assigned to the publisherId field
	 * @return {integer}
	 */
Base.prototype.maxSize_publisherId = function () {

		return 8;
};

	/**
	 * Returns schema information for publisherId column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
Base.column_publisherId = function () {

return [["varchar","8","",false],false,"PRI",null];
};

/**
 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
 * Optionally accept numeric value which is converted to string
 * @method beforeSet_display_type
 * @param {string} value
 * @return {string} The value
 * @throws {Error} An exception is thrown if 'value' is not string or is exceedingly long
 */
Base.prototype.beforeSet_display_type = function (value) {
		if (value == null) {
			value='';
		}
		if (value instanceof Db.Expression) return value;
		if (typeof value !== "string" && typeof value !== "number")
			throw new Error('Must pass a String to '+this.table()+".display_type");
		if (typeof value === "string" && value.length > 50)
			throw new Error('Exceedingly long value being assigned to '+this.table()+".display_type");
		return value;
};

	/**
	 * Returns the maximum string length that can be assigned to the display_type field
	 * @return {integer}
	 */
Base.prototype.maxSize_display_type = function () {

		return 50;
};

	/**
	 * Returns schema information for display_type column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
Base.column_display_type = function () {

return [["varchar","50","",false],false,"PRI",null];
};

/**
 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
 * Optionally accept numeric value which is converted to string
 * @method beforeSet_trait_type
 * @param {string} value
 * @return {string} The value
 * @throws {Error} An exception is thrown if 'value' is not string or is exceedingly long
 */
Base.prototype.beforeSet_trait_type = function (value) {
		if (value == null) {
			value='';
		}
		if (value instanceof Db.Expression) return value;
		if (typeof value !== "string" && typeof value !== "number")
			throw new Error('Must pass a String to '+this.table()+".trait_type");
		if (typeof value === "string" && value.length > 100)
			throw new Error('Exceedingly long value being assigned to '+this.table()+".trait_type");
		return value;
};

	/**
	 * Returns the maximum string length that can be assigned to the trait_type field
	 * @return {integer}
	 */
Base.prototype.maxSize_trait_type = function () {

		return 100;
};

	/**
	 * Returns schema information for trait_type column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
Base.column_trait_type = function () {

return [["varchar","100","",false],false,"PRI",null];
};

/**
 * Method is called before setting the field and verifies if value is string of length within acceptable limit.
 * Optionally accept numeric value which is converted to string
 * @method beforeSet_value
 * @param {string} value
 * @return {string} The value
 * @throws {Error} An exception is thrown if 'value' is not string or is exceedingly long
 */
Base.prototype.beforeSet_value = function (value) {
		if (value == null) {
			value='';
		}
		if (value instanceof Db.Expression) return value;
		if (typeof value !== "string" && typeof value !== "number")
			throw new Error('Must pass a String to '+this.table()+".value");
		if (typeof value === "string" && value.length > 100)
			throw new Error('Exceedingly long value being assigned to '+this.table()+".value");
		return value;
};

	/**
	 * Returns the maximum string length that can be assigned to the value field
	 * @return {integer}
	 */
Base.prototype.maxSize_value = function () {

		return 100;
};

	/**
	 * Returns schema information for value column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
Base.column_value = function () {

return [["varchar","100","",false],false,"PRI",null];
};

/**
 * Method is called before setting the field
 * @method beforeSet_insertedTime
 * @param {String} value
 * @return {Date|Db.Expression} If 'value' is not Db.Expression the current date is returned
 */
Base.prototype.beforeSet_insertedTime = function (value) {
		if (value instanceof Db.Expression) return value;
		if (typeof value !== 'object' && !isNaN(value)) {
			value = parseInt(value);
			value = new Date(value < 10000000000 ? value * 1000 : value);
		}
		value = (value instanceof Date) ? Base.db().toDateTime(value) : value;
		return value;
};

	/**
	 * Returns schema information for insertedTime column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
Base.column_insertedTime = function () {

return [["timestamp","100","",false],false,"","CURRENT_TIMESTAMP"];
};

/**
 * Method is called before setting the field
 * @method beforeSet_updatedTime
 * @param {String} value
 * @return {Date|Db.Expression} If 'value' is not Db.Expression the current date is returned
 */
Base.prototype.beforeSet_updatedTime = function (value) {
		if (value == undefined) return value;
		if (value instanceof Db.Expression) return value;
		if (typeof value !== 'object' && !isNaN(value)) {
			value = parseInt(value);
			value = new Date(value < 10000000000 ? value * 1000 : value);
		}
		value = (value instanceof Date) ? Base.db().toDateTime(value) : value;
		return value;
};

	/**
	 * Returns schema information for updatedTime column
	 * @return {array} [[typeName, displayRange, modifiers, unsigned], isNull, key, default]
	 */
Base.column_updatedTime = function () {

return [["timestamp","100","",false],true,"",null];
};

/**
 * Check if mandatory fields are set and updates 'magic fields' with appropriate values
 * @method beforeSave
 * @param {Object} value The object of fields
 * @param {Function} callback Call this callback if you return null
 * @return {Object|null} Return the fields, modified if necessary. If you return null, then you should call the callback(err, modifiedFields)
 * @throws {Error} If e.g. mandatory field is not set or a bad values are supplied
 */
Base.prototype.beforeSave = function (value) {
	var fields = ['publisherId','display_type','trait_type','value'], i;
	if (!this._retrieved) {
		var table = this.table();
		for (i=0; i<fields.length; i++) {
			if (this.fields[fields[i]] === undefined) {
				throw new Error("the field "+table+"."+fields[i]+" needs a value, because it is NOT NULL, not auto_increment, and lacks a default value.");
			}
		}
	}
	// convention: we'll have updatedTime = insertedTime if just created.
	this['updatedTime'] = value['updatedTime'] = new Db.Expression('CURRENT_TIMESTAMP');
	return value;
};

module.exports = Base;