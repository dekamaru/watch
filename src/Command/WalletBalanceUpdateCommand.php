<?php

namespace App\Command;

use App\Entity\Rig;
use App\Entity\Wallet;
use App\Enum\RigStatus;
use App\Enum\WalletType;
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
        $differences = [];
        foreach($wallets as $wallet)
        {
            $wallet->updateBalance();
            $em->persist($wallet);

            $diff = floatval(number_format($wallet->getBalance() - $wallet->getOldBalance(), 6));
            if ($diff != 0) {
                $differences[$wallet->getName()] = [
                    'postfix' => WalletType::SHORT_NAMES[$wallet->getType()],
                    'value' => ($diff > 0) ? ('+' . number_format($diff, 6)) : number_format($diff, 6)
                ];
            }

            $output->writeln($wallet->getName() . ': ' . $wallet->getOldBalance() . ' -> ' . $wallet->getBalance());
        }

        if (count($differences) > 0) {
            $k = 1;
            $message = 'Изменения на кошельках:' . PHP_EOL;
            foreach ($differences as $walletName => $diff) {
                $message .= $k . '. *' . $walletName . '* - ' . $diff['value'] . ' ' . $diff['postfix'] . PHP_EOL;
                $k++;
            }

            TelegramBot::sendMessage('203820', $message);
            TelegramBot::sendMessage('196110799', $message);
        }

        $em->flush();
    }
}
