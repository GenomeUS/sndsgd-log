<?php

use \sndsgd\log\Record;
use \sndsgd\log\file\LogFile;
use \sndsgd\util\Config;


class LogFileTest extends PHPUnit_Framework_TestCase
{
   /**
    * @covers \sndsgd\log\file\LogFile::getPathFromName
    */
   public function testGetPathFromName()
   {
      Config::set('sndsgd.log.file.dir', __DIR__);
      $expect = __DIR__.'/test.log';
      $this->assertEquals($expect, LogFile::getPathFromName('test'));
   }

   /**
    * @covers \sndsgd\log\file\LogFile::getPathFromName
    */
   public function testGetPathFromNameMissingConfigException()
   {
      Config::init();
      $expect = LogFile::DEFAULT_DIR.'/test.log';
      $this->assertEquals($expect, LogFile::getPathFromName('test'));
   }

   private function createTestRecord()
   {
      $record = Record::create('test', 'message');
      $record->addData('one', 1);
      return $record;
   }

   /**
    * @covers \sndsgd\log\file\LogFile::encodeRecord
    */
   public function testEncodeRecord()
   {
      $line = LogFile::encodeRecord($this->createTestRecord());
   }

   /**
    * @covers \sndsgd\log\file\LogFile::decodeRecord
    */
   public function testDecodeRecord()
   {
      $line = LogFile::encodeRecord($this->createTestRecord());
      $record = LogFile::decodeRecord($line);
      $this->assertEquals('message', $record->getMessage());
      $this->assertEquals(1, $record->getData('one'));

      $this->assertNull(LogFile::decodeRecord('invalid | log'));
   }

}

