<?php
/**
 * Created by PhpStorm.
 * User: egach
 * Date: 8/18/15
 * Time: 23:56
 */

namespace Config
{
	use org\bovigo\vfs\vfsStream;
	use SiTech\Config\Handler\NamedArgs;

	class XmlTest extends \PHPUnit_Framework_TestCase
	{
		private $config = [
			'section1' => [
				'foo' => 'bar',
				'bar' => 'baz',
			],
			'section2' => [
				'baz' => 'shebang',
				'shebang' => 'w00t',
			],
		];

		private $contents = <<<PHPUNIT_XML
<?xml version="1.0" encoding="UTF-8"?>
<config>
<section name="section1">
<key name="foo">bar</key>
<key name="bar">baz</key>
</section>
<section name="section2">
<key name="baz">shebang</key>
<key name="shebang">w00t</key>
</section>
</config>
PHPUNIT_XML;


		/**
		 * @var \org\bovigo\vfs\vfsStreamContainer
		 */
		private $root;

		/**
		 * @var \org\bovigo\vfs\vfsStreamFile
		 */
		private $configFile;

		public function setUp()
		{
			$this->root = vfsStream::setup('config');
		}

		/**
		 * @covers \SiTech\Config\Handler\File\Xml::read
		 */
		public function testRead()
		{
			$configFile = vfsStream::newFile(uniqid('phpunit', true).'.xml')
				->withContent($this->contents)
				->at($this->root);
			$h = new \SiTech\Config\Handler\File\Xml();
			$this->assertEquals($this->config, $h->read(new NamedArgs([
				'filename' => $configFile->url(),
			])));
		}

		/**
		 * @covers \SiTech\Config\Handler\File\Xml::read
		 * @covers \SiTech\Config\Handler\File\Exception\FileNotFound
		 */
		public function testReadFileNotFound()
		{
			$filename = uniqid('file_not_found', true).'.xml';
			$h = new \SiTech\Config\Handler\File\Xml();
			$this->setExpectedException('\SiTech\Config\Handler\File\Exception\FileNotFound', 'The configuration file '.$filename.' was not found');
			$h->read(new NamedArgs([
				'filename' => $filename
			]));
		}

		/**
		 * @covers \SiTech\Config\Handler\File\Xml::read
		 * @covers \SiTech\Config\Handler\File\Exception\FileNotReadable
		 */
		public function testReadFileNotReadable()
		{
			$filename = vfsStream::newFile(uniqid('file_not_found', true).'.xml', 000)->at($this->root);
			$h = new \SiTech\Config\Handler\File\Xml();
			$this->setExpectedException('\SiTech\Config\Handler\File\Exception\FileNotReadable', 'The configuration file '.$filename->url().' exists but is not readable');
			$h->read(new NamedArgs([
				'filename' => $filename->url()
			]));
		}

		/**
		 * @covers \SiTech\Config\Handler\File\Xml::read
		 * @covers \SiTech\Config\Handler\File\Xml\Exception\ParsingError
		 */
		public function testReadParsingError()
		{
			$configFile = vfsStream::newFile(uniqid('parsing_error', true).'.xml')
				->withContent('invalid')->at($this->root);
			$h = new \SiTech\Config\Handler\File\Xml();
			$this->setExpectedException(
        '\SiTech\Config\Handler\File\Xml\Exception\ParsingError',
        'XML Error: "Not well-formed (invalid token)" File: ' . $configFile->url() . ' Line: 1 Col: 1 Pos: 0'
      );
			$h->read(new NamedArgs([
				'filename' => $configFile->url(),
			]));
		}

		/**
		 * @covers \SiTech\Config\Handler\File\Xml::write
		 */
		public function testWrite()
		{
			$configFile = vfsStream::newFile(uniqid('phpunit', true).'.xml')->at($this->root);
			$h = new \SiTech\Config\Handler\File\Xml();
			$h->write(new NamedArgs([
				'filename' => $configFile->url(),
				'config' => $this->config,
			]));
			$this->assertEquals($this->contents, $configFile->getContent());
		}

		/**
		 * @covers \SiTech\Config\Handler\File\Xml::write
		 * @covers \SiTech\Config\Handler\File\Exception\FileNotWritable
		 */
		public function testWriteFileNotWritable()
		{
			$configFile = vfsStream::newFile(uniqid('file_not_writable', true).'.xml', 000)->at($this->root);
			$h = new \SiTech\Config\Handler\File\Xml();
			$this->setExpectedException('\SiTech\Config\Handler\File\Exception\FileNotWritable', 'The configuration file '.$configFile->url().' exists but is not writable');
			$h->write(new NamedArgs([
				'filename'  => $configFile->url(),
				'config'    => $this->config,
			]));
		}
	}
}
