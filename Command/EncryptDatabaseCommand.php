<?php

namespace Resomedia\DoctrineEncryptBundle\Command;

use Resomedia\DoctrineEncryptBundle\DependencyInjection\DoctrineEncryptExtension;
use Resomedia\DoctrineEncryptBundle\Subscribers\DoctrineEncryptSubscriber;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
/**
 * Batch encryption for the database
 *
 * @author Jérémy Pasini
 * @author Marcel van Nuil <marcel@ambta.com>
 * @author Michael Feinbier <michael@feinbier.net>
 */
class EncryptDatabaseCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('doctrine:encrypt:database')
            ->setDescription('Encrypt whole database on tables which are not encrypted yet')
            ->addArgument('encryptorclass', InputArgument::OPTIONAL, 'The encryptor you want to decrypt the database with')
            ->addArgument('batchSize', InputArgument::OPTIONAL, 'The number of row to update/flush', 200);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Get entity manager, question helper, subscriber service and annotation reader
        $question = $this->getHelper('question');
        $batchSize = $input->getArgument('batchSize');

        //If encryptor has been set use that encryptor else use default
        if($input->getArgument('encryptorclass') && class_exists($input->getArgument('encryptorclass'))) {
            $this->subscriber->setEncryptor($input->getArgument('encryptorclass'));
        }

        //Get entity manager metadata
        $metaDataArray = $this->getEncryptionableEntityMetaData();
        $confirmationQuestion = new ConfirmationQuestion(
            "<question>\n" . count($metaDataArray) . " entities found which are containing properties with the encryption tag.\n\n" .
            "Which are going to be encrypted with [" . $this->subscriber->getEncryptor() . "]. \n\n".
            "Wrong settings can mess up your data and it will be unrecoverable. \n" .
            "I advise you to make <bg=yellow;options=bold>a backup</bg=yellow;options=bold>. \n\n" .
            "Continue with this action? (y/yes)</question>", false
        );

        if (!$question->ask($input, $output, $confirmationQuestion)) {
            return;
        }

        //Start decrypting database
        $output->writeln("\nEncrypting all fields can take up to several minutes depending on the database size.");

        //desactivate subscriber
        $searchedListener = null;
        foreach ($this->entityManager->getEventManager()->getListeners() as $event => $listeners) {
            foreach ($listeners as $key => $listener) {
                if ($listener instanceof DoctrineEncryptSubscriber) {
                    $searchedListener = $listener;
                    break 2;
                }
            }
        }
        if ($searchedListener) {
            $evm = $this->entityManager->getEventManager();
            $evm->removeEventListener(array('preUpdate', 'postUpdate', 'postLoad', 'preFlush', 'postFlush'), $searchedListener);
        }

        //Loop through entity manager meta data
        foreach($metaDataArray as $metaData) {
            $i = 1;
            $iterator = $this->getEntityIterator($metaData->name);
            $totalCount = $this->getTableCount($metaData->name);
            $output->writeln(sprintf('Processing <comment>%s</comment>', $metaData->name));
            $progressBar = new ProgressBar($output, $totalCount);
            foreach ($iterator as $row) {
                $this->subscriber->processFields($row[0]);
                if (($i % $batchSize) === 0) {
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                    $progressBar->advance($batchSize);
                }
                $i++;
            }
            $progressBar->finish();
            $output->writeln('');
            $this->entityManager->flush();
        }

        //Say it is finished
        $output->writeln("\nEncryption finished. Values encrypted: <info>" . $this->subscriber->encryptCounter . " values</info>.\nAll values are now encrypted.");
    }
}