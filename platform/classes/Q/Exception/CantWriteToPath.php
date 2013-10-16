<?php

/**
 * @module Q
 */
class Q_Exception_CantWriteToPath extends Q_Exception
{
	/**
	 * @class Q_Exception_CantWriteToPath
	 * @constructor
	 * @extends Q_Exception
	 * @param {string} $keys
	 */
};

Q_Exception::add('Q_Exception_CantWriteToPath', "Not authorized to write files here.");
