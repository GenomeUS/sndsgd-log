<?php

namespace sndsgd\log\mailgun;

use \sndsgd\log\Record;
use \sndsgd\util\Config;


class WriterTest extends \PHPUnit_Framework_TestCase
{
   public static function loadConfig()
   {
      $path = __DIR__.'/config.php';
      if (file_exists($path)) {
         Config::init(require $path);
         return true;
      }
      return false;
   }

   public static function loadFakeConfig()
   {
      $path = __DIR__.'/fake-config.php';
      if (file_exists($path)) {
         Config::init(require $path);
         return true;
      }
      return false;
   }

   public function setUp()
   {
      $this->r = Record::create('test', 'this is the message');
      $this->r->addData('key', 'value');
      $this->r->addData('multi-line', "one\ntwo");
   }

   /**
    * @expectedException Exception
    */
   public function testWriteMissingConfigException()
   {
      $this->r->write('sndsgd\\log\\mailgun\\Writer');
   }

   public function testSendEmail()
   {
      if (self::loadConfig()) {
         $this->r->write('sndsgd\\log\\mailgun\\Writer');
      }
   }

   /**
    * @expectedException Exception
    */
   public function testSendEmailException()
   {
      if (self::loadFakeConfig()) {
         $this->r->write('sndsgd\\log\\mailgun\\Writer');
      }
   }
}

