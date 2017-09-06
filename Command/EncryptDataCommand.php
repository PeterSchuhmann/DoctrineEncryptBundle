<?php

namespace Resomedia\DoctrineEncryptBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EncryptDataCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('doctrine:encrypt:data')
            ->setDescription('Encrypt data in argument')
            ->addArgument("data", InputArgument::REQUIRED, 'The data you want to encrypt')
            ->addArgument('encryptorclass', InputArgument::OPTIONAL, 'The encryptor you want to decrypt the data with');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $encryptor = $this->getContainer()->get('resomedia_doctrine_encrypt.encryptor');

        //If encryptor has been set use that encryptor else use default
        if($input->getArgument('encryptorclass') && class_exists($input->getArgument('encryptorclass'))) {
            $encryptor->setEncryptor($input->getArgument('encryptorclass'));
        }

        $output->writeln('Data : ' . $input->getArgument('data'));
        $output->writeln('Encrypt value : ' . $encryptor->encrypt($input->getArgument('data')));
    }
}