<?php

namespace sndsgd\log\writer;

use \Exception;
use \Mailgun\Mailgun as MG;
use \sndsgd\log\Writer;
use \sndsgd\util\Config;
use \sndsgd\util\Json;


/**
 * A log writer for Mailgun
 * 
 * @see [http://www.mailgun.com/](http://www.mailgun.com/)
 */
class Mailgun extends Writer
{
   /**
    * {@inheritdoc}
    */
   public function write()
   {
      $emailBody = $this->createEmailBody();

      $cfg = Config::getAs([
         'sndsgd.log.writer.mailgun.apiKey' => 'apikey',
         'sndsgd.log.writer.mailgun.domain' => 'domain',
         'sndsgd.log.writer.mailgun.senderAddress' => 'sender',
         'sndsgd.log.writer.mailgun.recipientAddress' => 'recipient'
      ]);

      if (!is_array($cfg)) {
         throw new Exception("failed to email log record; $cfg");
      }
      
      try {
         $mg = new MG($cfg['apikey']);
         $response = $mg->sendMessage($cfg['domain'], [
            'from' => $cfg['sender'],
            'to' => $cfg['recipient'],
            'subject' => 'new log record: '.$this->record->getName(),
            'text' => $emailBody
         ]);
      }
      catch (Exception $ex) {
         throw new Exception("failed to email log record; ".$ex->getMessage());
      }
   }

   /**
    * Create an email body from the record
    * 
    * @return string
    */
   private function createEmailBody()
   {
      $ret = 
         "timestamp: ".$this->record->getTimestamp()."\n".
         "date: ".$this->record->getDate()."\n".
         'message: '.$this->record->getMessage()."\n\n".
         'data: '.json_encode($this->record->getData(), Json::HUMAN)."\n";

      return $ret."\n\nsha1: ".sha1($ret)."\n";
   }
}

