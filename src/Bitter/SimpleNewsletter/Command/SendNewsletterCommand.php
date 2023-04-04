<?php

namespace Bitter\SimpleNewsletter\Command;

use Concrete\Core\Foundation\Command\Command;

class SendNewsletterCommand extends Command
{
    protected $sendQueueItemId;

    public function __construct(int $sendQueueItemId)
    {
        $this->sendQueueItemId = $sendQueueItemId;
    }

    /**
     * @return int
     */
    public function getSendQueueItemId(): int
    {
        return $this->sendQueueItemId;
    }

    /**
     * @param int $sendQueueItemId
     * @return SendNewsletterCommand
     */
    public function setSendQueueItemId(int $sendQueueItemId): SendNewsletterCommand
    {
        $this->sendQueueItemId = $sendQueueItemId;
        return $this;
    }
}
