<?php
namespace Config
{
	use org\bovigo\vfs\vfsStream;
	use SiTech\Config\Handler\NamedArgs;

	/**
	 * Class INITest
	 * @group Config
	 */
	class INITest extends \PHPUnit_Framework_TestCase
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

		private $contents = <<<PHPUNIT_INI
[section1]
foo=bar
bar=baz
[section2]
baz=shebang
shebang=w00t

PHPUNIT_INI;


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
		 * @covers \SiTech\Config\Handler\File\INI::read
		 */
		public function testRead()
		{
			$configFile = vfsStream::newFile(uniqid('phpunit', true).'.ini')->at($this->root)
				->withContent($this->contents);
			$h = new \SiTech\Config\Handler\File\INI();
			$this->assertEquals($this->config, $h->read(new NamedArgs([
				'filename' => $configFile->url(),
			])));
		}

		/**
		 * @covers \SiTech\Config\Handler\File\INI::read
		 * @covers \SiTech\Config\Handler\File\Exception\FileNotFound
		 */
		public function testReadFileNotFound()
		{
			$filename = uniqid('file_not_found', true).'.ini';
			$h = new \SiTech\Config\Handler\File\INI();
			$this->setExpectedException('\SiTech\Config\Handler\File\Exception\FileNotFound', 'The configuration file '.$filename.' was not found');
			$h->read(new NamedArgs([
				'filename' => $filename
			]));
		}

		/**
		 * @covers \SiTech\Config\Handler\File\INI::read
		 * @covers \SiTech\Config\Handler\File\Exception\FileNotReadable
		 */
		public function testReadFileNotReadable()
		{
			$filename = vfsStream::newFile(uniqid('file_not_found', true).'.ini', 000)->at($this->root);
			$h = new \SiTech\Config\Handler\File\INI();
			$this->setExpectedException('\SiTech\Config\Handler\File\Exception\FileNotReadable', 'The configuration file '.$filename->url().' exists but is not readable');
			$h->read(new NamedArgs([
				'filename' => $filename->url()
			]));
		}

		/**
		 * @covers \SiTech\Config\Handler\File\INI::write
		 */
		public function testWrite()
		{
			$configFile = vfsStream::newFile(uniqid('phpunit', true).'.ini')->at($this->root);
			$h = new \SiTech\Config\Handler\File\INI();
			$h->write(new NamedArgs([
				'filename' => $configFile->url(),
				'config' => $this->config,
			]));
			$this->assertEquals($this->contents, $configFile->getContent());
		}

		/**
		 * @covers \SiTech\Config\Handler\File\INI::write
		 * @covers \SiTech\Config\Handler\File\Exception\FileNotWritable
		 */
		public function testWriteFileNotWritable()
		{
			$configFile = vfsStream::newFile(uniqid('file_not_writable', true).'.ini', 000)->at($this->root);
			$h = new \SiTech\Config\Handler\File\INI();
			$this->setExpectedException('\SiTech\Config\Handler\File\Exception\FileNotWritable', 'The configuration file '.$configFile->url().' exists but is not writable');
			$h->write(new NamedArgs([
				'filename'  => $configFile->url(),
				'config'    => $this->config,
			]));
		}
	}
}
 