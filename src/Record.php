<?php

namespace sndsgd\log;

use \InvalidArgumentException;
use \sndsgd\Date;


/**
 * A representation of a single record within a collection of records
 */
class Record
{
   use \sndsgd\data\Manager;

   /**
    * Convenience method to create a log
    *
    * @param string $name The log name
    * @param string $message The log message content
    * @return sndsgd\Log
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
   protected $name = "error";

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
   public function __construct()
   {
      $this->timestamp = microtime(true);
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
    * Get a formatted version of the timestamp using `DateTime::format()`
    * 
    * @return string
    */
   public function getDate($fmt = "D, d M Y H:i:s.u O")
   {
      $dt = Date::create($this->timestamp);
      return $dt->format($fmt);
   }

   /**
    * @param string $name The name for the log 
    * @return \sndsgd\log\Record this object instance
    */
   public function setName($name)
   {
      if (
         !is_string($name) ||
         preg_match('/[^a-z0-9-_]/i', $name) === 1
      ) {
         throw new InvalidArgumentException(
            "invalid value provided for 'name'; expecting a string that ".
            "contains only letters, numbers, underscore (_), ".
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
      foreach (func_get_args() as $writer) {
         if (is_string($writer)) {
            if (!class_exists($writer)) {
               throw new InvalidArgumentException(
                  "invalid value provided for 'writer'; expecting a subclass ".
                  "of sndsgd\log\Writer as string"
               );
            }
            $writer = new $writer;
         }
         else if (($writer instanceof Writer) === false) {
            throw new InvalidArgumentException(
               "invalid value provided for 'writer'; expecting either and ".
               "instance of sndsgd\log\Writer or a subclass name as string"
            );
         }

         $writer->setRecord($this);
         $writer->write();
      }
   }
}

