<?php

namespace sndsgd\log\file;

use \Exception;
use \sndsgd\log\FileInterface;
use \sndsgd\log\Record;
use \sndsgd\Config;
use \sndsgd\File;
use \sndsgd\Path;


/**
 * A log file writer
 */
class Writer extends \sndsgd\log\Writer
{
   /**
    * {@inheritdoc}
    */
   public function write()
   {
      $path = LogFile::getPathFromName($this->record->getName());
      if (($test = File::prepare($path, 0775)) !== true) {
         throw new Exception("failed to write log file '$path'; $test");
      }

      $line = LogFile::encodeRecord($this->record);
      if (!@file_put_contents($path, "$line\n", FILE_APPEND)) {
         throw new Exception("failed to write log '$path': $line");
      }
   }
}

