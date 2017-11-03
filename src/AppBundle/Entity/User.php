<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="institution", type="string", length=255, nullable=true)
     */
    private $institution;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birth", type="date", nullable=true)
     */
    private $birth;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $confirmationStatus;

    /**
     * @var Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image", inversedBy="users", cascade={"persist", "remove"})
     */
    private $image;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="DevTools\BlogBundle\Entity\Article", mappedBy="user", cascade={"persist", "remove"})
     */
    private $articles; // Notez le « s », un user est lié à plusieurs articles
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="DevTools\BlogBundle\Entity\Comment", mappedBy="user", cascade={"persist", "remove"})
     */
    private $comments; // Notez le « s », un user est lié à plusieurs commentaires


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->confirmationStatus=false;
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
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }


    /**
     * Set institution
     *
     * @param string $institution
     *
     * @return User
     */
    public function setInstitution($institution)
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * Get institution
     *
     * @return string
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Set birth
     *
     * @param \DateTime $birth
     *
     * @return User
     */
    public function setBirth($birth)
    {
        $this->birth = $birth;

        return $this;
    }

    /**
     * Get birth
     *
     * @return \DateTime
     */
    public function getBirth()
    {
        return $this->birth;
    }

    /**
     * Set confirmationStatus
     *
     * @param boolean $confirmationStatus
     *
     * @return User
     */
    public function setConfirmationStatus($confirmationStatus)
    {
        $this->confirmationStatus = $confirmationStatus;

        return $this;
    }

    /**
     * Get confirmationStatus
     *
     * @return boolean
     */
    public function getConfirmationStatus()
    {
        return $this->confirmationStatus;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return User
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
     * Add article
     *
     * @param \DevTools\BlogBundle\Entity\Article $article
     *
     * @return User
     */
    public function addArticle(\DevTools\BlogBundle\Entity\Article $article)
    {
        $this->articles[] = $article;

        return $this;
    }

    /**
     * Remove article
     *
     * @param \DevTools\BlogBundle\Entity\Article $article
     */
    public function removeArticle(\DevTools\BlogBundle\Entity\Article $article)
    {
        $this->articles->removeElement($article);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Add comment
     *
     * @param \DevTools\BlogBundle\Entity\Comment $comment
     *
     * @return User
     */
    public function addComment(\DevTools\BlogBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \DevTools\BlogBundle\Entity\Comment $comment
     */
    public function removeComment(\DevTools\BlogBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }
}
