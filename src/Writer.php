<?php

namespace sndsgd\log;


/**
 * A base class for log writers
 */
abstract class Writer
{
   /**
    * A log record
    *
    * @var sndsgd\log\Record
    */
   protected $record;

   /**
    * Create a new writer instance
    *
    * @param sndsgd\log\Record $record
    */
   public function __construct(Record $record)
   {
      $this->record = $record;
   }

   /**
    * Write the record to a log
    * 
    * @return void
    */
   abstract public function write();
}

