<?php

namespace Db\CreatorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of CreateCommand
 *
 * @author Ayyoub
 * @author Kaoutar
 */
class CreateCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('db:create')
                ->setDescription('Creates the database.')
                ->addArgument('dbname')
                ->addArgument('filePath');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $dbname = $input->getArgument('dbname');
        $filePath = $input->getArgument('filePath');
        if ($dbname == 'fpo') {
            $this->createFpoDb($output, $filePath);
        }

        if ($dbname == 'lens') {
            $this->createLensDb($output, $filePath);
        }

        $output->writeln('Done! Have a nice day !');
    }

    public function createLensDb(OutputInterface $output, $filePath) {
        $output->writeln('Preparing the SQL query for PatentLens...');
        $lensCreator = $this->getContainer()->get('db_creator.creator');
        $lensCreator->setXmlFilePath($filePath);
        $lensCreator->setConfig(array(
            'bundle' => 'Lens',
            'patent' => 'patent',
            'idTag' => false,
            'title' => 'Title',
            'abstract' => 'Abstract',
            'pubDate' => 'PublicationDate',
            'filDate' => 'FilingDate',
            'inventor' => 'ApplicantAndInventor',
            'name' => 'FullName',
            'country' => 'Country',
            'applicant' => 'ApplicantAndInventor'
        ));
        $lensCreator->createDb();
    }

    public function createFpoDb(OutputInterface $output, $filePath) {
        $output->writeln('Preparing the SQL query for FreePatentsOnline...');
        $creator = $this->getContainer()->get('db_creator.creator');
        $creator->setXmlFilePath($filePath);
        $creator->setConfig(array(
            'bundle' => 'Creator',
            'patent' => 'patent',
            'idTag' => true,
            'title' => 'title',
            'abstract' => 'abstract',
            'pubDate' => 'publication_date',
            'filDate' => 'filing_date',
            'inventor' => 'inventor',
            'name' => 'name',
            'country' => 'country',
            'applicant' => 'assignee'
        ));
        $creator->createDb();
    }

}
