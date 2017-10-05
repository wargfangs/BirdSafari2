<?php

namespace Birds\ObservationsBundle\Entity;

use AppBundle\Entity\Image;
use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Observation
 *
 * @ORM\Table(name="observation")
 * @ORM\Entity(repositoryClass="Birds\ObservationsBundle\Repository\ObservationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Observation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="birdname", type="string", length=255, nullable=false)
     */
    private $birdname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float", nullable=false)
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float", nullable=false)
     */
    private $longitude;

    /**
     * @var boolean
     *
     * @ORM\Column(name="valid", type="boolean",nullable =false)
     */
    private $valid;
	/**
     * One Observation has One User.
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=false)
     */
    private $user;


	 /**
     * @var Image
     * @ORM\Column(name="imageId",nullable=true)
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="image", referencedColumnName="id", nullable=true)
     */
    private $image;


    public function __construct()
    {
        $this->valid=false;
        $this->date = new \DateTime('now');
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set birdname
     *
     * @param string $birdname
     *
     * @return Observation
     */
    public function setBirdname($birdname)
    {
        $this->birdname = $birdname;

        return $this;
    }

    /**
     * Get birdname
     *
     * @return string
     */
    public function getBirdname()
    {
        return $this->birdname;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Observation
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     *
     * @return Observation
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     *
     * @return Observation
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
	/**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Observation
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
	
	/**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return Image
     */
    public function setImage(\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \AppBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set valid
     *
     * @param \boolean $valid
     *
     * @return Observation
     */
    public function setValid( $valid)
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * Get valid
     *
     * @return \boolean
     */
    public function getValid()
    {
        return $this->valid;
    }

    /**
     *
     * @ORM\PrePersist
     */
    public function convertBirdToString()
    {

        if($this->getBirdname() instanceof Birds)
            $this->setBirdname($this->birdname->fetchResult());
        if($this->getImage() != null)
        {
            $this->image = $this->getImage()->getId();
        }
    }
}
