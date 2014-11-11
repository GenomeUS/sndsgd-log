<?php

use sndsgd\log\Record;
use \sndsgd\util\Config;


class MailgunTest extends PHPUnit_Framework_TestCase
{
   public static function loadConfig()
   {
      $path = __DIR__.'/mailgun.config.php';
      if (file_exists($path)) {
         Config::init(require $path);
         return true;
      }
      return false;
   }

   public static function loadFakeConfig()
   {
      $path = __DIR__.'/mailgun.fake-config.php';
      if (file_exists($path)) {
         Config::init(require $path);
         return true;
      }
      return false;
   }

   public function setUp()
   {
      $this->r = (new Record('testing...'))
         ->setName('test')
         ->addData('key', 'value')
         ->addData('multi-line', "one\ntwo");
   }

   /**
    * @expectedException Exception
    */
   public function testWriteMissingConfigException()
   {
      $this->r->write('sndsgd\\log\\writer\\Mailgun');
   }

   public function testSendEmail()
   {
      if (self::loadConfig()) {
         $this->r->write('sndsgd\\log\\writer\\Mailgun');
      }
   }

   /**
    * @expectedException Exception
    */
   public function testSendEmailException()
   {
      if (self::loadFakeConfig()) {
         $this->r->write('sndsgd\\log\\writer\\Mailgun');
      }
   }
}

