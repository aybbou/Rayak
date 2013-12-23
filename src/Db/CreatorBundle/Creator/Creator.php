<?php

namespace Db\CreatorBundle\Creator;

use Doctrine\ORM\EntityManager;

/**
 * Description of Creator
 *
 * @author Ayyoub
 */
class Creator
{

    private $xmlFilePath;
    protected $em;
    private $config;

    public function setXmlFilePath($xmlFilePath)
    {
        $this->xmlFilePath = $xmlFilePath;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
        if( !isset($config['bundle'], $config['patent'], $config['title'], $config['abstract'], $config['pubDate'], $config['filDate'], $config['inventor'], $config['name'], $config['country'], $config['applicant']) ) {
            throw new \Exception('A parameter is messing !');
        }
    }

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createDb()
    {
        $xml = new \DOMDocument();
        $xml->load($this->xmlFilePath);

        $patentTag = $this->config['patent'];

        $patents = $xml->getElementsByTagName($patentTag);

        foreach ($patents as $patent) {
            $this->addPatent($patent);
        }
        echo "Executing the SQL query...\n";
        $this->em->flush();
    }

    protected function addPatent($patent)
    {
        $idTag = $this->config['idTag'];
        $abstractTag = $this->config['abstract'];
        $titleTag = $this->config['title'];
        $pubDateTag = $this->config['pubDate'];
        $filDateTag = $this->config['filDate'];
        $bundle=  $this->config['bundle'];

        if ($idTag) {
            $id = (integer) $patent->getElementsByTagName('id')->item(0)->nodeValue;
        } else {
            $id = (integer) $patent->getAttribute('num');
        }

        $title = $patent->getElementsByTagName($titleTag)->item(0)->nodeValue;

        $abstract = $patent->getElementsByTagName($abstractTag)->item(0);
        if ($abstract) {
            $abstract = $abstract->nodeValue;
        } else {
            $abstract = '';
        }
        $pubDate = $patent->getElementsByTagName($pubDateTag)->item(0)->nodeValue;
        $filDate = $patent->getElementsByTagName($filDateTag)->item(0)->nodeValue;

        $inventors = $this->getInventors($patent);
        $applicants = $this->getApplicants($patent);

        $patent = $this->em->getRepository('Db'.$bundle.'Bundle:Patent')->find($id);
        if ($patent == null) {
            $patentClass = "\\Db\\$bundle"."Bundle\\Entity\\Patent";
            $patent = new $patentClass();
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

    protected function getInventors($patent)
    {
        $inventorsTab = array();
        $inventorTag = $this->config['inventor'];
        $inventors = $patent->getElementsByTagName($inventorTag);
        foreach ($inventors as $inventor) {
            $inventorsTab[] = $this->getInventor($inventor);
        }
        return $inventorsTab;
    }

    protected function getInventor($inventor)
    {
        $nameTag = $this->config['name'];
        $countryTag = $this->config['country'];
        $bundle=  $this->config['bundle'];
        $name = $inventor->getElementsByTagName($nameTag)->item(0)->nodeValue;
        $countryCode = $inventor->getElementsByTagName($countryTag)->item(0)->nodeValue;
        $countryCode = trim(strtolower($countryCode));

        $inventor = $this->em->getRepository('Db'.$bundle.'Bundle:Inventor')->find($name);
        if ($inventor == null) {
            $inventorClass="\\Db\\$bundle"."Bundle\\Entity\\Inventor";
            $inventor = new $inventorClass();
            $inventor->setFullName($name);
        }

        $country = $this->em->getRepository('Db'.$bundle.'Bundle:Country')->find($countryCode);
        if ($country == null) {
            $countryClass= "\\Db\\$bundle"."Bundle\\Entity\\Country";
            $country = new $countryClass();
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

    protected function getApplicants($patent)
    {
        $applicantsTab = array();
        $applicantTag = $this->config['applicant'];
        $applicants = $patent->getElementsByTagName($applicantTag);
        foreach ($applicants as $applicant) {
            $applicantsTab[] = $this->getApplicant($applicant);
        }
        return $applicantsTab;
    }

    protected function getApplicant($applicant)
    {
        $nameTag = $this->config['name'];
        $countryTag = $this->config['country'];
        $bundle=  $this->config['bundle'];
        $name = strtolower($applicant->getElementsByTagName($nameTag)->item(0)->nodeValue);
        $countryCode = $applicant->getElementsByTagName($countryTag);
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

        $countryCode = trim(strtolower($countryCode));

        $country = $this->em->getRepository('Db'.$bundle.'Bundle:Country')->find($countryCode);
        if ($country == null) {
            $countryClass= "\\Db\\$bundle"."Bundle\\Entity\\Country";
            $country = new $countryClass();
            $country->setName($countryCode);
            $country->setCode($countryCode);
        }

        $applicant = $this->em->getRepository('Db'.$bundle.'Bundle:Applicant')->find($name);
        if ($applicant == null) {
            $applicantClass= "\\Db\\$bundle"."Bundle\\Entity\\Applicant";
            $applicant = new $applicantClass();
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
