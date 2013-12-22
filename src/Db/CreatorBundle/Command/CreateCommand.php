<?php

namespace Db\CreatorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Description of CreateCommand
 *
 * @author Ayyoub
 */
class CreateCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('db:create')
                ->setDescription('Creates the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->prepareDb($output);
        
        $output->writeln('Preparing the SQL query for FreePatentsOnline...');
        $creator = $this->getContainer()->get('db_creator.creator');
        $creator->setXmlFilePath('/home/aybbou/Bureau/result.xml');
        $creator->setConfig(array(
            'bundle'=>'Creator',
            'patent'=>'patent',
            'idTag'=>true,
            'title'=>'title',
            'abstract'=>'abstract',
            'pubDate'=>'publication_date',
            'filDate'=>'filing_date',
            'inventor'=>'inventor',
            'name'=>'name',
            'country'=>'country',
            'applicant'=>'assignee'
        ));
        $creator->createDb();
        
        $output->writeln('Preparing the SQL query for PatentLens...');
        $lensCreator = $this->getContainer()->get('db_creator.creator');
        $lensCreator->setXmlFilePath('/home/aybbou/Bureau/results.xml');
        $lensCreator->setConfig(array(
            'bundle'=>'Lens',
            'patent'=>'patent',
            'idTag'=>false,
            'title'=>'Title',
            'abstract'=>'Abstract',
            'pubDate'=>'PublicationDate',
            'filDate'=>'FilingDate',
            'inventor'=>'ApplicantAndInventor',
            'name'=>'FullName',
            'country'=>'Country',
            'applicant'=>'ApplicantAndInventor'
        ));
        $lensCreator->createDb();
        

        $output->writeln('Done! Have a nice day !');
    }

    protected function prepareDb($output) {
        $drop = $this->getApplication()->find('doctrine:schema:drop');
        $arguments = array('command' => 'doctrine:schema:drop', '--force' => true);
        $input = new ArrayInput($arguments);
        $returnCode = $drop->run($input, $output);
        $update = $this->getApplication()->find('doctrine:schema:update');
        $arguments = array('command' => 'doctrine:schema:update', '--force' => true);
        $input = new ArrayInput($arguments);
        $returnCode = $update->run($input, $output);
    }

}
