<?php

namespace sndsgd\log\writer;

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
class MailgunWriter extends AbstractEmailWriter
{
   /**
    * {@inheritdoc}
    */
   public function write()
   {
      $apikey = Config::getRequired("mailgun.apiKey");
      return $this->sendMessage(
         new Mailgun($apikey),
         $this->getSender(),
         $this->getRecipient(),
         $this->getSubject(),
         $this->createEmailBody()
      );
   }

   /**
    * Send the message
    *
    * Note: this method exists, and is public so it can be mocked
    * @param Mailgun $mailgun
    * @param array<string,string> $cfg Config values for Mailgun
    * @return boolean
    * @throws Exception If the email could not be sent
    */
   public function sendMessage(Mailgun $mailgun, $from, $to, $subject, $text)
   {
      $domain = Config::getRequired("mailgun.domain");
      return $mailgun->sendMessage($domain, [
         "from" => $from,
         "to" => $to,
         "subject" => $subject,
         "text" => $text
      ]);
   }
}

