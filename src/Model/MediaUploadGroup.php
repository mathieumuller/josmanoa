<?php

namespace App\Model;

use App\Entity\Album;
use App\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\PropertyAccess\PropertyAccess;

class MediaUploadGroup
{
    /**
     * @var string
     */
    protected $uploadId;

    /**
     * @var array
     */
    protected $coordinates;
    /**
     * @var ArrayCollection
     */
    protected $albums;
    /**
     * @var \DateTime
     */
    protected $dateFrom;
    /**
     * @var \DateTime
     */
    protected $dateTo;

    protected $accessor;

    public function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
        $this->albums = new ArrayCollection();
        $this->uploadId = uniqid();
    }

    public function applyMedia(Media $media): Media
    {
        foreach (get_object_vars($this) as $property => $value) {
            if ($this->accessor->isWritable($media, $property)) {
                $this->accessor->setValue($media, $property, $value);
            }
        }

        return $media;
    }

    /**
     * @return array
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * @param array $coordinates
     *
     * @return self
     */
    public function setCoordinates(array $coordinates)
    {
        $this->coordinates = $coordinates;

        return $this;
    }

    /**
     * @return Album
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    public function addAlbum(Album $album)
    {
        if (!$this->albums->contains($album)) {
            $this->albums->add($album);
        }
    }

    public function removeAlbum(Album $album)
    {
        if ($this->albums->contains($album)) {
            $this->albums->removeElement($album);
        }
    }

    /**
     * @return \DateTime
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * @param \DateTime $dateFrom
     *
     * @return self
     */
    public function setDateFrom(\DateTime $dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * @param \DateTime $dateTo
     *
     * @return self
     */
    public function setDateTo(\DateTime $dateTo)
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    /**
     * @return string
     */
    public function getUploadId()
    {
        return $this->uploadId;
    }

    /**
     * @param string $uploadId
     *
     * @return self
     */
    public function setUploadId($uploadId)
    {
        $this->uploadId = $uploadId;

        return $this;
    }
}
