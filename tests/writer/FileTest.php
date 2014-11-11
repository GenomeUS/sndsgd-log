<?php

use sndsgd\log\Record;
use \sndsgd\util\Config;


class FileTest extends PHPUnit_Framework_TestCase
{
   public function setUp()
   {
      Config::init();
      $this->r = (new Record('testing...'))
         ->setName('test')
         ->addData('key', 'value')
         ->addData('multi-line', "one\ntwo");
   }

   /**
    * @expectedException Exception
    */
   public function testInvalidFilePathException()
   {
      Config::set('sndsgd.log.writer.file.path', '/___doesnt_exist__/logs');
      $this->r->write('sndsgd\\log\\writer\\File');
   }
}

