<?php

namespace App\Command;

use App\Entity\Rig;
use App\Entity\Wallet;
use App\Enum\RigStatus;
use App\Model\TelegramBot;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class WalletBalanceUpdateCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'wallet:update';

    protected function configure()
    {
        $this
            ->setDescription('Updates wallet balances')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var Wallet[] $wallets */
        $wallets = $em->getRepository('App:Wallet')->findAll();
        foreach($wallets as $wallet)
        {
            $wallet->updateBalance();
            $em->persist($wallet);
            $output->writeln($wallet->getName() . ': ' . $wallet->getOldBalance() . ' -> ' . $wallet->getBalance());
        }

        $em->flush();
    }
}
