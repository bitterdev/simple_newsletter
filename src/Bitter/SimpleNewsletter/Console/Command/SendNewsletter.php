<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 */

namespace Bitter\SimpleNewsletter\Console\Command;

use Bitter\SimpleNewsletter\Service\Sender;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SendNewsletter extends Command
{
    protected function configure()
    {
        $this
            ->setName('simple-newsletter:send-newsletter')
            ->setDescription(t('Send the queued newsletter campaigns.'));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = Application::getFacadeApplication();
        /** @var Sender $sender */
        $sender = $app->make(Sender::class);

        $io = new SymfonyStyle($input, $output);

        $errorList = $sender->sendAll();

        if (!$errorList->has()) {
            $io->success(t("All newsletter campaigns were sent successfully."));
        } else {
            foreach ($errorList->getList() as $error) {
                $io->error($error->getMessage());
            }
        }
    }
}
