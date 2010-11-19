<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This is made to write queries out instead of executing them.
 *
 * @author Eric Gach <eric@php-oop.net>
 */
class SiTech_DB_Statement_FileWriter extends SiTech_DB_Statement
{
	/**
	 *
	 * @var array
	 */
	protected $_boundParams = array();

	/**
	 * PDO holder for the connection. We use this to call PDO::quote().
	 *
	 * @var SiTech_DB
	 */
	protected $_conn;

	protected $_output;

	/**
	 * Constructor. When you tell PDO to use this statement, you must tell it
	 * the connection we're using for calling quote() and the file we want to
	 * write to. It will open the file for appending.
	 *
	 * @param SiTech_DB $conn
	 * @param string $output Path to file for output. Default is stdout.
	 */
	protected function __construct(SiTech_DB $conn, $output = 'php://stdout')
	{
		$this->_conn = $conn;
		if (!($this->_output = fopen('a', $output))) {
			throw new SiTech_Exception('Failed to open output file \''.$output.'\' for writing');
		}
	}

	public function bindParam($parameter, &$variable, $data_type = null, $length = null, $driver_options = null) {
		$this->_boundParams[$parameter] = array(
			'length'  => $length,
			'options' => $driver_options,
			'type'    => $data_type,
			'value'   => &$variable
		);
		parent::bindParam($parameter, $variable, $data_type, $length, $driver_options);
	}

	public function bindValue($parameter, $value, $data_type = null) {
		$this->_boundParams[$parameter] = array(
			'type'  => $data_type,
			'value' => $value
		);
		parent::bindValue($parameter, $value, $data_type);
	}

	/**
	 * Instead of executing the query on the database connection, we want to
	 * write the SQL to a file.
	 *
	 * @param array $input_parameters
	 */
	public function execute(array $input_parameters = array())
	{
		$sql = $this->queryString;

		if (!empty($input_parameters)) {
			foreach ($input_parameters as $key => $val) {
				if (is_int($key)) {
					$sql = preg_replace('#\?#', $this->_conn->quote($val), $sql, 1);
				} else {
					$sql = str_replace($key, $this->_conn->quote($val), $sql);
				}
			}
		}

		if (!empty($this->_boundParams)) {
			foreach ($this->_boundParams as $key => $array) {
				$value = $array['value'];

				if (!is_null($param['type'])) {
					$value = self::cast($value, $param['type']);
				}

				if ($param['maxlen'] && $param['maxlen'] != self::NO_MAX_LENGTH) {
					$value = self::truncate($value, $param['maxlen']);
				}

				if (is_int($key)) {
					if (!is_null($value)) {
						$sql = preg_replace('#\?#', $this->connection->quote($value), $sql, 1);
					} else {
						$sql = preg_replace('#\?#', 'NULL', $sql, 1);
					}
				} else {
					if (!is_null($value)) {
						$sql = str_replace($key, $this->connection->quote($value), $sql);
					} else {
						$sql = str_replace($key, 'NULL', $sql);
					}
				}
			}

			fwrite($this->_output, $sql);
		}
	}
}
