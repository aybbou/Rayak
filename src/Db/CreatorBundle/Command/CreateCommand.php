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
        $output->writeln('Preparing the SQL query...');

        $creator = $this->getContainer()->get('db_creator.creator');
        $creator->setXmlFilePath('/home/aybbou/Bureau/result.xml');
        $creator->createDb();

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
