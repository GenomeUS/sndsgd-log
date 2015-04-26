<?php

namespace sndsgd\log\writer;

use \Exception;
use \sndsgd\log\FileInterface;
use \sndsgd\log\Record;
use \sndsgd\Config;
use \sndsgd\Path;


/**
 * A log file writer
 */
class FileWriter extends \sndsgd\log\Writer
{
   /**
    * {@inheritdoc}
    */
   public function write()
   {
      $file = File::createFromLogName($this->record->getName());
      if (!$file->prepareWrite()) {
         throw new Exception("failed to write log record; ".$file->getError());
      }

      $line = $this->encodeRecord();
      if (!$file->write($line, FILE_APPEND)) {
         throw new Exception("failed to write log; ".$file->getError());
      }
   }

   private function encodeRecord()
   {
      $opts = JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT;
      $data = json_encode($this->record->getData(), $opts);
      return implode(File::DELIMETER, [
         $this->record->getDate(),
         $this->record->getMessage(),
         $data
      ]);
   }
}

