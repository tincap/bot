<?php

namespace tincap\Bot\Request;


use tincap\Bot\Bot;

class RequestCollection
{
    /**
     * @var Bot
     */
    public $bot;

    /**
     * Constructor.
     * @param Bot $bot
     */
    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
    }
}