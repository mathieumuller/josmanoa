<?php

namespace App\Controller;

use App\Manager\MediaManager;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller used to upload files.
 *
 * @author Mathieu Muller <mathieu.muller1006@gmail.com>
 *
 * @Route("/file-upload")
 */
class UploadController extends AbstractController
{
    /**
     * @Route("/media/{uploadId}", name="josmanoa_media_upload")
     */
    public function uploadMedia($uploadId, Request $request, MediaRepository $mediaRepository, EntityManagerInterface $em, MediaManager $mediaManager)
    {
        if ($file = $request->files->get('file')) {
            if (0 === $file->getError()) {
                $media = $mediaRepository->createNew();

                $media->setUploadId($uploadId)
                    ->setUploadedFile($file)
                    ->setName($file->getClientOriginalName())
                ;
                foreach ($mediaManager->createThumbnails($file) as $size => $thumbnail) {
                    $media->addThumbnail($thumbnail);
                }

                $em->persist($media);
                $em->flush();
            } else {
                return new Response("L'erreur suivante est survenue : ".$file->getError(), 400);
            }
        } else {
            return new Response('Une erreur est survenue', 400);
        }

        return new Response('Le média a bien été enregistré');
    }
}
