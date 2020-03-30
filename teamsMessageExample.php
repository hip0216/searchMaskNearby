<?php

require 'vendor/autoload.php';
use Sebbmyr\Teams\TeamsConnector;

// you should add your own token in token.php
require 'token.php';
require 'teamsMessage/messageCard.php';
require 'teamsMessage/parseArrayToString.php';
use SmallFreshMeat\Teams\MessageCard;
use function SmallFreshMeat\parseArrayToString;

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

$webhook = TEAMS_WEBHOOK_TOKEN;
$messageTitle = "查詢結果";
$messageContent = parseArrayToString($outPutDatas);
// echo $messageContent;

$connector = new TeamsConnector($webhook);
$card = new MessageCard($messageTitle, $messageContent);
$connector->send($card);
