<?php

namespace App\Entity;

use App\Traits\TimeAwareEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AlbumRepository")
 * @ORM\Table(name="album")
 * @ORM\HasLifecycleCallbacks()
 *
 * @author Mathieu Muller <mathieu.muller1006@gmail.com>
 */
class Album
{
    use TimeAwareEntity;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Media", inversedBy="albumCovers")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $cover;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="albums")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $owner;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Media", inversedBy="albums")
     * @ORM\JoinTable(name="media_album")
     */
    protected $medias;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function hasMedia(Media $media)
    {
        return $this->medias->contains($media);
    }

    public function getDescriptionOrDefault()
    {
        return $this->getDescription() ?? $this->getCreatedAt()->format(getenv('DATE_FORMAT'));
    }

    public function isCover(Media $media)
    {
        if (null === $this->getCover()) {
            return false;
        }

        return $this->getCover()->getId() === $media->getId();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * @param Media $media
     *
     * @return self
     */
    public function addMedia(Media $media)
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
        }

        return $this;
    }

    /**
     * @param Media $media
     *
     * @return self
     */
    public function removeMedia(Media $media)
    {
        if ($this->medias->contains($media)) {
            $this->medias->removeElement($media);
            if ($cover = $this->getCover()) {
                if ($media->getId() === $cover->getId()) {
                    $this->cover = null;
                }
            }
        }

        return $this;
    }

    /**
     * @return Media
     */
    public function getCover()
    {
        if (null !== $this->cover && !$this->hasMedia($this->cover)) {
            $this->cover = null;
        }

        return $this->cover;
    }

    /**
     * @param Media $cover
     *
     * @return self
     */
    public function setCover(Media $cover)
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     *
     * @return self
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;

        return $this;
    }
}
