<?php

namespace sndsgd\log;

use \InvalidArgumentException;


/**
 * A represenation of a single record within a log of one or more records
 */
class Record
{
   /**
    * A human readable name for the log
    * 
    * @var string
    */
   protected $name = 'error';

   /**
    * The time in milliseconds the log instance was created
    * 
    * @var float
    */
   protected $timestamp;

   /**
    * A human readable message
    * 
    * @var string
    */
   protected $message = null;

   /**
    * Data to include with the record
    * 
    * @var array.<string,mixed>
    */
   protected $data = [];

   /**
    * Create a new message instance
    * 
    * @param string|null $message
    */
   public function __construct($message = null)
   {
      $this->timestamp = microtime(true);
      if ($message !== null) {
         $this->setMessage($message);
      }
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

      $this->message = preg_replace(
         ["','", "/\s+/"],
         ["', '", ' '],
         $message
      );

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
    * @param string $name - a custom name for the log
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
            "contains only use alphanumeric characters, underscore (_), ".
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
    * Add data to the record
    * 
    * @param array.<string,mixed>|string $key
    * @param mixed $value
    * @return sndsgd\log\Record
    */
   public function addData($key, $value = null)
   {
      if (is_string($key) && $value !== null) {
         $this->data[$key] = $value;
      }
      else if (is_array($key) && $value === null) {
         foreach ($key as $k => $v) {
            $this->data[$k] = $v;
         }
      }
      else {
         throw new InvalidArgumentException(
            "invalid arguments provided; expecting either a single array ".
            "of data, or a separate key and value"
         );
      }

      return $this;
   }

   /**
    * Get the record data
    * 
    * @return array.<string,mixed>|null
    */
   public function getData()
   {
      return (count($this->data) === 0) ? null : $this->data;
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

