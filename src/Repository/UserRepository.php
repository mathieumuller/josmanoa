<?php

namespace App\Repository;

use App\Entity\User;

class UserRepository extends BaseRepository
{
    protected $entityClass = User::class;
}
