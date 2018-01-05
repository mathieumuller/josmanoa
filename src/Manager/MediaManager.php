<?php

namespace App\Manager;

use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Symfony\Component\HttpFoundation\File\File;
use App\Entity\Thumbnail;
use Imagine\Image\Point;

class MediaManager
{
    const FILESYSTEM_PREFIX_LOCAL = 'local://';
    const FILESYSTEM_PREFIX_S3 = 's3://';

    protected $em;
    protected $tempFileSystem;
    protected $thumbzFileSystem;
    protected $s3FileSystem;

    public function __construct(
        EntityManagerInterface $em,
        FilesystemInterface $tempFileSystem,
        FilesystemInterface $thumbzFileSystem,
        FilesystemInterface $s3FileSystem
    ) {
        $this->em = $em;
        $this->tempFileSystem = $tempFileSystem;
        $this->thumbzFileSystem = $thumbzFileSystem;
        $this->s3FileSystem = $s3FileSystem;
    }

    public function transfer(Media $media)
    {
        $mountManager = new MountManager([
            'local' => $this->tempFileSystem,
            's3' => $this->s3FileSystem,
        ]);

        $filename = $media->getFilename();

        if ($this->tempFileSystem->has($filename)) {
            $mountManager->move(self::FILESYSTEM_PREFIX_LOCAL.$filename, self::FILESYSTEM_PREFIX_S3.$filename);
            $media->setStatus(Media::STATUS_TRANSFERED);
        } else {
            $media->setStatus(Media::STATUS_TRANSFER_FAILED);
        }
    }

    public function createThumbnails(File $file): array
    {
        $thumbz = [];
        $thumbPrefix = 'tmb'.uniqid();

        $thumbExtension = $this->getFileExtension($file);
        $thumbzPathPrefix = $this->thumbzFileSystem->getAdapter()->getPathPrefix();
        $imagine = new Imagine();

        foreach (Thumbnail::getFormats() as $size => $dimensions) {
            $thumbPath = $thumbPrefix."_$size.$thumbExtension";
            $thumb = $this->em->getRepository('App:Thumbnail')->createNew();
            $thumb->setSize($size)
                ->setPath($thumbPath)
            ;
            $original = $imagine->open($file->getRealPath());
            $original = $this->cropToSquare($original);
            $thumbImg = $original->thumbnail(new Box($dimensions[0], $dimensions[1], ImageInterface::THUMBNAIL_OUTBOUND));
            $thumbImg->save("$thumbzPathPrefix/$thumbPath");

            $thumbz[] = $thumb;
        }

        return $thumbz;
    }

    protected function cropToSquare(ImageInterface $image): ImageInterface
    {
        $box = $image->getSize();
        $width = $box->getWidth();
        $height = $box->getHeight();

        switch ($width <=> $height) {
            case -1:
                $start = new Point(0, ($height - $width) / 2);
                $box = new Box($width, $width);
                break;
            case 1:
                $start = new Point(($width - $height) / 2, 0);
                $box = new Box($height, $height);
                break;
            case 0:
                return $image;
                break;
        }

        return $image->crop($start, $box);
    }

    protected function getFileExtension(File $file): string
    {
        if (empty($extension = $file->guessExtension())) {
            $extension = $file->getExtension();

            if (empty($extension)) {
                $extension = $this->getPathExtension($file->getRealPath());
            }
        }

        return $extension;
    }
}
