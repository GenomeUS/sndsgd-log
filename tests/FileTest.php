<?php

namespace sndsgd\log;

use \sndsgd\Config;


class FileTest extends \PHPUnit_Framework_TestCase
{

   public function testCreateFromLogName()
   {
      Config::init();

      $name = "test";
      $expect = File::DEFAULT_DIR."/$name.log";
      $file = File::createFromLogName($name);
      $this->assertEquals($expect, $file->getPath());

      Config::set("sndsgd.log.file.dir", __DIR__);
      $expect = __DIR__."/$name.log";
      $file = File::createFromLogName($name);
      $this->assertEquals($expect, $file->getPath());

      Config::init();
   }
}

