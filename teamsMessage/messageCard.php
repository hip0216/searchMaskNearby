<?php

namespace SmallFreshMeat\Teams;

require 'vendor/autoload.php';

use Sebbmyr\Teams\AbstractCard as Card;

class MessageCard extends Card
{
    private $messageTitle;
    private $messageContent;
    public function __construct(string $messageTitle, string $messageContent)
    {
        $this->messageTitle = $messageTitle;
        $this->messageContent = $messageContent;
    }
    public function getMessage()
    {
        return [
            "@context" => "http://schema.org/extensions",
            "@type" => "MessageCard",
            "themeColor" => !empty($this->data['themeColor']) ? $this->data['themeColor'] : "0072C6",
            "title" => $this->messageTitle,
            "text" => $this->messageContent
        ];
    }
}
