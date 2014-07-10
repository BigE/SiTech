# SiTech

![develop build status](https://travis-ci.org/BigE/SiTech.svg?branch=develop)

## Introduction

The SiTech framework is written in PHP and was built to be a very modular framework. Each component can be used by
itself or as part of the framework. The goal behind SiTech is to be very lightweight and extensible to help speed
large application development.

---

## Installation

The simplest way to install SiTech is to use composer. Simply add the github repository to the repositories section
of your composer.json and add SiTech as a requirement.

	{
		"repositories": {
			"type": "vcs",
			"SiTech": "https://github.com/BigE/SiTech.git"
		},
		"require": {
			"SiTech/SiTech": "*"
		}
	}

---

## Unit Tests

PHPUnit is used to perform unit tests on the SiTech framework. There is a phpunit.xml file provided at the base of the
SiTech repository that can be used. To run the tests, simply run the command `phpunit -c /path/to/SiTech/phpunit.xml`.
All tests are stored in the tests folder of the repository.

If you are writing unit tests for the code, you must provide the @covers deceleration for each method tested, on each
declared test method. This will help ensure that we truly have code coverage with unit tests.

## Documentation

Currently we do not have any documentation written up as it is all source code documentation. Until there is official
documentation for SiTech, the simplest way to generate the source code documentation is to use
[phpdocumentor](http://www.phpdoc.org/).