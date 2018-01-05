<?php

namespace App\Repository;

use App\Entity\Album;

class AlbumRepository extends BaseRepository
{
    protected $entityClass = Album::class;
}
