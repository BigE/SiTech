<?php
/**
 * SiTech/Session.php
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @copyright SiTech Group (c) 2008-2009
 * @filesource
 * @package SiTech_Console
 * @subpackage SiTech_Console_GetOpts
 * @todo Create documentation for all methods, variables and constants.
 * @version $Id$
 */

/**
 * This class is a replacement for the getopts() function. It adds a lot of
 * functionality to make things more automatic and use less code. This class
 * will automatically parse -h or --help and -v or --version to output the
 * proper information.
 *
 * @package SiTech_Console_GetOpts
 */
class SiTech_Console_GetOpts
{
	const TYPE_STRING = 1;

	const TYPE_INT = 2;

	const TYPE_FLOAT = 3;
	
	protected $long = array();

	protected $params = array();
	
	protected $program;

	protected $options = array();

	protected $short = array();

	protected $version;

	protected $usage;

	/**
	 * Constructor.
	 */
	public function __construct($usage = '%prog [options]', $version = null, $description = null)
	{
		$this->program = basename($_SERVER['argv'][0]);

		$this->setUsage($usage);
		$this->setVersion($version);
	}

	public function addOption(array $option)
	{
		if (!isset($option['short']) && !isset($option['long'])) {
			$this->params[] = $option;
		} else {
			$this->options[] = $option;
			if (isset($option['short'])) {
				$this->short[$option['short']] = key($this->options);
			}
			if (isset($option['long'])) {
				$this->long[$option['long']] = key($this->options);
			}
		}
	}

	public function displayHelp($exit = true)
	{
		$this->displayUsage(false);
		echo "\n";
		if (!empty($this->version)) {
			printf("%-30s%s\n", "--version", "display the current version and exit");
		}
		printf("%-30s%s\n", "-h, --help", "show this help message and exit");

		$params = array();
		foreach ($this->options as $option) {
			$opts = array();

			if (isset($option['short'])) {
				$opts[] = '-'.$option['short'];
			}
			if (isset($option['long'])) {
				$opts[] = '--'.$option['long'];
			}
			
			$opts = implode(', ', $opts);
			printf("%-30s%s\n", $opts, (isset($option['desc'])? $option['desc'] : null));
		}

		if ($exit) exit;
	}

	public function displayUsage($exit = true)
	{
		echo "Usage: $this->usage";

		if (sizeof($this->params) > 0) {
			foreach ($this->params as $key => $param) {
				echo ' [param',($key+1),']';
			}
		}

		echo "\n";
		if ($exit) exit;
	}

	public function displayVersion($exit = true)
	{
		echo $this->version,"\n";
		if ($exit) exit;
	}

	public function parse()
	{
		$options = array();

		for ($i = 1; $i < $_SERVER['argc']; $i++) {
			switch ($_SERVER['argv'][$i]) {
				case '-h':
				case '--help':
					$this->displayHelp();
					break;

				case '--version':
					$this->displayVersion();
					break;

				default:
					$arg = $_SERVER['argv'][$i];
					if ($this->_isLongOpt($arg)) {
						$arg = substr($arg, 2);
						if (strstr($arg, '=') !== false) {
							list($arg,$param) = explode('=', $arg);
						}

						if (isset($this->long[$arg])) {
							if (isset($param)) {
								$options[$arg] = $param;
							} else {
								$options[$arg] = true;
							}
						} else {
							echo 'Unknown long option --',$arg,"\n";
						}
					} elseif ($this->_isShortOpt($arg)) {
						$arg = substr($arg, 1);
						if (strlen($arg) > 1) {
							/* short arguments can be merged together, so check them! */
							for ($x = 0; $x < strlen($arg); $x++) {
								if ($arg[$x] === 'h') {
									$this->displayHelp();
								}

								if (isset($this->short[$arg[$x]])) {
									$options[$arg[$x]] = true;
								} else {
									echo 'Unknown short option -',$arg[$x],"\n";
								}
							}
						} else {
							if (isset($this->short[$arg])) {
								$options[$arg] = true;
								$key = $this->short[$arg];

								if (isset($this->options[$key]['max']) && $this->options[$key]['max'] > 0) {
									$next = $i + 1;
									if (isset($_SERVER['argv'][$next]) && !$this->_isShortOpt($_SERVER['argv'][$next]) && !$this->_isLongOpt($_SERVER['argv'][$next])) {
									}
								}
							} else {
								echo 'Unknown short option -',$arg,"\n";
							}
						}
					} else {
						/* parameter or argument */
					}
					break;
			}
		}

		return($options);
	}

	public function setUsage($usage)
	{
		$this->usage = rtrim(str_replace('%prog', $this->program, $usage));
	}

	public function setVersion($version)
	{
		$this->version = $version;
	}

	protected function _isLongOpt($arg)
	{
		if (substr($arg, 0, 2) == '--') {
			return(true);
		} else {
			return(false);
		}
	}

	protected function _isShortOpt($arg)
	{
		if ($arg[0] == '-' && $arg[1] != '-') {
			return(true);
		} else {
			return(false);
		}
	}

	public function __get($name)
	{
		switch ($name) {
			case 'program':
				return($this->program);
				break;
		}
	}
}
