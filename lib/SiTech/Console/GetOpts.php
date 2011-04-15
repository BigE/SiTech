<?php
/**
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
 */

namespace SiTech\Console;

/**
 * This class is a replacement for the getopts() function. It adds a lot of
 * functionality to make things more automatic and use less code. This class
 * will automatically parse -h or --help and -v or --version to output the
 * proper information.
 *
 * @author Eric Gach <eric@php-oop.net>
 * @package SiTech\Console
 * @version $Id$
 */
class GetOpts
{
	/**
	 * String option
	 */
	const TYPE_STRING = 1;

	/**
	 * Integer option
	 */
	const TYPE_INT = 2;

	/**
	 * Float option
	 */
	const TYPE_FLOAT = 3;

	/**
	 * Long options that we can parse out when checking input.
	 *
	 * @var array
	 */
	protected $long = array();

	/**
	 * Parameters that will be parsed out when checking input.
	 *
	 * @var array
	 */
	protected $params = array();

	/**
	 * Program name of the current program.
	 *
	 * @var string
	 */
	protected $program;

	/**
	 * Options that the class looks for when checking input.
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * Short options that will be parsed out when checking input.
	 *
	 * @var array
	 */
	protected $short = array();

	/**
	 * Current version of the program.
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Usage of the program.
	 *
	 * @var string
	 */
	protected $usage;

	/**
	 * Here you can specify the specific usage, the version and the description
	 * for the help output.
	 *
	 * @see setUsage,setVersion
	 */
	public function __construct($usage = '%prog [options]', $version = null, $description = null)
	{
		$this->program = basename($_SERVER['argv'][0]);

		$this->setUsage($usage);
		$this->setVersion($version);
	}

	/**
	 * Add an option to the list. You can use the following settings:
	 * 	short:	Short tag for option, a single letter.
	 * 	long:	Long tag for option.
	 * 	desc:	Description to show when help is called.
	 *
	 * @param array $option
	 */
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

	/**
	 * Display the help message from the options that have been added to the
	 * class. This will also display the usage. If exit is true, the program
	 * will automatically exit after the help message is displayed.
	 *
	 * @param bool $exit
	 */
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

	/**
	 * Display the usage for the program. This is shorter than the help and just
	 * shows how to use the program, none of the options are displayed. If exit
	 * is true, the program will exit automatically after the usage is displayed.
	 *
	 * @param bool $exit
	 */
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

	/**
	 * Display the current set version of the program. If exit is true, the
	 * program will exit automatically after the version is displayed.
	 *
	 * @param bool $exit
	 */
	public function displayVersion($exit = true)
	{
		echo $this->version,"\n";
		if ($exit) exit;
	}

	/**
	 * Parse the options sent to the program. This will parse out arguments,
	 * values, and parameters. It will return an array of each item that is
	 * set, and if applicable the value that was set for it. This does no value
	 * checking before returning.
	 *
	 * @return array
	 */
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

	/**
	 * Set the program usage. Using %prog in the string will show the program
	 * name in the string.
	 *
	 * @param string $usage
	 */
	public function setUsage($usage)
	{
		$this->usage = rtrim(str_replace('%prog', $this->program, $usage));
	}

	/**
	 * Set the version string for the current program.
	 *
	 * @param string $version
	 */
	public function setVersion($version)
	{
		$this->version = $version;
	}

	/**
	 * Internal test to see if the option is a long option.
	 *
	 * @param string $arg
	 * @return bool
	 */
	protected function _isLongOpt($arg)
	{
		return(substr($arg, 0, 2) == '--');
	}

	/**
	 * Internal test to see if the option is a short option.
	 *
	 * @param string $arg
	 * @return bool
	 */
	protected function _isShortOpt($arg)
	{
		return($arg[0] == '-' && $arg[1] != '-');
	}

	/**
	 * Get class variables. Currently the only one that can be retreived is the
	 * program variable.
	 *
	 * @param string $name
	 * @return string
	 */
	public function __get($name)
	{
		switch ($name) {
			case 'program':
				return($this->program);
				break;
		}
	}
}
