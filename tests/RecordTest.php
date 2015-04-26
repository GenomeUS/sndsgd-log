<?php

namespace sndsgd\log;

use \StdClass;


class RecordTest extends \PHPUnit_Framework_TestCase
{
   public function setUp()
   {
      $this->r = new Record;
   }

   public function testCreate()
   {
      $name = "test";
      $message = "test message";
      $r = Record::create($name, $message);
      $this->assertInstanceOf("sndsgd\\log\\Record", $r);
      $this->assertEquals($name, $r->getName());
      $this->assertEquals($message, $r->getMessage());
   }

   public function testSetGetName()
   {
      $this->r->setName("test");
      $this->assertEquals("test", $this->r->getName());
   }

   /**
    * @expectedException InvalidArgumentException
    */
   public function testSetNameException()
   {
      $this->r->setName([]);
   }

   /**
    * @expectedException InvalidArgumentException
    */
   public function testSetNameRegexException()
   {
      $this->r->setName("asdf 09u132487y9 07 o&YI*(&ty");
   }

   public function testDateAndTimestamp()
   {
      $timestamp = $this->r->getTimestamp();
      $this->assertTrue(is_float($timestamp));
      $this->assertTrue($timestamp < microtime(true));

      $date = $this->r->getDate("r");
      $this->assertEquals(date("r", $timestamp), $date);
   }

   public function testSetAndGetMessage()
   {
      $this->assertEquals("", $this->r->getMessage());
      $this->r->setMessage("test \n  test ");
      $this->assertEquals("test test", $this->r->getMessage());

      $this->r->setMessage("test\ntest");
      $this->assertEquals("test test", $this->r->getMessage());
   }

   /**
    * @expectedException InvalidArgumentException
    */
   public function testSetMessageException()
   {
      $this->r->setMessage([]);
   }

   public function testData()
   {
      $this->assertEquals([], $this->r->getData());

      $this->r->addData("one", 1);
      $this->assertEquals(["one" => 1], $this->r->getData());

      $this->r->addData([
         "two" => 2,
         "three" => 3
      ]);
      $this->assertEquals(["one"=>1,"two"=>2,"three"=>3], $this->r->getData());
   }

   /**
    * @expectedException InvalidArgumentException
    */
   public function testWriteInvalidStringArgument()
   {
      $this->r->write("invalid");
   }

   /**
    * @expectedException InvalidArgumentException
    */
   public function testWriteInvalidObjectArgument()
   {
      $this->r->write(new StdClass);
   }
}

