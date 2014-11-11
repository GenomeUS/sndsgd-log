<?php

use sndsgd\log\Record;


class RecordTest extends PHPUnit_Framework_TestCase
{
   public function setUp()
   {
      $this->r = new Record();
   }

   public function testConstructor()
   {
      $r = new Record();
      $this->assertEquals('', $r->getMessage());
      

      $r = new Record('testing...');
   }

   public function testSetGetName()
   {
      $this->r->setName('test');
      $this->assertEquals('test', $this->r->getName());
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
   public function testSetNameException2()
   {
      $this->r->setName('asdf 09u132487y9 07 o&YI*(&ty');
   }

   public function testDateAndTimestamp()
   {
      $timestamp = $this->r->getTimestamp();
      $this->assertTrue(is_float($timestamp));
      $this->assertTrue($timestamp < microtime(true));

      $date = $this->r->getDate();
      $this->assertEquals(date('r', floor($timestamp)), $date);
   }

   public function testSetAndGetMessage()
   {
      $this->assertEquals('', $this->r->getMessage());
      $this->r->setMessage('test');
      $this->assertEquals('test', $this->r->getMessage());
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
      $this->assertEquals(null, $this->r->getData());

      $this->r->addData('one', 1);
      $this->assertEquals(['one' => 1], $this->r->getData());

      $this->r->addData([
         'two' => 2,
         'three' => 3
      ]);
      $this->assertEquals(['one'=>1,'two'=>2,'three'=>3], $this->r->getData());
   }

   /**
    * @expectedException InvalidArgumentException
    */
   public function testSetDataException()
   {
      $this->r->addData('123');
   }

   public function testWrite()
   {
      $this->r->setName('test');
      $this->r->setMessage('testing, 1, 2, 3...');
      $this->r->addData(['one' => 1]);
      $this->r->addData('multi-line', "one\ntwo");
      $this->r->write('sndsgd\\log\\writer\\File');
   }
}

