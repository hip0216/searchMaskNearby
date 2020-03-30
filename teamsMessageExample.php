<?php

require 'vendor/autoload.php';
use Sebbmyr\Teams\TeamsConnector;

// You should add your own token in token.php
require 'token.php';
require 'teamsMessage/messageCard.php';
require 'teamsMessage/parseArrayToString.php';
use SmallFreshMeat\Teams\MessageCard;
use function SmallFreshMeat\parseArrayToString;

// Some fake datas for testing
// For the real use, you need to provide datas in this format
$outPutDatas = [
    [
        '醫事機構名稱' => '仁心藥局',
        '醫事機構地址' => '新北市新店區中正路４０２號１樓',
        '成人口罩剩餘數' => 600
    ],
    [
        '醫事機構名稱' => '再生藥局',
        '醫事機構地址' => '新北市新店區北新路３段６５巷９號',
        '成人口罩剩餘數' => 600
    ],
    [
        '醫事機構名稱' => '活元藥局',
        '醫事機構地址' => '新北市新店區民族路４９號１樓',
        '成人口罩剩餘數' => 597
    ]
];

// Require the webhook token
$webhook = TEAMS_WEBHOOK_TOKEN;

// Type your message title here
$messageTitle = "查詢結果";
// Transform the datas into string and make it formatted
$messageContent = parseArrayToString($outPutDatas);

// You can see the result in the command line if you want
// echo $messageContent;

// Set a Teams connect by webhook
$connector = new TeamsConnector($webhook);
// Create a Teams message card
$card = new MessageCard($messageTitle, $messageContent);
// Send the card to Teams' channel
$connector->send($card);
