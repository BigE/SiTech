#!/usr/bin/env php
<?php
/*
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

/**
 * This is to help generate documentation for the SiTech library. Currently it
 * supports generation for
 *
 * phpDocumentor - v1 works, but does not support 5.3 features. v2 is previously
 *                 known as "docblox" and is the prefered method.
 * DocBlox - http://www.docblox-project.org/
 */

define('SITECH_BASE', dirname(dirname(__FILE__)));

function rrmdir($dir)
{
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
			}
		}
		reset($objects);
		rmdir($dir);
	}
}

function _mkdir($dir)
{
	if (!file_exists($dir)) {
		if (!mkdir($dir)) {
			echo 'Failed to create folder '.$dir.PHP_EOL;
			exit(-1);
		}
	}
}

require_once(SITECH_BASE.'/lib/SiTech/Console/GetOpts.php');
$opts = new SiTech\Console\GetOpts('%prog [options]', '1.0.0', 'Script to help generate documentation for the SiTech library.');
$opts->addOption(array(
	'desc' => 'Remove the old documentation files. Some will use them as cache and produce incorrect results.',
	'long' => 'clean',
	'short' => 'c'
));
$opts->addOption(array(
	'desc' => 'Show output from the documentation generator.',
	'long' => 'verbose',
	'short' => 'v'
));
/*$opts->addOption(array(
	'long' => 'docblox',
	'desc' => 'Generate documentation using DocBlox.'
));*/
$opts->addOption(array(
	'long' => 'doxygen',
	'desc' => 'Generate documentation using doxygen. This uses the config file in '.SITECH_BASE.'/Docs/SiTech.doxygen'
));
$opts->addOption(array(
	'long' => 'phpdoc',
	'desc' => 'Generate documentation using phpDocuemntor.'
));
$options = $opts->parse();

define('DOCS_VERBOSE', (isset($options['verbose']) || isset($options['v']))? true : false);

if (isset($options['clean']) || isset($options['c'])) {
	echo 'Cleaning old documentation files... ';
	rrmdir(SITECH_BASE.'/Docs/DocBlox');
	rrmdir(SITECH_BASE.'/Docs/Doxygen');
	rrmdir(SITECH_BASE.'/Docs/phpDocs');
	echo 'Done.'.PHP_EOL;
}

if (isset($options['docblox'])) {
	_mkdir(SITECH_BASE.'/Docs/DocBlox');

	echo 'Processing documentation... ';
	ob_start();
	echo PHP_EOL;
	echo shell_exec('docblox project:parse -d '.SITECH_BASE.'/lib/SiTech -t '.SITECH_BASE.'/Docs/DocBlox');
	echo PHP_EOL;
	if (DOCS_VERBOSE) {
		ob_end_flush();
	}  else {
		ob_end_clean();
	}
	echo 'Done.'.PHP_EOL;
	echo 'Generating templates... ';
	ob_start();
	echo PHP_EOL;
	echo shell_exec('docblox project:transform -s '.SITECH_BASE.'/Docs/DocBlox/structure.xml -t '.SITECH_BASE.'/Docs/DocBlox');
	echo PHP_EOL;
	if (DOCS_VERBOSE) {
		ob_end_flush();
	}  else {
		ob_end_clean();
	}
	echo 'Done.'.PHP_EOL;
}

if (isset($options['doxygen'])) {
	_mkdir(SITECH_BASE.'/Docs/Doxygen');
	echo 'Running Doxygen... ';
	ob_start();
	echo PHP_EOL;
	echo shell_exec('doxygen '.SITECH_BASE.'/Docs/SiTech.doxygen');
	echo PHP_EOL;
	if (DOCS_VERBOSE) {
		ob_end_flush();
	} else {
		ob_end_clean();
	}
	echo 'Done.'.PHP_EOL;
}

if (isset($options['phpdoc'])) {
	_mkdir(SITECH_BASE.'/Docs/phpDocs');
	echo 'Running phpDocumentor... ';
	ob_start();
	echo PHP_EOL;
	// This command should support phpDocumentor 1.x and 2.x
	echo shell_exec('phpdoc -d '.SITECH_BASE.'/lib/SiTech -t '.SITECH_BASE.'/Docs/phpDocs --sourcecode --title SiTech');
	echo PHP_EOL;
	if (DOCS_VERBOSE) {
		ob_end_flush();
	} else {
		ob_end_clean();
	}
	echo 'Done.'.PHP_EOL;
}

exit(0);
