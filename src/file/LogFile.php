<?php

namespace sndsgd\log\file;

use \sndsgd\log\Record;
use \sndsgd\Config;

/**
 * Utility methods for working with log files
 */
class LogFile
{
   /**
    * Property delimeter for files
    * 
    * @const string
    */
   const DELIMETER = ' •|• ';

   /**
    * Default log file directory
    *
    * Override by setting a config value for 'sndsgd.log.file.dir'
    * @const string
    */
   const DEFAULT_DIR = '/tmp/sndsgd-logs';

   /**
    * Get an absolute file path for a named log
    *
    * @param string $name The name of the log
    * @return string The absolute file path
    * @throws Exception If the config value is not set
    */
   public static function getPathFromName($name = 'error')
   {
      $dir = Config::get('sndsgd.log.file.dir', self::DEFAULT_DIR);
      return "{$dir}/{$name}.log";
   }

   /**
    * Create a line of text from a log record
    * 
    * @param sndsgd\log\Record $record
    * @return string
    */
   public static function encodeRecord(Record $record)
   {
      return implode(self::DELIMETER, [
         number_format($record->getTimestamp(), 4, '.', ''),
         $record->getDate(),
         $record->getMessage(),
         json_encode($record->getData(), JSON_UNESCAPED_SLASHES)
      ]);
   }

   /**
    * Decode a string into a log record instance
    * 
    * @param string $line
    * @return sndsgd\log\Record
    */
   public static function decodeRecord($line)
   {
      $parts = explode(self::DELIMETER, $line, 4);
      if (count($parts) !== 4) {
         return null;
      }

      $record = new Record($parts[0]);
      $record->setMessage($parts[2]);
      $record->addData(json_decode($parts[3], true));
      return $record;
   }
}

