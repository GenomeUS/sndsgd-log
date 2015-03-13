<?php

namespace sndsgd\log\mailgun;

use \ReflectionClass;
use \Mailgun\Mailgun;
use \sndsgd\log\Record;
use \sndsgd\Config;

/**
 * @coversDefaultClass \sndsgd\log\mailgun\Writer
 */
class WriterTest extends \PHPUnit_Framework_TestCase
{
   /**
    * @coversNothing
    */
   public static function tearDownAfterClass()
   {
      Config::init();
   }

   /**
    * @coversNothing
    */
   public function setUp()
   {
      Config::init([
         "sndsgd.log.writer.mailgun.apiKey" => "blegh",
         "sndsgd.log.writer.mailgun.domain" => "example.com",
         "sndsgd.log.writer.mailgun.senderAddress" => "test@example.com",
         "sndsgd.log.writer.mailgun.recipientAddress" => "nobody@example.com"
      ]);

      $this->r = Record::create("test", "this is the message");
      $this->r->addData("key", "value");
      $this->r->addData("multi-line", "one\ntwo");
   }

   /**
    * @coversNothing
    */
   private function getPropertyValue($class, $property)
   {
      $rc = new ReflectionClass(get_class($class));
      $property = $rc->getProperty($property);
      $property->setAccessible(true);
      return $property->getValue($class);
   }

   /**
    * @covers ::setSender
    * @covers ::setRecipient
    * @covers ::validateEmail
    */
   public function testSetSenderAndRecipient()
   {
      $sender = "sender@domain.com";
      $recipient = "recipient@domain.com";
      $w = new Writer;
      $w->setSender($sender);
      $this->assertEquals($sender, $this->getPropertyValue($w, "sender"));
      $w->setRecipient($recipient);
      $this->assertEquals($recipient, $this->getPropertyValue($w, "recipient"));
   }

   /**
    * @covers ::validateEmail
    * @expectedException InvalidArgumentException
    */
   public function testSetSenderInvalidEmail()
   {
      $writer = new Writer;
      $writer->setSender("asd");
   }

   /**
    * @covers ::setSubject
    */
   public function testSetSubject()
   {
      $subject = "test subject";
      $w = new Writer;
      $w->setSubject($subject);
      $this->assertEquals($subject, $this->getPropertyValue($w, "subject"));
   }

   /**
    * @covers ::sendMessage
    */
   public function testSendEmail()
   {
      $apikey = Config::get("sndsgd.log.writer.mailgun.apiKey");

      $writer = $this->getMockBuilder("sndsgd\\log\\mailgun\\Writer")->getMock();
      $writer->method("sendMessage")->willReturn(true);

      $this->r->write($writer);
   }

   /**
    * Really attempts to send a message, but fails due to an invalid api key
    * @expectedException Exception
    */
   public function testSendEmailException()
   {
      $this->r->write("sndsgd\\log\\mailgun\\Writer");
   }

   /**
    * @expectedException Exception
    */
   public function testWriteMissingConfigException()
   {
      Config::init();
      $this->r->write("sndsgd\\log\\mailgun\\Writer");
   }
}

