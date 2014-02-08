<?php

namespace Db\CreatorBundle\Tests\Creator;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Db\CreatorBundle\Creator\Creator;

class CreatorTest extends WebTestCase
{

    private $creator;

    public function setUp()
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $this->creator = new Creator($em);
    }

    /**
     * @expectedException \Exception
     */
    public function testSetConfigWithMissingParameters()
    {
        $this->creator->setConfig(array());
    }

    /**
     * @dataProvider setConfigData
     */
    public function testSetConfig(array $config)
    {
        $this->creator->setConfig($config);
    }

    public function setConfigData()
    {
        return array(
            array(array(
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
                )),
            array(array(
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
                ))
        );
    }

}
