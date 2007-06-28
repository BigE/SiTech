<?php
interface SiTech_DB_Backend_Interface
{
	/**
	 * Begin a transaction with the database. Any queries that are executed
	 * until commit() is called will not effect the data. If rollBack() is
	 * called, all changes will be reverted and not effect the database.
	 *
	 * @return bool
	 */
    public function beginTransaction();

    /**
     * Commit changes of a transaction to the database.
     *
     * @return bool
     */
    public function commit();

    /**
     * Delete rows from the database. Returns total number of rows deleted.
     *
     * @param string Table name to delete rows from.
     * @param string Where clause for deletion.
     * @return int
     */
    public function delete($table, $where = '');

    /**
     * Grab the most recent error code from the database. Returns 0 if no previous
     * error exists.
     *
     * @return int
     */
    public function errorCode();

    /**
     * Grab the most recent error information from the database. Returns an
     * array contaning the error number, SQL error number, and error string.
     *
     * @return array
     */
    public function errorInfo();

    /**
     * Execute a query against the database then return the number of rows.
     *
     * @param mixed $sql SiTech_DB_Select object or SQL string.
     * @return int
     */
    public function exec($sql);

    /**
     * Get an attribute.
     *
     * @param int $attr
     * @return mixed
     */
    public function getAttribute($attr);

    /**
     * Get fetch mode that's currently set.
     *
     * @return int
     */
    public function getFetchMode();

    /**
     * Insert data into a table. If array is multidimensional, multiple inserts
     * are assumed.
     *
     * @param string $table Table name to insert data into.
     * @param array $data Array of data to insert.
     * @return int
     */
    public function insert($table, array $data);

    /**
     * Grab the ID of the last row inserted into a table.
     *
     * @param string $name Field name that ID exists in.
     * @return mixed
     */
    public function lastInsertId($name=null);

    /**
     * Prepare SQL for execution in the database.
     *
     * @param mixed $sql SiTech_DB_Select object or SQL string.
     * @param array $args Array of arguments to be interpolated in the SQL.
     * @return SiTech_DB_Statement_Interface
     */
    public function prepare($sql, $args=array());

    /**
     * Execute a query to the database and return the statement object.
     *
     * @param mixed $sql SiTech_DB_Select object or SQL string.
     * @param int $mode Fetch mode for SiTech_DB_Statement.
     * @param midex $arg1
     * @param mixed $arg2
     * @return SiTech_DB_Statement_Interface
     */
    public function query($sql, $mode=null, $arg1=null, $arg2=null);

    /**
     * Format and escape data to the specified type.
     *
     * @param mixed $data Data to format or escape.
     * @param int $mode Type that data is supposed to be.
     * @return mixed
     */
    public function quote($data, $mode=null);

    /**
     * Revert any changes made to the database by the transaction.
     *
     * @return bool
     */
    public function rollBack();

    /**
     * Begin a SELECT statement by returning a SiTech_DB_Select object.
     *
     * @return SiTech_DB_Select_Interface
     */
    public function select();

    /**
     * Set an attribute for the connection.
     *
     * @param int $attr
     * @param mixed $value
     * @return bool
     */
    public function setAttribute($attr, $value);

    /**
     * Set the current fetch mode.
     *
     * @param int $mode Fetch mode to use.
     * @param mixed $arg1 Optional argument for fetch mode.
     * @param mixed $arg2 Optional second argument for fetch mode.
     */
    public function setFetchMode($mode, $arg1=null, $arg2=null);

    /**
     * Enter description here...
     *
     * @param string $table Table name to update rows
     * @param array $set Column/value pairs
     * @param unknown_type $where Where clause for update
     * @return int Number of affected rows
     */
    public function update($table, array $set, $where='');
}
?>
