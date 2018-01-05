<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class BaseRepository extends ServiceEntityRepository
{
    protected $entityClass;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, $this->entityClass);
    }

    public function createNew()
    {
        return new $this->entityClass();
    }
}
