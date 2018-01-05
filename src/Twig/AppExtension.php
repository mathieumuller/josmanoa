<?php

namespace App\Twig;

use App\Entity\Media;
use App\Resolver\PathResolver;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use App\Entity\Thumbnail;

class AppExtension extends AbstractExtension
{
    private $pathResolver;

    public function __construct(PathResolver $pathResolver)
    {
        $this->pathResolver = $pathResolver;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('media_path', [$this, 'getMediaPath']),
            new TwigFilter('thumb_path', [$this, 'getThumbnailPath']),
        ];
    }

    public function getMediaPath(Media $media, $absolute = false)
    {
        return $this->pathResolver->getMediaPath($media, $absolute);
    }

    public function getThumbnailPath(Media $media, $thumbSize = Thumbnail::SIZE_S, $absolute = false)
    {
        return $this->pathResolver->getThumbnailPath($media, $thumbSize, $absolute);
    }
}
