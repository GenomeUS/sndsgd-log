<?php

namespace sndsgd\log;

use \sndsgd\Config;


class File extends \sndsgd\fs\File
{
   /**
    * Property delimeter for files
    * 
    * @const string
    */
   const DELIMETER = " •|• ";

   /**
    * Default log file directory
    *
    * Override by setting a config value for 'sndsgd.log.file.dir'
    * @const string
    */
   const DEFAULT_DIR = "/tmp/sndsgd-logs";

   /**
    * Create a file instance given a log name
    * 
    * @param string $name The name of the log record
    * @return \sndsgd\fs\File
    */
   public static function createFromLogName($name)
   {
      $dir = Config::get("sndsgd.log.file.dir", self::DEFAULT_DIR);
      return new self("{$dir}/{$name}.log");
   }
}

