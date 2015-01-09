<?php

namespace sndsgd\log\file;

use \org\bovigo\vfs\vfsStream;
use \sndsgd\log\Record;
use \sndsgd\util\Config;
use \sndsgd\util\Temp;


class WriterTest extends \PHPUnit_Framework_TestCase
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

   public function testWrite()
   {
      list($vdir, $path) = $this->createVfs();
      Config::set('sndsgd.log.file.dir', $path);
      
      $record = Record::create('test', 'this is the log message');
      $record->write('sndsgd\\log\\file\\Writer');
   }

   public function testWriteWithObject()
   {
      list($vdir, $path) = $this->createVfs();
      Config::set('sndsgd.log.file.dir', $path);
      
      $record = Record::create('test', 'this is the log message');
      $record->write(new \sndsgd\log\file\Writer);
   }

   /**
    * @expectedException Exception
    */
   public function testWritePrepareFail()
   {
      list($vdir, $path) = $this->createVfs();
      Config::set('sndsgd.log.file.dir', $path);
      $vdir->chmod(0000);

      $record = Record::create('test', 'this is the log message');
      $record->write('sndsgd\\log\\file\\Writer');
   }

   /**
    * @expectedException Exception
    */
   public function testWriteFileWriteFailure()
   {
      list($vdir, $path) = $this->createVfs();
      Config::set('sndsgd.log.file.dir', $path);
      vfsStream::setQuota(10);
      
      $record = Record::create('test', 'this is the log message');
      $record->write('sndsgd\\log\\file\\Writer');
   }

}

