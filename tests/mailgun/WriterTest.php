<?php

namespace sndsgd\log\mailgun;

use \Mailgun\Mailgun;
use \sndsgd\log\Record;
use \sndsgd\Config;


class WriterTest extends \PHPUnit_Framework_TestCase
{
   public static function setUpBeforeClass()
   {
      Config::init([
         'sndsgd.log.writer.mailgun.apiKey' => 'blegh',
         'sndsgd.log.writer.mailgun.domain' => 'example.com',
         'sndsgd.log.writer.mailgun.senderAddress' => 'test@example.com',
         'sndsgd.log.writer.mailgun.recipientAddress' => 'nobody@example.com'
      ]);
   }

   public static function tearDownAfterClass()
   {
      Config::init();
   }

   public function setUp()
   {
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

