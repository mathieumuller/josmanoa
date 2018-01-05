<?php

namespace App\Resolver;

use App\Entity\Media;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PathResolver
{
    protected $tempFileSystem;
    protected $thumbzFileSystem;
    protected $s3FileSystem;
    protected $requestStack;
    protected $projectPath;

    public function __construct(
        FilesystemInterface $tempFileSystem,
        FilesystemInterface $thumbzFileSystem,
        FilesystemInterface $s3FileSystem,
        RequestStack $requestStack,
        $projectPath
    ) {
        $this->tempFileSystem = $tempFileSystem;
        $this->thumbzFileSystem = $thumbzFileSystem;
        $this->s3FileSystem = $s3FileSystem;
        $this->requestStack = $requestStack;
        $this->projectPath = $projectPath;
    }

    public function getMediaPath(Media $media, $absolute = false)
    {
        if ($media->isRemote()) {
            return $this->getRemotePath($media);
        } else {
            return $this->getLocalPath($media, $absolute);
        }
    }

    public function getThumbnailPath(Media $media, $thumbSize, $absolute = false)
    {
        $thumbnail = $media->getThumbnail($thumbSize);
        $pathPrefix = $this->thumbzFileSystem->getAdapter()->getPathPrefix();
        $fullPath = $pathPrefix.$thumbnail->getPath();

        return $this->replaceLocalPath($fullPath, $absolute);
    }

    protected function getRemotePath(Media $media)
    {
        $adapter = $this->s3FileSystem->getAdapter();
        $host = $adapter->getClient()->getEndPoint()->__toString();

        return $host.'/'
            .$adapter->getBucket().'/'
            .$adapter->getPathPrefix()
            .$media->getFilename()
        ;
    }

    protected function getLocalPath(Media $media, $absolute = false)
    {
        $pathPrefix = $this->tempFileSystem->getAdapter()->getPathPrefix();
        $fullPath = $pathPrefix.$media->getFilename();

        return $this->replaceLocalPath($fullPath, $absolute);
    }

    protected function replaceLocalPath($localPath, $absolute = false)
    {
        if (!$absolute) {
            $path = str_replace($this->getPublicPath(), '', $localPath);
        } else {
            $path = str_replace($this->getPublicPath(), $this->getBaseUrl(), $localPath);
        }

        return $path;
    }

    protected function getBaseUrl()
    {
        return $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost();
    }

    protected function getPublicPath()
    {
        return $this->projectPath.'/public';
    }
}
