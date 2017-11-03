<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Image
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
     * @ORM\Column(name="src", type="string", length=255)
     */
    private $src;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;

    //private $height;
    //private $width;
    //private $extension;
    //private $size;
    //private $small; //Image
    //private $normal; //Image
    //private $normal; //Image

    private $file;
    private $tempFilename;

    /**
     * @var String
     * 
     * @ORM\OneToMany(targetEntity="DevTools\BlogBundle\Entity\Article", mappedBy="image", cascade={"persist"})
     */
    private $articles; // Notez le « s », une image est liée à plusieurs articles
	
    /**
     * @var String
     * 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="image", cascade={"persist"})
     */
    private $users; // Notez le « s », une image est liée à plusieurs users

    /**
     * @var String
     * 
     * @ORM\OneToMany(targetEntity="Birds\ObservationsBundle\Entity\Observation", mappedBy="image", cascade={"persist"})
     */
    private $observations; // Notez le « s », une image est liée à plusieurs observations
    
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
     * Set src
     *
     * @param string $src
     *
     * @return Image
     */
    public function setSrc($src)
    {
        $this->src = $src;

        return $this;
    }

    /**
     * Get src
     *
     * @return string
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }


    public function getFile()
    {
        return $this->file;
    }





    // On modifie le setter de File, pour prendre en compte l'upload d'un fichier lorsqu'il en existe déjà un autre
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;

        // On vérifie si on avait déjà un fichier pour cette entité
        if (null !== $this->src) {

            $this->tempFilename = $this->src;
            $this->src = null;
            $this->alt = null;
        }

        $this->preUpload();

    }

    /**
     *  PreUpdate ne sert à rien, l'objet est détruit après le persist de la classe possédante. Doit être fait avant.
     *
     */
    public function preUpload()
    {
        if (null === $this->file) {
            return;
        }

        $this->src = $this->file->guessExtension();
        $this->alt = $this->file->getClientOriginalName();

        $this->file->move($this->getUploadRootDir(),$this->alt);
        file_put_contents("log.txt", "src = ".$this->src . " ... alt = ". $this->alt);
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // Si jamais il n'y a pas de fichier (champ facultatif), on ne fait rien
        if (null === $this->file) {
            return;
        }

        // Supprimer toutes les versions d'image qu'on a créé associé à cette image.
        if (null !== $this->tempFilename) {
            $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFilename;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }

        }

        // On déplace le fichier envoyé dans le répertoire de notre choix
        $this->file->move(
            $this->getUploadRootDir(), // Le répertoire de destination
            $this->id.'.'.$this->src   // Le nom du fichier à créer, ici « id.extension »
        );
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        // On sauvegarde temporairement le nom du fichier, car il dépend de l'id
        $this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->src;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        // En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
        if (file_exists($this->tempFilename)) {
            // On supprime le fichier
            unlink($this->tempFilename);
        }
    }

    public function getUploadDir()
    {
        // On retourne le chemin relatif vers l'image pour un navigateur
        return 'uploads/img';
    }

    public function getUploadRootDir()
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP

        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->articles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add article
     *
     * @param \DevTools\BlogBundle\Entity\Article $article
     *
     * @return Image
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
     * Add user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Image
     */
    public function addUser(\AppBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeUser(\AppBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add observation
     *
     * @param \Birds\ObservationsBundle\Entity\Observation $observation
     *
     * @return Image
     */
    public function addObservation(\Birds\ObservationsBundle\Entity\Observation $observation)
    {
        $this->observations[] = $observation;

        return $this;
    }

    /**
     * Remove observation
     *
     * @param \Birds\ObservationsBundle\Entity\Observation $observation
     */
    public function removeObservation(\Birds\ObservationsBundle\Entity\Observation $observation)
    {
        $this->observations->removeElement($observation);
    }

    /**
     * Get observations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getObservations()
    {
        return $this->observations;
    }
}
