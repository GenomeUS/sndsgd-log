<?php

namespace sndsgd\log\file;

use \Exception;
use \InvalidArgumentException;
use \sndsgd\util\File;


/**
 * A log file reader
 */
class Reader extends \sndsgd\log\Reader
{
   /**
    * Create a reader from a log name
    *
    * @param string $name The name of the log file to read
    * @return sndsgd\log\file\Reader
    */
   public static function createFromName($name)
   {
      $path = LogFile::getPathFromName($name);
      return new self($path);
   }

   /**
    * The absolute path to the log file
    *
    * @var string
    */
   protected $path;

   /**
    * The file resource for reading from
    *
    * @var resource
    */
   protected $fh;

   /**
    * Create a new log reader
    *
    * @param string $path The absolute path to the log file to read
    */
   public function __construct($path)
   {
      if (!is_string($path)) {
         throw new InvalidArgumentException(
            "invalid value provided for 'path'; ".
            "expecting an absolute file path as string"
         );
      }
      else if (($test = File::isReadable($path)) !== true) {
         throw new Exception("failed to create log file reader; $test");
      }
      else {
         $this->path = $path;
         $this->fh = fopen($path, 'r'); 
      }
   }

   /**
    * If a file is open for reading, close it
    */
   public function __destruct()
   {
      if ($this->fh) {
         fclose($this->fh);
      }
   }

   /**
    * {@inheritdoc}
    */
   public function count()
   {
      $pos = ftell($this->fh);
      $ret = 0;
      while (!feof($this->fh)) {
         $buffer = fread($this->fh, 8192);
         $ret += substr_count($buffer, "\n");
      }
      fseek($this->fh, $pos);
      return $ret;
   }

   /**
    * Set the read offset
    *
    * @param integer $offset The number of lines to offset the reader to
    * @return boolean Whether or not records exist after the offset
    * @throws InvalidArgumentException If $offset is not an integer
    */
   public function setOffset($offset = 0)
   {
      if (!is_int($offset)) {
         throw new InvalidArgumentException(
            "invalid value provided for 'offset'; ".
            "expecting an integer"
         );
      }

      fseek($this->fh, 0);
      $line = 0;
      
      while (
         $line < $offset &&
         ($eof = feof($this->fh)) === false && 
         fgets($this->fh) !== false
      ) {
         $line++;
      }

      return (feof($this->fh) === false);
   }

   /**
    * {@inheritdoc}
    */
   public function read()
   {
      if (feof($this->fh) || ($line = fgets($this->fh)) === false) {
         return null;
      }

      return LogFile::decodeRecord($line);
   }
}

