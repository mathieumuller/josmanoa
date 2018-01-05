<?php

namespace App\Upload;

use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;

class FileNamer implements NamerInterface
{
    public function name($object, PropertyMapping $mapping): string
    {
        return uniqid()
            .'.'
            .$object->getUploadedFile()->guessExtension()
        ;
    }
}
