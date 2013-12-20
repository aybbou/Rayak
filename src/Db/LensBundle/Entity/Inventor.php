<?php

namespace Db\LensBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Db\LensBundle\Entity\Country;

/**
 * Inventor
 *
 * @ORM\Table(name="inventor2")
 * @ORM\Entity
 */
class Inventor
{

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="fullName", type="string", length=255)
     */
    private $fullName;
    
    /**
    * @ORM\ManyToMany(targetEntity="Patent", mappedBy="inventors")
    */
    private $patents;
    
    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="inventors")
     * @ORM\JoinColumn(name="country_code", referencedColumnName="code")
     */
    private $country;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     * @return Inventor
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    
        return $this;
    }

    /**
     * Get fullName
     *
     * @return string 
     */
    public function getFullName()
    {
        return $this->fullName;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->patents = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add patents
     *
     * @param \Db\LensBundle\Entity\Patent $patents
     * @return Inventor
     */
    public function addPatent(\Db\LensBundle\Entity\Patent $patents)
    {
        $this->patents[] = $patents;
    
        return $this;
    }

    /**
     * Remove patents
     *
     * @param \Db\LensBundle\Entity\Patent $patents
     */
    public function removePatent(\Db\LensBundle\Entity\Patent $patents)
    {
        $this->patents->removeElement($patents);
    }

    /**
     * Get patents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPatents()
    {
        return $this->patents;
    }

    /**
     * Set country
     *
     * @param \Db\LensBundle\Entity\Country $country
     * @return Inventor
     */
    public function setCountry(\Db\LensBundle\Entity\Country $country = null)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return \Db\LensBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }
}