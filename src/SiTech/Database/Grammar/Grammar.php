<?php
namespace SiTech\Database\Grammar
{
	/**
	 * This is the base grammar class that all database grammars will extend.
	 *
	 * This class provides the basic neccisary grammar functions that all
	 * grammar classes will need to either use or override.
	 *
	 * @package SiTech\Database\Grammar
	 */
	abstract class Grammar
	{
		protected $keywordIdentifier = '"';
		protected $tablePrefix = '';

		/**
		 * Get the currently set table prefix.
		 *
		 * @return string
		 */
		public function getTablePrefix()
		{
			return $this->tablePrefix;
		}

		/**
		 * Set the table prefix you would like to use.
		 *
		 * @param $prefix
		 * @return Grammar
		 */
		public function setTablePrefix($prefix)
		{
			$this->tablePrefix = $prefix;
			return $this;
		}

		/**
		 * Wrap a value in keyword identifiers.
		 *
		 * Determine what value we have and how to wrap it in the grammar's
		 * default keyword identifiers. This will also prefix tables with the
		 * $tablePrefix of this class.
		 *
		 * @param string $value
		 * @return string
		 */
		public function wrap($value)
		{
			// If the table is aliased, we need to wrap both the table and the alias
			if (strpos(strtolower($value), ' as ')) {
				$parts = explode(' ', $value);
				return $this->wrap($this->prefixTable($parts[0])).' '.$parts[1].' '.$this->wrap($parts[2]);
			}

			$out = [];
			$parts = explode('.', $value);

			foreach ($parts as $k => $part) {
				// If we have a table.column value, we need to add the prefix to
				// the table value.
				if ($k === 0 && sizeof($parts) > 1) {
					$part = $this->prefixTable($part);
				}

				$out[] = $this->wrapValue($part);
			}

			return implode('.', $out);
		}

		/**
		 * Add a prefix to the table.
		 *
		 * @param string $table
		 * @return string
		 * @see $tablePrefix
		 */
		protected function prefixTable($table)
		{
			return $this->tablePrefix.$table;
		}

		/**
		 * Wrap a string with the grammar's default keyword identifiers.
		 *
		 * If the value is * then we simply just return the * since that should
		 * not be wrapped in keyword identifiers.
		 *
		 * @param string $value
		 * @return string
		 */
		protected function wrapValue($value)
		{
			if ($value === '*') {
				return $value;
			}

			$value = str_replace($this->keywordIdentifier, str_repeat($this->keywordIdentifier, 2), $value);
			return $this->keywordIdentifier.$value.$this->keywordIdentifier;
		}
	}
}