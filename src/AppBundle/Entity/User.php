<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
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
     * @var boolean
     *
     * Attribut non persisté en base de donnée
     */
    private $acceptCgu;
	
	/**
     * @var boolean
	 *Attribut non persisté en base de donnée
     */ 
    private $newsletterSubscriber;

    /**
     * @var Image
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Image", inversedBy="users", cascade={"persist", "remove"})
     */
    private $image;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="Birds\BlogBundle\Entity\Article", mappedBy="user", cascade={"persist", "remove"})
     */
    private $articles; // Notez le « s », un user est lié à plusieurs articles
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="Birds\BlogBundle\Entity\Comment", mappedBy="user", cascade={"persist", "remove"})
     */
    private $comments; // Notez le « s », un user est lié à plusieurs commentaires

    /**
	 * @Vich\UploadableField(mapping="avatar", fileNameProperty="avatarName")
	 * 
	 * @var File
	 */
	private $avatar;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 *
	 * @var string
	 */
	private $avatarName;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 *
	 * @var \DateTime
	*/
	private $updatedAt;

	
	
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
     * Set acceptCgu
     *
     * @param boolean $acceptCgu
     *
     * @return User
     */
    public function setAcceptCgu($acceptCgu)
    {
        $this->acceptCgu = $acceptCgu;

        return $this;
    }

    /**
     * Get acceptCgu
     *
     * @return boolean
     */
    public function getAcceptCgu()
    {
        return $this->acceptCgu;
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
     * @param \Birds\BlogBundle\Entity\Article $article
     *
     * @return User
     */
    public function addArticle(\Birds\BlogBundle\Entity\Article $article)
    {
        $this->articles[] = $article;

        return $this;
    }

    /**
     * Remove article
     *
     * @param \Birds\BlogBundle\Entity\Article $article
     */
    public function removeArticle(\Birds\BlogBundle\Entity\Article $article)
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
     * @param \Birds\BlogBundle\Entity\Comment $comment
     *
     * @return User
     */
    public function addComment(\Birds\BlogBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \Birds\BlogBundle\Entity\Comment $comment
     */
    public function removeComment(\Birds\BlogBundle\Entity\Comment $comment)
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
	
	/**
 * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
 *
 * @return User
*/
public function setAvatar(File $image = null)
{
   
    if ($image) {
		
		$this->avatar = $image;
        $this->updatedAt = new \DateTimeImmutable();
	}
    return $this;
}

	/**
	 * @return File|null
	 */
	public function getAvatar()
	{
		return $this->avatar;
	}

	/**
	 * @param string $avatarName
	 *
	 * @return User
	 */
	public function setAvatarName($avatarName)
	{
		$this->avatarName = $avatarName;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getAvatarName()
	{
		return $this->avatarName;
	}
	
	 /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
	
	
	/**
     * Set newsletterSubscriber
     *
     * @param \DateTime $newsletterSubscriber
     *
     * @return User
     */
    public function setNewsletterSubscriber($newsletterSubscriber)
    {
        $this->newsletterSubscriber = $newsletterSubscriber;

        return $this;
    }

    /**
     * Get newsletterSubscriber
     *
     * @return \DateTime
     */
    public function getNewsletterSubscriber()
    {
        return $this->newsletterSubscriber;
    }
}
