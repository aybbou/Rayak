<?php

namespace Db\CreatorBundle\Creator;

use Db\CreatorBundle\Entity\Patent;
use Db\CreatorBundle\Entity\Applicant;
use Db\CreatorBundle\Entity\Inventor;
use Db\CreatorBundle\Entity\Country;
use Doctrine\ORM\EntityManager;

/**
 * Description of Creator
 *
 * @author Ayyoub
 */
class Creator {

    private $xmlFilePath;
    protected $em;

    public function setXmlFilePath($xmlFilePath) {
        $this->xmlFilePath = $xmlFilePath;
    }

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function createDb() {
        $xml = new \DOMDocument();
        $xml->load($this->xmlFilePath);

        $patents = $xml->getElementsByTagName('patent');

        $c = 1;
        foreach ($patents as $patent) {
            //echo "Patent : $c\n";
            $this->addPatent($patent);
            $c++;
            if ($c > 250) {
                //break;
            }
        }
        echo "Executing the SQL query...\n";
        $this->em->flush();
    }

    protected function addPatent($patent) {

        $id = (integer) $patent->getElementsByTagName('id')->item(0)->nodeValue;
        $title = $patent->getElementsByTagName('title')->item(0)->nodeValue;
        try {
            $abstract = $patent->getElementsByTagName('abstract')->item(0)->nodeValue;
        } catch (\Exception $e) {
            $abstract = '';
        }
        $pubDate = $patent->getElementsByTagName('publication_date')->item(0)->nodeValue;
        $filDate = $patent->getElementsByTagName('filing_date')->item(0)->nodeValue;

        $inventors = $this->getInventors($patent);
        $applicants = $this->getApplicants($patent);

        $patent = $this->em->getRepository('DbCreatorBundle:Patent')->find($id);
        if ($patent == null) {
            $patent = new Patent();
            $patent->setId($id);
            $patent->setAbstract($abstract);
            $patent->setTitle($title);
            $patent->setPublicationDate(new \DateTime($pubDate));
            $patent->setFillingDate(new \DateTime($filDate));

            foreach ($inventors as $inventor) {
                if (!$patent->getInventors()->contains($inventor)) {
                    $patent->addInventor($inventor);
                }
            }

            foreach ($applicants as $applicant) {
                if (!$patent->getApplicants()->contains($applicant)) {
                    $patent->addApplicant($applicant);
                }
            }

            $this->em->persist($patent);
        }
    }

    protected function getInventors($patent) {
        $inventorsTab = array();

        $inventors = $patent->getElementsByTagName('inventor');
        foreach ($inventors as $inventor) {
            $inventorsTab[] = $this->getInventor($inventor);
        }
        return $inventorsTab;
    }

    protected function getInventor($inventor) {
        $name = $inventor->getElementsByTagName('name')->item(0)->nodeValue;
        $countryCode = $inventor->getElementsByTagName('country')->item(0)->nodeValue;
        $countryCode= trim(strtolower($countryCode));
        
        $inventor = $this->em->getRepository('DbCreatorBundle:Inventor')->find($name);
        if ($inventor == null) {
            $inventor = new Inventor();
            $inventor->setFullName($name);
        }

        $country = $this->em->getRepository('DbCreatorBundle:Country')->find($countryCode);
        if ($country == null) {
            $country = new Country();
            $country->setName($countryCode);
            $country->setCode($countryCode);
        }

        if (!$country->getInventors()->contains($inventor)) {
            $country->addInventor($inventor);
        }

        $inventor->setCountry($country);

        $this->em->persist($country);
        $this->em->persist($inventor);

        return $inventor;
    }

    protected function getApplicants($patent) {
        $applicantsTab = array();

        $applicants = $patent->getElementsByTagName('assignee');
        foreach ($applicants as $applicant) {
            $applicantsTab[] = $this->getApplicant($applicant);
        }
        return $applicantsTab;
    }

    protected function getApplicant($applicant) {
        $name = strtolower($applicant->getElementsByTagName('name')->item(0)->nodeValue);
        $countryCode = $applicant->getElementsByTagName('country');
        if (is_object($countryCode)) {
            $countryCode = $countryCode->item(0);
            if (is_object($countryCode)) {
                $countryCode = $countryCode->nodeValue;
            } else {
                $countryCode = 'NN';
            }
        } else {
            $countryCode = 'NN';
        }

        if (strlen($countryCode) > 3) {
            $countryCode = 'NN';
        }
        
        $countryCode= trim(strtolower($countryCode));

        $country = $this->em->getRepository('DbCreatorBundle:Country')->find($countryCode);
        if ($country == null) {
            $country = new Country();
            $country->setName($countryCode);
            $country->setCode($countryCode);
        }

        $applicant = $this->em->getRepository('DbCreatorBundle:Applicant')->find($name);
        if ($applicant == null) {
            $applicant = new Applicant();
            $applicant->setFullName($name);
            $applicant->setCountry($country);
        }

        if (!$country->getApplicants()->contains($applicant)) {
            $country->addApplicant($applicant);
        }

        $this->em->persist($country);
        $this->em->persist($applicant);

        return $applicant;
    }

}
