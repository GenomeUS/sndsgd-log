<?php

namespace sndsgd\log\writer;

use \Exception;
use \sndsgd\log\FileInterface;
use \sndsgd\log\Record;
use \sndsgd\log\Writer;
use \sndsgd\util\Config;
use \sndsgd\util\File as UtilFile;
use \sndsgd\util\Path;


/**
 * A log writer for files
 */
class File extends Writer implements FileInterface
{
   /**
    * {@inheritdoc}
    */
   public function write()
   {
      $dir = Config::get('sndsgd.log.writer.file.path', self::DEFAULT_PATH);
      $path = Path::normalize($dir.'/'.$this->record->getName().'.log');
      if (($prep = UtilFile::prepare($path, 0775)) !== true) {
         throw new Exception("failed to write log file '$path'; $prep");
      }
      
      $line = $this->createLine();
      if (!@file_put_contents($path, "$line\n", FILE_APPEND)) {
         throw new Exception("failed to write log '$path': $line");
      }      
   }

   /**
    * Create a line of text from the record
    * 
    * @return string
    */
   private function createLine()
   {
      $timestamp = $this->record->getTimestamp();
      $date = date('r', floor($timestamp));
      return implode(self::DELIMETER, [
         number_format($timestamp, 4, '.', ''),
         $date,
         $this->record->getMessage(),
         json_encode($this->record->getData(), JSON_UNESCAPED_SLASHES)
      ]);
   }
}

