<?php

namespace Birds\ObservationsBundle\Entity;

use AppBundle\Entity\Image;
use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var boolean
     * @ORM\Column(name="notSure", type="boolean", length=255, nullable=true)
     */
    private $notSure;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;
    /**
     * @var \int
     *
     * @ORM\Column(name="hour", type="integer", nullable=false)
     */
    private $hour;

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
     * @var string
     *
     * @ORM\Column(name="place", type="string", nullable=true)
     */
    private $place;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=true)
     *
     */
    private $image;

    /**
     * @ORM\Column(name="valid_pic", type="boolean",nullable=false)
     * @var boolean
     */
    private $hasValidPictureForShow=false;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=100, nullable=true)
     */
    private $title;
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

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
    public function setImage(\AppBundle\Entity\Image $image)
    {
        $this->image = $image;
        //file_put_contents("image.txt", "Created instance: src:". $image->getSrc(). " alt:" . $image->getAlt(). " Id:" . $image->getId()  );


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
        $this->hour= $this->date->format('H');
        if($this->getBirdname() instanceof Birds)
            $this->setBirdname($this->birdname->fetchResult());


        if($this->getImage() != null)
        {
            $this->image = $this->getImage()->getId();
        }
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Observation
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
     * Set description
     *
     * @param string $description
     *
     * @return Observation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set notSure
     *
     * @param boolean $notSure
     *
     * @return Observation
     */
    public function setNotSure($notSure)
    {
        $this->notSure = $notSure;

        return $this;
    }

    /**
     * Get notSure
     *
     * @return boolean
     */
    public function getNotSure()
    {
        return $this->notSure;
    }

    /**
     * Set hour
     *
     * @param integer $hour
     *
     * @return Observation
     */
    public function setHour($hour)
    {
        $this->hour = $hour;

        return $this;
    }

    /**
     * Get hour
     *
     * @return integer
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * Set place
     *
     * @param string $place
     *
     * @return Observation
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set hasValidPictureForShow
     *
     * @param boolean $hasValidPictureForShow
     *
     * @return Observation
     */
    public function setHasValidPictureForShow($hasValidPictureForShow)
    {
        $this->hasValidPictureForShow = $hasValidPictureForShow;

        return $this;
    }

    /**
     * Get hasValidPictureForShow
     *
     * @return boolean
     */
    public function getHasValidPictureForShow()
    {
        return $this->hasValidPictureForShow;
    }

    /**
     * Removes reference from database. Do not suppress
     */
    public function removeImageReference()
    {
        $this->image = null;
    }
}
