<?php

namespace SmallFreshMeat;

class Token
{
    private const TEAMS_WEBHOOK_TOKEN = 'MY_TOKEN';
    public function getTeamsWebhookToken()
    {
        return self::TEAMS_WEBHOOK_TOKEN;
    }
}
