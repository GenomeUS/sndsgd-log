<?php

namespace sndsgd\log\writer;

use \ReflectionClass;
use \Mailgun\Mailgun;
use \sndsgd\log\Record;
use \sndsgd\Config;

/**
 * @coversDefaultClass \sndsgd\log\writer\MailgunWriter
 */
class MailgunWriterTest extends \PHPUnit_Framework_TestCase
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
         "mailgun.apiKey" => "blegh",
         "mailgun.domain" => "example.com",
         "sndsgd.log.writer.email.senderAddress" => "test@example.com",
         "sndsgd.log.writer.email.recipientAddress" => "nobody@example.com"
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
      $w = new MailgunWriter;
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
      $writer = new MailgunWriter;
      $writer->setSender("asd");
   }

   /**
    * @covers ::setSubject
    */
   public function testSetSubject()
   {
      $subject = "test subject";
      $w = new MailgunWriter;
      $w->setSubject($subject);
      $this->assertEquals($subject, $this->getPropertyValue($w, "subject"));
   }

   /**
    * @covers ::sendMessage
    */
   public function testSendEmail()
   {
      $apikey = Config::get("sndsgd.log.writer.mailgun.apiKey");

      $writer = $this->getMockBuilder("sndsgd\\log\\writer\\MailgunWriter")->getMock();
      $writer->method("sendMessage")->willReturn(true);

      $this->r->write($writer);
   }

   /**
    * Really attempts to send a message, but fails due to an invalid api key
    * @expectedException Exception
    */
   public function testSendEmailException()
   {
      $this->r->write("sndsgd\\log\\writer\\MailgunWriter");
   }

   /**
    * @expectedException Exception
    */
   public function testWriteMissingConfigException()
   {
      Config::init();
      $this->r->write("sndsgd\\log\\writer\\MailgunWriter");
   }
}

