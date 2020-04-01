<?php

namespace SmallFreshMeat\Teams;

require 'vendor/autoload.php';

use Sebbmyr\Teams\AbstractCard as Card;

/**
 * A Teams message card which contains title and content.
 * 
 * @param string $messageTitle message title of a Teams message card
 * @param string $messageContent message content of a Teams message card
 * 
 * @return object return an object of a Teams message card
 */
class MessageCard extends Card
{
    private $messageTitle;
    private $messageContent;
    public function __construct(string $messageTitle, string $messageContent)
    {
        $this->messageTitle = $messageTitle;
        $this->messageContent = $messageContent;
    }
    /**
     * Just for implementing the abstract method in need.
     * Therefore, you won't need to use it at all.
     */
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
