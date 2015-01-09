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
    * Set the log record
    *
    * @param sndsgd\log\Record $record
    */
   public function setRecord(Record $record)
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

