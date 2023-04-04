<?php

namespace Bitter\SimpleNewsletter\Command;


use Bitter\SimpleNewsletter\Service\Sender;

class SendNewsletterCommandHandler
{
    protected $sender;

    public function __construct(
        Sender $sender
    )
    {
        $this->sender = $sender;
    }

    public function __invoke(SendNewsletterCommand $command)
    {
        $this->sender->sendMail($command->getSendQueueItemId());
    }
}
