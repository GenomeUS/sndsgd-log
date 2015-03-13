<?php

namespace sndsgd\log\mailgun;

use \Exception;
use \InvalidArgumentException;
use \Mailgun\Mailgun;
use \sndsgd\Config;
use \sndsgd\Json;


/**
 * A log writer for Mailgun
 * 
 * @see [http://www.mailgun.com/](http://www.mailgun.com/)
 */
class Writer extends \sndsgd\log\Writer
{
   /**
    * The email sender (overrides the config sender)
    * 
    * @var string
    */
   protected $sender;

   /**
    * The email recipient (overrides the config recipient)
    * 
    * @var string
    */
   protected $recipient;

   /**
    * The email subject
    * 
    * @var string
    */
   protected $subject;


   /**
    * {@inheritdoc}
    */
   public function write()
   {
      $apikey = Config::getRequired("sndsgd.log.writer.mailgun.apiKey");
      return $this->sendMessage(
         new Mailgun($apikey),
         $this->getSender(),
         $this->getRecipient(),
         $this->getSubject(),
         $this->createEmailBody()
      );
   }

   /**
    * Override the sender address specified in the config
    * 
    * @param string $email
    */
   public function setSender($email)
   {
      $this->sender = $this->validateEmail($email);
   }

   /**
    * Override the recipient address specified in the config
    * 
    * @param string $email
    */
   public function setRecipient($email)
   {
      $this->recipient = $this->validateEmail($email);
   }

   private function validateEmail($email)
   {
      $email = filter_var($email, FILTER_VALIDATE_EMAIL);
      if ($email === false) {
         throw new InvalidArgumentException(
            "invalid value provided for 'email'; ".
            "expecting a valid email address as string"
         );
      }
      return $email;
   }

   /**
    * Set the message subject
    * @param string $subject
    */
   public function setSubject($subject)
   {
      $this->subject = $subject;
   }

   private function getSender()
   {
      return ($this->sender !== null) 
         ? $this->sender
         : Config::getRequired("sndsgd.log.writer.mailgun.senderAddress");
   }

   private function getRecipient()
   {
      return ($this->recipient !== null) 
         ? $this->recipient
         : Config::getRequired("sndsgd.log.writer.mailgun.recipientAddress");
   }

   private function getSubject()
   {
      return ($this->subject !== null)
         ? $this->subject
         : "new log record: ".$this->record->getName();
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
   public function sendMessage(Mailgun $mailgun, $from, $to, $subject, $text)
   {
      $domain = Config::getRequired("sndsgd.log.writer.mailgun.domain");
      return $mailgun->sendMessage($domain, [
         "from" => $from,
         "to" => $to,
         "subject" => $subject,
         "text" => $text
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

