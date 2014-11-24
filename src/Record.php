<?php

namespace sndsgd\log;

use \InvalidArgumentException;


/**
 * A representation of a single record within a log of one or more records
 */
class Record
{
   use \sndsgd\util\data\Manager;

   /**
    * Convenience method to create a log record
    *
    * @param string $name The log name
    * @param string $message The log message content
    * @return sndsgd\log\Record
    */
   public static function create($name, $message)
   {
      return (new Record)
         ->setName($name)
         ->setMessage($message);
   }

   /**
    * The time in milliseconds the log instance was created
    * 
    * @var float
    */
   protected $timestamp;

   /**
    * A human readable name for the log
    * 
    * @var string
    */
   protected $name = 'error';

   /**
    * A human readable message
    * 
    * @var string
    */
   protected $message = null;

   /**
    * Create a log record instance
    *
    * @param float|string $timestamp The timestamp of the log creation
    */
   public function __construct($timestamp = null)
   {
      $this->timestamp = ($timestamp === null)
         ? microtime(true)
         : floatval($timestamp);      
   }

   /**
    * Get the record timestamp
    * 
    * @return float
    */
   public function getTimestamp()
   {
      return $this->timestamp;
   }

   /**
    * Get a formatted version of the record timestamp using `date`
    * 
    * @return string
    */
   public function getDate($fmt = 'r')
   {
      return date($fmt, floor($this->timestamp));
   }

   /**
    * @param string $name The name for the log 
    * @return sndsgd\log\Record this object instance
    */
   public function setName($name)
   {
      if (
         !is_string($name) ||
         preg_match('/[^a-z0-9-_]/i', $name) === 1
      ) {
         throw new InvalidArgumentException(
            "invalid value provided for 'name'; expecting a string that ".
            "contains only alphanumeric characters, underscore (_), ".
            "and dash (-)"
         );
      }
      $this->name = $name;
      return $this;
   }

   /**
    * @return string
    */
   public function getName()
   {
      return $this->name;
   }

   /**
    * Set the record message
    * 
    * @param string $message The message
    * @return sndsgd\log\Record this log instance
    */
   public function setMessage($message)
   {
      if (!is_string($message)) {
         throw new InvalidArgumentException(
            "invalid value provided for 'message'; expecting a string"
         );
      }

      $this->message = trim(preg_replace('~\s+~', ' ', $message));
      return $this;
   }

   /**
    * Get the record message
    * 
    * @return string
    */
   public function getMessage()
   {
      return ($this->message === null) ? '' : $this->message;
   }

   /**
    * Write the record
    * 
    * @param string $writer,... One or more writer classnames
    */
   public function write()
   {
      foreach (func_get_args() as $class) {
         $writer = new $class($this);
         $writer->write();
      }
   }
}

