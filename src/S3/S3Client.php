<?php

namespace App\S3;

use Aws\S3\S3Client as BaseS3Client;

class S3Client extends BaseS3Client
{
    public function getUri()
    {
        return $this->getEndPoint()->__toString();
    }
}
