<?php
namespace SiTech\Config\Handler
{
	interface Handler
	{
		public function read(NamedArgs $args = null);
		public function write(NamedArgs $args = null);
	}
} 