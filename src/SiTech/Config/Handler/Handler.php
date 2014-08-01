<?php
namespace SiTech\Config\Handler
{
	interface Handler
	{
		public function read(NamedArgs $args);
		public function write(NamedArgs $args);
	}
} 