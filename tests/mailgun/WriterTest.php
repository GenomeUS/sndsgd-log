<?php

namespace sndsgd\log\mailgun;

use \Mailgun\Mailgun;
use \sndsgd\log\Record;
use \sndsgd\util\Config;


class WriterTest extends \PHPUnit_Framework_TestCase
{
   public function setUp()
   {
      $path = __DIR__.'/../resources/fake-mailgun-config.php';
      Config::init(require $path);

      $this->r = Record::create('test', 'this is the message');
      $this->r->addData('key', 'value');
      $this->r->addData('multi-line', "one\ntwo");
   }

   public function testSendEmail()
   {
      $apikey = Config::get('sndsgd.log.writer.mailgun.apiKey');

      $writer = $this->getMockBuilder('sndsgd\\log\\mailgun\\Writer')->getMock();
      $writer->method('sendMessage')->willReturn(true);

      $this->r->write($writer);
   }

   /**
    * @expectedException Exception
    */
   public function testSendEmailException()
   {
      $this->r->write('sndsgd\\log\\mailgun\\Writer');
   }

   /**
    * @expectedException Exception
    */
   public function testWriteMissingConfigException()
   {
      Config::init([]);
      $this->r->write('sndsgd\\log\\mailgun\\Writer');
   }
}

