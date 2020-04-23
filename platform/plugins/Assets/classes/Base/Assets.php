<?php

/**
 * Autogenerated base class for the Assets model.
 * 
 * Don't change this file, since it can be overwritten.
 * Instead, change the Assets.php file.
 *
 * @module Assets
 */
/**
 * Base class for the Assets model
 * @class Base_Assets
 */
abstract class Base_Assets
{
	/**
	 * The list of model classes
	 * @property $table_classnames
	 * @type array
	 */
	static $table_classnames = array (
  0 => 'Assets_Badge',
  1 => 'Assets_Charge',
  2 => 'Assets_Connected',
  3 => 'Assets_Customer',
  4 => 'Assets_Earned',
  5 => 'Assets_Leader',
);

	/**
     * This method calls Db.connect() using information stored in the configuration.
     * If this has already been called, then the same db object is returned.
	 * @method db
	 * @return {Db_Interface} The database object
	 */
	static function db()
	{
		return Db::connect('Assets');
	}

	/**
	 * The connection name for the class
	 * @method connectionName
	 * @return {string} The name of the connection
	 */
	static function connectionName()
	{
		return 'Assets';
	}
};