<?php

namespace sndsgd\log\mailgun;

use \Exception;
use \Mailgun\Mailgun;
use \sndsgd\util\Config;
use \sndsgd\util\Json;


/**
 * A log writer for Mailgun
 * 
 * @see [http://www.mailgun.com/](http://www.mailgun.com/)
 */
class Writer extends \sndsgd\log\Writer
{
   /**
    * {@inheritdoc}
    */
   public function write()
   {
      $cfg = Config::getAs([
         'sndsgd.log.writer.mailgun.apiKey' => 'apikey',
         'sndsgd.log.writer.mailgun.domain' => 'domain',
         'sndsgd.log.writer.mailgun.senderAddress' => 'sender',
         'sndsgd.log.writer.mailgun.recipientAddress' => 'recipient'
      ]);

      if (!is_array($cfg)) {
         throw new Exception("failed to email log record; $cfg");
      }

      $mailgun = new Mailgun($cfg['apikey']);
      return $this->sendMessage($mailgun, $cfg);
   }

   /**
    * Send the message
    *
    * Note: this method exists, and is public so it can be mocked
    * @param Mailgun $mailgun
    * @param array.<string,string> $cfg Config values for Mailgun
    * @return boolean
    * @throws Exception If the email could not be sent
    */
   public function sendMessage(Mailgun $mailgun, $cfg)
   {
      return $mailgun->sendMessage($cfg['domain'], [
         'from' => $cfg['sender'],
         'to' => $cfg['recipient'],
         'subject' => 'new log record: '.$this->record->getName(),
         'text' => $this->createEmailBody()
      ]);
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

