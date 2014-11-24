<?php

namespace sndsgd\log;


/**
 * A base class for log readers
 */
abstract class Reader
{
   /**
    * Get the number of records in a log
    * 
    * @return integer
    */
   abstract public function count();

   /**
    * Set the read offset
    *
    * @param integer $offset The number of lines to offset the reader to
    * @return boolean Whether or not records exist after the offset
    * @throws InvalidArgumentException If $offset is not an integer
    */
   abstract public function setOffset($offset = 0);

   /**
    * Decode the next record and return it
    * 
    * @return sndsgd\log\Record|null
    * @return sndsgd\log\Record If a record was successfully read
    * @return null If no more records exist
    */
   abstract public function read();
}

