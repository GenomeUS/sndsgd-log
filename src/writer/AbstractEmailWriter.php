<?php

namespace sndsgd\log\writer;

use \Exception;
use \InvalidArgumentException;
use \sndsgd\Config;
use \sndsgd\Json;


/**
 * A base class for email writers
 */
abstract class AbstractEmailWriter extends \sndsgd\log\Writer
{
   /**
    * The email sender (overrides config value)
    * 
    * @var string
    */
   protected $sender;

   /**
    * The email recipient (overrides config value)
    * 
    * @var string
    */
   protected $recipient;

   /**
    * The email replyto address (overrides config value)
    * 
    * @var string
    */
   protected $replyTo;

   /**
    * The email returnpath address (overrides config value)
    * 
    * @var string
    */
   protected $returnPath;


   /**
    * The email subject
    * 
    * @var string
    */
   protected $subject;

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

   /**
    * @param string $email
    * @return string
    * @throws InvalidArgumentException If the email is not valid
    */
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
    * 
    * @param string $subject
    */
   public function setSubject($subject)
   {
      $this->subject = $subject;
   }

   protected function getSender()
   {
      return ($this->sender !== null) 
         ? $this->sender
         : Config::getRequired("sndsgd.log.writer.email.senderAddress");
   }

   protected function getRecipient()
   {
      return ($this->recipient !== null) 
         ? $this->recipient
         : Config::getRequired("sndsgd.log.writer.email.recipientAddress");
   }

   protected function getReplyTo()
   {
      return ($this->replyTo !== null) 
         ? $this->replyTo
         : Config::getRequired("sndsgd.log.writer.email.replyToAddress");
   }

   protected function getReturnPath()
   {
      return ($this->returnPath !== null) 
         ? $this->returnPath
         : Config::getRequired("sndsgd.log.writer.email.returnPathAddress");
   }

   protected function getSubject()
   {
      return ($this->subject !== null)
         ? $this->subject
         : "new log record: ".$this->record->getName();
   }



   /**
    * Create an email body from the record
    * 
    * @return string
    */
   protected function createEmailBody()
   {
      $ret = 
         "date: ".$this->record->getDate()."\n".
         'message: '.$this->record->getMessage()."\n\n".
         'data: '.json_encode($this->record->getData(), Json::HUMAN)."\n";

      return $ret."\n\nsha1: ".sha1($ret)."\n";
   }
}

