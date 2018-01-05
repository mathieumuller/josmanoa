<?php

namespace App\Entity;

use App\Traits\TimeAwareEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MediaRepository")
 * @ORM\Table(name="media")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 *
 * @author Mathieu Muller <mathieu.muller1006@gmail.com>
 */
class Media
{
    use TimeAwareEntity;

    const STATUS_LOCAL = 1;
    const STATUS_TRANSFER_PROCESSING = 2;
    const STATUS_TRANSFERED = 3;
    const STATUS_TRANSFER_FAILED = 4;

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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $coordinates;

    /**
     * @var string
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $date;

    /**
     * @var string
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $dateFrom;

    /**
     * @var string
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $dateTo;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $uploadId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $status = self::STATUS_LOCAL;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Thumbnail", mappedBy="media", cascade={"persist", "remove"})
     */
    protected $thumbnails;

    /**
     * This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="media", fileNameProperty="file.name", size="file.size", mimeType="file.mimeType", originalName="file.originalName", dimensions="file.dimensions")
     *
     * @var File
     */
    private $uploadedFile;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File")
     *
     * @var EmbeddedFile
     */
    private $file;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="medias")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $owner;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Album", mappedBy="medias")
     */
    protected $albums;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Album", mappedBy="cover")
     */
    protected $albumCovers;

    public function __construct()
    {
        $this->file = new EmbeddedFile();
        $this->albums = new ArrayCollection();
        $this->thumbnails = new ArrayCollection();
    }

    public function isPortrait()
    {
        $file = $this->getFile();
        if ($dimensions = $file->getDimensions()) {
            return $dimensions[0] <= $dimensions[1];
        }

        return false;
    }

    public function isLandscape()
    {
        $file = $this->getFile();
        if ($dimensions = $file->getDimensions()) {
            return $dimensions[0] > $dimensions[1];
        }

        return false;
    }

    public function getFilename()
    {
        return $this->getFile() ? $this->getFile()->getName() : null;
    }

    public function getDefaultName()
    {
        return $this->getUploadId()
            .'/'
            .uniqid()
            .'.'
            .$this->getUploadedFile()->guessExtension()
        ;
    }

    public function getThumbnail($size = Thumbnail::SIZE_S)
    {
        return $this->getThumbnails()->filter(function ($tmb) use ($size) {
            return $tmb->getSize() === $size;
        })->first();
    }

    public function getFileExtension($withDot = false)
    {
        if ($file = $this->getFile()) {
            $mimeTypes = [
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/jpeg' => 'jpeg',
            ];

            $extension = $mimeTypes[$file->getMimeType()];

            return $withDot ? '.'.$extension : $extension;
        }

        return;
    }

    public function isRemote()
    {
        return self::STATUS_TRANSFERED === $this->getStatus();
    }

    public function isLocal()
    {
        return self::STATUS_LOCAL === $this->getStatus();
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
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile $file
     */
    public function setUploadedFile(File $uploadedFile = null)
    {
        $this->uploadedFile = $uploadedFile;

        if ($uploadedFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * @param EmbeddedFile $file
     */
    public function setFile(EmbeddedFile $file)
    {
        $this->file = $file;
    }

    /**
     * @return EmbeddedFile
     */
    public function getFile()
    {
        return $this->file;
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

    /**
     * @return string
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
     * @return ArrayCollection
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    public function addAlbum(Album $album)
    {
        if (!$this->albums->contains($album)) {
            $this->albums->add($album);
            $album->addMedia($this);
        }
    }

    public function removeAlbum(Album $album)
    {
        if ($this->albums->contains($album)) {
            $this->albums->removeElement($album);
            $album->removeMedia($this);
        }
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

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
     * @return ArrayCollection
     */
    public function getThumbnails()
    {
        return $this->thumbnails;
    }

    /**
     * @param Thumbnail $thumbnail
     *
     * @return self
     */
    public function addThumbnail(Thumbnail $thumbnail)
    {
        if (!$this->thumbnails->contains($thumbnail)) {
            $this->thumbnails->add($thumbnail);
            $thumbnail->setMedia($this);
        }

        return $this;
    }

    /**
     * @param Thumbnail $thumbnail
     *
     * @return self
     */
    public function removeThumbnail(Thumbnail $thumbnail)
    {
        if ($this->thumbnails->contains($thumbnail)) {
            $this->thumbnails->removeElement($thumbnail);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAlbumCovers()
    {
        return $this->albumCovers;
    }

    /**
     * @param ArrayCollection $albumCovers
     *
     * @return self
     */
    public function setAlbumCovers(ArrayCollection $albumCovers)
    {
        $this->albumCovers = $albumCovers;

        return $this;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $date
     *
     * @return self
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * @param string $dateFrom
     *
     * @return self
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * @param string $dateTo
     *
     * @return self
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;

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
