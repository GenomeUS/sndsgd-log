<?php

namespace sndsgd\log\file;

use \org\bovigo\vfs\vfsStream;
use \sndsgd\log\Record;
use \sndsgd\Config;
use \sndsgd\Temp;


class ReaderTest extends \PHPUnit_Framework_TestCase
{
   /**
    * @covers nothing
    */
   public function tearDown()
   {
      Config::init();
   }

   /**
    * @covers nothing
    */
   private function createVfs()
   {
      $root = vfsStream::setup('root');
      $vdir = vfsStream::newDirectory('logs')->at($root);
      return [$vdir, vfsStream::url('root/logs')];
   }

   /**
    * @covers nothing
    */
   private function createLogRecord()
   {
      $dir = Temp::dir('test-log-file-reader-');
      Config::set('sndsgd.log.file.dir', $dir);
      $name = 'test';
      $record = Record::create($name, 'this is the message');
      $record->write('sndsgd\\log\\file\\Writer');

      return [$name, LogFile::getPathFromName($name)];
   }

   /**
    * @covers \sndsgd\log\file\Reader::createFromName
    * @expectedException Exception
    */
   public function testCreateFromNameFailure()
   {
      Reader::createFromName(42);
   }

   /**
    * @covers \sndsgd\log\file\Reader::createFromName
    */
   public function testCreateFromName()
   {
      list($name, $path) = $this->createLogRecord();

      $reader = Reader::createFromName($name);
      $this->assertInstanceOf('sndsgd\\log\\file\\Reader', $reader);
   }

   /**
    * @covers \sndsgd\log\file\Reader::__construct
    */
   public function testConstruct()
   {
      list($name, $path) = $this->createLogRecord();
      $reader = new Reader($path);
      $this->assertInstanceOf('sndsgd\\log\\file\\Reader', $reader);
   }

   /**
    * @expectedException InvalidArgumentException
    */
   public function testConstructorInvalidArgException()
   {
      $reader = new Reader(42);
   }

   /**
    * @covers \sndsgd\log\file\Reader::__construct
    * @expectedException Exception
    */
   public function testConstructorFilePrepareFailure()
   {
      list($vfsdir, $path) = $this->createVfs();
      Config::set('sndsgd.log.file.dir', $path);
      $vfsdir->chmod(0000);
      $reader = new Reader("{$path}/test.log");
   }

   /**
    * @covers \sndsgd\log\file\Reader::__destruct
    */
   public function testDestruct()
   {
      list($name, $path) = $this->createLogRecord();
      $reader = new Reader($path);
      $reader = null;
   }

   /**
    * @covers nothing
    */
   private function createMultipleLogRecords($len = null)
   {
      $len = ($len === null) ? mt_rand(10, 100) : $len;
      $dir = Temp::dir('test-log-file-reader-');
      Config::set('sndsgd.log.file.dir', $dir);
      $name = 'test';
      for ($i=0; $i<$len; $i++) {
         $record = Record::create($name, "message #$i");
         $record->write('sndsgd\\log\\file\\Writer');
      }

      return [LogFile::getPathFromName($name), $len];
   }

   /**
    * @covers \sndsgd\log\file\Reader::count
    */
   public function testCount()
   {
      list($path, $num) = $this->createMultipleLogRecords();
      $reader = new Reader($path);
      $this->assertEquals($num, $reader->count());
   }

   /**
    * @covers \sndsgd\log\file\Reader::setOffset
    */
   public function testSetOffset()
   {
      list($path, $num) = $this->createMultipleLogRecords(10);
      $reader = new Reader($path);
      $this->assertEquals(10, $reader->count());

      $this->assertTrue($reader->setOffset(9));
      $this->assertFalse($reader->setOffset(12));
   }

   /**
    * @covers \sndsgd\log\file\Reader::setOffset
    * @expectedException InvalidArgumentException
    */
   public function testSetOffsetInvalidArgumentException()
   {
      list($path, $num) = $this->createMultipleLogRecords(10);
      $reader = new Reader($path);
      $reader->setOffset('asd');
   }

   /**
    * @covers \sndsgd\log\file\Reader::read
    */
   public function testRead()
   {
      list($path, $num) = $this->createMultipleLogRecords();
      $reader = new Reader($path);
      for ($i=0; $i<$num; $i++) {
         $record = $reader->read();
         $this->assertEquals("message #$i", $record->getMessage());
      }

      $this->assertNull($reader->read());
   }
}

