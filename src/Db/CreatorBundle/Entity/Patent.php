<?php

namespace Db\CreatorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Db\CreatorBundle\Entity\Applicant;
use Db\CreatorBundle\Entity\Inventor;

/**
 * Patent
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Db\CreatorBundle\Entity\PatentRepository")
 */
class Patent
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="abstract", type="text")
     */
    private $abstract;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fillingDate", type="date")
     */
    private $fillingDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publicationDate", type="date")
     */
    private $publicationDate;

    /**
     * @ORM\ManyToMany(targetEntity="Applicant", inversedBy="patents")
     * @ORM\JoinTable(name="applicants_patents",
     *      joinColumns={@ORM\JoinColumn(name="patent_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="applicant_id", referencedColumnName="fullName")}
     *      )
     */
    private $applicants;

    /**
     * @ORM\ManyToMany(targetEntity="Inventor", inversedBy="patents")
     * @ORM\JoinTable(name="inventors_patents",
     *      joinColumns={@ORM\JoinColumn(name="patent_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="inventor_id", referencedColumnName="fullName")}
     *      )
     */
    private $inventors;

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
     * Set title
     *
     * @param  string $title
     * @return Patent
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set abstract
     *
     * @param  string $abstract
     * @return Patent
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;

        return $this;
    }

    /**
     * Get abstract
     *
     * @return string
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * Set fillingDate
     *
     * @param  \DateTime $fillingDate
     * @return Patent
     */
    public function setFillingDate($fillingDate)
    {
        $this->fillingDate = $fillingDate;

        return $this;
    }

    /**
     * Get fillingDate
     *
     * @return \DateTime
     */
    public function getFillingDate()
    {
        return $this->fillingDate;
    }

    /**
     * Set publicationDate
     *
     * @param  \DateTime $publicationDate
     * @return Patent
     */
    public function setPublicationDate($publicationDate)
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    /**
     * Get publicationDate
     *
     * @return \DateTime
     */
    public function getPublicationDate()
    {
        return $this->publicationDate;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->applicants = new \Doctrine\Common\Collections\ArrayCollection();
        $this->inventors = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add applicants
     *
     * @param  \Db\CreatorBundle\Entity\Applicant $applicants
     * @return Patent
     */
    public function addApplicant(\Db\CreatorBundle\Entity\Applicant $applicants)
    {
        $this->applicants[] = $applicants;

        return $this;
    }

    /**
     * Remove applicants
     *
     * @param \Db\CreatorBundle\Entity\Applicant $applicants
     */
    public function removeApplicant(\Db\CreatorBundle\Entity\Applicant $applicants)
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

    /**
     * Add inventors
     *
     * @param  \Db\CreatorBundle\Entity\Inventor $inventors
     * @return Patent
     */
    public function addInventor(\Db\CreatorBundle\Entity\Inventor $inventors)
    {
        $this->inventors[] = $inventors;

        return $this;
    }

    /**
     * Remove inventors
     *
     * @param \Db\CreatorBundle\Entity\Inventor $inventors
     */
    public function removeInventor(\Db\CreatorBundle\Entity\Inventor $inventors)
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
     * Set id
     *
     * @param  integer $id
     * @return Patent
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

}
