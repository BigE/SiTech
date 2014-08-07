<?php
namespace Config
{
	use org\bovigo\vfs\vfsStream;
	use SiTech\Config\Handler\NamedArgs;

	/**
	 * Class IniTest
	 * @group Config
	 */
	class IniTest extends \PHPUnit_Framework_TestCase
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

		private $contents = <<<PHPUNIT_Ini
[section1]
foo=bar
bar=baz
[section2]
baz=shebang
shebang=w00t

PHPUNIT_Ini;


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
		 * @covers \SiTech\Config\Handler\File\Ini::read
		 */
		public function testRead()
		{
			$configFile = vfsStream::newFile(uniqid('phpunit', true).'.ini')
				->withContent($this->contents)
				->at($this->root);
			$h = new \SiTech\Config\Handler\File\Ini();
			$this->assertEquals($this->config, $h->read(new NamedArgs([
				'filename' => $configFile->url(),
			])));
		}

		/**
		 * @covers \SiTech\Config\Handler\File\Ini::read
		 * @covers \SiTech\Config\Handler\File\Exception\FileNotFound
		 */
		public function testReadFileNotFound()
		{
			$filename = uniqid('file_not_found', true).'.ini';
			$h = new \SiTech\Config\Handler\File\Ini();
			$this->setExpectedException('\SiTech\Config\Handler\File\Exception\FileNotFound', 'The configuration file '.$filename.' was not found');
			$h->read(new NamedArgs([
				'filename' => $filename
			]));
		}

		/**
		 * @covers \SiTech\Config\Handler\File\Ini::read
		 * @covers \SiTech\Config\Handler\File\Exception\FileNotReadable
		 */
		public function testReadFileNotReadable()
		{
			$filename = vfsStream::newFile(uniqid('file_not_found', true).'.ini', 000)->at($this->root);
			$h = new \SiTech\Config\Handler\File\Ini();
			$this->setExpectedException('\SiTech\Config\Handler\File\Exception\FileNotReadable', 'The configuration file '.$filename->url().' exists but is not readable');
			$h->read(new NamedArgs([
				'filename' => $filename->url()
			]));
		}

		/**
		 * @covers \SiTech\Config\Handler\File\Ini::read
		 * @covers \SiTech\Config\Handler\File\Ini\Exception\ParsingError
		 */
		public function testReadParsingError()
		{
			$configFile = vfsStream::newFile(uniqid('parsing_error', true).'.ini')
				->withContent('invalid')->at($this->root);
			$h = new \SiTech\Config\Handler\File\Ini();
			$this->setExpectedException('\SiTech\Config\Handler\File\Ini\Exception\ParsingError', 'There was a problem parsing the ini file '.$configFile->url());
			$h->read(new NamedArgs([
				'filename' => $configFile->url(),
			]));
		}

		/**
		 * @covers \SiTech\Config\Handler\File\Ini::write
		 */
		public function testWrite()
		{
			$configFile = vfsStream::newFile(uniqid('phpunit', true).'.ini')->at($this->root);
			$h = new \SiTech\Config\Handler\File\Ini();
			$h->write(new NamedArgs([
				'filename' => $configFile->url(),
				'config' => $this->config,
			]));
			$this->assertEquals($this->contents, $configFile->getContent());
		}

		/**
		 * @covers \SiTech\Config\Handler\File\Ini::write
		 * @covers \SiTech\Config\Handler\File\Exception\FileNotWritable
		 */
		public function testWriteFileNotWritable()
		{
			$configFile = vfsStream::newFile(uniqid('file_not_writable', true).'.ini', 000)->at($this->root);
			$h = new \SiTech\Config\Handler\File\Ini();
			$this->setExpectedException('\SiTech\Config\Handler\File\Exception\FileNotWritable', 'The configuration file '.$configFile->url().' exists but is not writable');
			$h->write(new NamedArgs([
				'filename'  => $configFile->url(),
				'config'    => $this->config,
			]));
		}
	}
}
 