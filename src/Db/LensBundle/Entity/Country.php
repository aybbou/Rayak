<?php

namespace Db\LensBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Db\LensBundle\Entity\Inventor;

/**
 * Country
 *
 * @ORM\Table(name="country2")
 * @ORM\Entity
 */
class Country
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="code", type="string", length=5)
     */
    private $code;
    
    /**
     * @ORM\OneToMany(targetEntity="Inventor", mappedBy="country")
     */
    private $inventors;
    
    /**
     * @ORM\OneToMany(targetEntity="Applicant", mappedBy="country")
     */
    private $applicants;

    /**
     * Set name
     *
     * @param string $name
     * @return Country
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Country
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->inventors = new \Doctrine\Common\Collections\ArrayCollection();
        $this->applicants = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add inventors
     *
     * @param \Db\LensBundle\Entity\Inventor $inventors
     * @return Country
     */
    public function addInventor(\Db\LensBundle\Entity\Inventor $inventors)
    {
        $this->inventors[] = $inventors;
    
        return $this;
    }

    /**
     * Remove inventors
     *
     * @param \Db\LensBundle\Entity\Inventor $inventors
     */
    public function removeInventor(\Db\LensBundle\Entity\Inventor $inventors)
    {
        $this->inventors->removeElement($inventors);
    }

    /**
     * Get inventors
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInventors()
    {
        return $this->inventors;
    }

    /**
     * Add applicants
     *
     * @param \Db\LensBundle\Entity\Applicant $applicants
     * @return Country
     */
    public function addApplicant(\Db\LensBundle\Entity\Applicant $applicants)
    {
        $this->applicants[] = $applicants;
    
        return $this;
    }

    /**
     * Remove applicants
     *
     * @param \Db\LensBundle\Entity\Applicant $applicants
     */
    public function removeApplicant(\Db\LensBundle\Entity\Applicant $applicants)
    {
        $this->applicants->removeElement($applicants);
    }

    /**
     * Get applicants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getApplicants()
    {
        return $this->applicants;
    }
}