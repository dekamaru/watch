<?php

namespace App\Command;

use App\Entity\Rig;
use App\Enum\RigStatus;
use App\Model\TelegramBot;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RigAlertCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'rig:alert';

    private $failMessage = 'Следующие фермы не работают:' . PHP_EOL;
    private $recoverMessage = 'Подключение восстановлено у следующих ферм:' . PHP_EOL;
    /** @var Rig[] */
    private $failed = [];
    /** @var Rig[] */
    private $recovered = [];

    protected function configure()
    {
        $this
            ->setDescription('Checks rigs')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var Rig[] $rigs */
        $rigs = $em->getRepository('App:Rig')->findAll();
        foreach($rigs as $rig)
        {
            $rigStatus = $rig->getStatus();

            $status = $rig->checkAvailability();
            if (!$status)
            {
                if ($rigStatus == RigStatus::WORKING)
                {
                    $this->failed[] = $rig;
                }
                $output->writeln($rig->getName() . ' is failed');
            }
            else
            {
                if ($rigStatus == RigStatus::NOT_WORKING)
                {
                    $this->recovered[] = $rig;
                }

                $em->persist($rig);
                $output->writeln($rig->getName() . ' success');
            }
        }

        $em->flush();

        if (count($this->failed) > 0)
        {
            foreach($this->failed as $k => $rig)
            {
                $lastSeen = $rig->getLastSeen() === null ? '-' : $rig->getLastSeen()->format('d.m.Y H:i:s');
                $this->failMessage .= ($k + 1) . '. *' . $rig->getName() . '*, последний отклик ('. $lastSeen .')' . PHP_EOL;
            }

            TelegramBot::sendMessage('203820', $this->failMessage);
            TelegramBot::sendMessage('196110799', $this->failMessage);
        }

        if (count($this->recovered) > 0)
        {
            foreach($this->recovered as $k => $rig)
            {
                $this->recoverMessage .= ($k + 1) . '. *' . $rig->getName() . '*'. PHP_EOL;
            }

            TelegramBot::sendMessage('203820', $this->recoverMessage);
            TelegramBot::sendMessage('196110799', $this->recoverMessage);
        }
    }
}
