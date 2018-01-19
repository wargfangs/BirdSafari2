<?php

namespace Birds\BlogBundle\Entity;

use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="Birds\BlogBundle\Repository\CommentRepository")
 */
class Comment
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
     * @ORM\Column(name="content", type="string", length=255)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="submitDate", type="datetime")
     */
    private $date;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\User", inversedBy="comments", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", unique=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Birds\BlogBundle\Entity\Article", inversedBy="comments", cascade={"persist"})
     * @ORM\joinColumn(referencedColumnName="id", nullable=true, onDelete="SET NULL") 
     */
    private $article;

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
     * Set content
     *
     * @param string $content
     *
     * @return Comment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Comment
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
     * Set article
     *
     * @param \Birds\BlogBundle\Entity\Article $article
     *
     * @return Article
     */
    public function setArticle(\Birds\BlogBundle\Entity\Article $article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return \Birds\BlogBundle\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Article
     */
    
    public function setUser(\AppBundle\Entity\User $user = null)
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
    
    public function __construct()
    {
        $this->date = new \DateTime('Europe/Paris');
    }
}
