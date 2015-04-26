<?php

namespace sndsgd\log\writer;

use \Aws\Ses\SesClient;
use \sndsgd\Config;


/**
 * A log writer for Amazon SES
 * 
 * @see [http://aws.amazon.com/ses/](http://aws.amazon.com/ses/)
 */
class AmazonSesWriter extends AbstractEmailWriter
{
   public function write()
   {
      $client = $this->getSesClient();
      $options = $this->getEmailOptions();
      $result = $client->sendEmail($options);
   }

   private function getSesClient()
   {
      return SesClient::factory([
         "profile" => Config::getRequired("amazon.aws.ses.profile"),
         "region"  => Config::getRequired("amazon.aws.ses.region")
      ]);
   }

   private function getEmailOptions()
   {
      return [
         /**
          * The sender email address
          * @var string
          */
         "Source" => $this->getSender(),
         
         /**
          * The recipient email addresses
          * @var array<string,array<string>>
          */
         "Destination" => [
            "ToAddresses" => (array) $this->getRecipient(),
            "CcAddresses" => [],
            "BccAddresses" => [],
         ],


         "Message" => [
            "Subject" => [
               /** 
                * The email subject
                * @var string
                */
               "Data" => $this->getSubject(),
               // "Charset" => "UTF8",
            ],
   

            "Body" => [
               "Text" => [
                  // Data is required
                  "Data" => $this->createEmailBody()
                  // "Charset" => "string",
               ],
                  // "Html" => array(
                  //     // Data is required
                  //     "Data" => "string",
                  //     "Charset" => "string",
                  // ),
            ],
         ],

         /**
          * The reply-to email address(es) for the message.
          * @var array<string>
          */
         "ReplyToAddresses" => (array) $this->getReplyTo(),
 
         /**
          * The email address to which bounces and complaints are to be 
          * forwarded when feedback forwarding is enabled
          * @var string
          */
         "ReturnPath" => $this->getReturnPath()
      ];
   }
}

