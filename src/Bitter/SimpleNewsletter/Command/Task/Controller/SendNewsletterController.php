<?php

namespace Bitter\SimpleNewsletter\Command\Task\Controller;

use Bitter\SimpleNewsletter\Command\SendNewsletterCommand;
use Bitter\SimpleNewsletter\Service\Sender;
use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Command\Task\Controller\AbstractController;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\BatchProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Support\Facade\Application;

class SendNewsletterController extends AbstractController
{

    public function getName(): string
    {
        return t('Send Newsletter');
    }

    public function getDescription(): string
    {
        return t('Send the queued newsletter campaigns.');
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        $app = Application::getFacadeApplication();
        /** @var Sender $sender */
        $sender = $app->make(Sender::class);
        $batch = Batch::create(t('Send Newsletter'));

        $sender->start();

        foreach($sender->getQueueItems() as $queueItem) {
            $batch->add(new SendNewsletterCommand($queueItem));
        }

        $sender->finish();

        return new BatchProcessTaskRunner($task, $batch, $input, t('Send Newsletter'));
    }


}
