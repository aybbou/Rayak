<?php

namespace Db\MainBundle\Tests\Utility;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Db\MainBundle\Utility\KeywordsExtractor;
use Db\CreatorBundle\Entity\Inventor;
use Db\CreatorBundle\Entity\Patent;

class KeywordsExtractorTest extends WebTestCase
{

    private $extractor;

    public function setUp()
    {
        $this->extractor = new KeywordsExtractor();
    }

    public function tearDown()
    {
        unset($this->extractor);
    }

    /**
     * @dataProvider getKeywordsFromPatentsData
     */
    public function testGetKeywordsFromPatents($patents, $result, $n = null)
    {
        $this->assertEquals($this->extractor->getKeywordsFromPatents($patents, $n), $result);
    }

    public function getKeywordsFromPatentsData()
    {
        $p1 = new Patent();
        $p1->setTitle('this is a title for a patent about rfid so yeah RFID (rfID)');
        $p2 = new Patent();
        $p2->setTitle('this is an other patent about RfId');
        $p3 = new Patent();
        $p3->setTitle('rfid rfid rfid rfid rfid is rfid Is RfId is');
        $patents = array($p1, $p2, $p3);
        return array(
            array(array(), array()),
            array($patents, array(array('keyword' => 'rfid', 'count' => 11)))
        );
    }

    /**
     * @dataProvider getKeywordsOfInventorData
     */
    public function testGetKeywordsOfInventor($inventor, $result, $number = 20)
    {
        $this->assertEquals($this->extractor->getKeywordsOfInventor($inventor, $number), $result);
    }

    public function getKeywordsOfInventorData()
    {
        $inv1 = new Inventor();

        $inv2 = new Inventor();

        $p1 = new Patent();
        $p1->setTitle('this is a title for a patent about rfid so yeah RFID');
        $p2 = new Patent();
        $p2->setTitle('this is an other patent about RfId');
        $inv2->addPatent($p1);
        $inv2->addPatent($p2);
        $inv2->setFullName('FULL Name');

        return array(
            array($inv1, array('fullName' => null, 'keywords' => array(), 'counts' => array())),
            array($inv2, array('fullName' => 'FULL Name', 'keywords' => array(
                        0 => 'rfid',
                        1 => 'this',
                        2 => 'about',
                        3 => 'patent',
                        4 => 'other',
                        5 => 'title',
                        6 => 'yeah'
                    ), 'counts' => array(
                        0 => 3,
                        1 => 2,
                        2 => 2,
                        3 => 2,
                        4 => 1,
                        5 => 1,
                        6 => 1
                    )))
        );
    }

}
