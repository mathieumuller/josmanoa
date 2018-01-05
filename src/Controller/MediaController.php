<?php

namespace App\Controller;

use App\Form\MediaUploadGroupType;
use App\Model\MediaUploadGroup;
use App\Repository\MediaRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Media;
use App\Form\MediaType;

/**
 * Controller used to manage media.
 *
 * @author Mathieu Muller <mathieu.muller1006@gmail.com>
 *
 * @Route("/media")
 */
class MediaController extends AbstractController
{
    /**
     * Create a new Media.
     *
     * @Route("/", name="josmanoa_index_media")
     */
    public function indexAction(MediaRepository $repository)
    {
        return $this->render(
            'media/index.html.twig',
            ['medias' => $repository->findAll()]
        );
    }

    /**
     * Create a new Media.
     *
     * @Route("/create", name="josmanoa_create_media")
     */
    public function createAction(Request $request, EntityManagerInterface $em, MediaRepository $repository)
    {
        $mediaGroup = new MediaUploadGroup();
        $form = $this->createForm(MediaUploadGroupType::class, $mediaGroup);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $medias = $repository->findBy(['uploadId' => $mediaGroup->getUploadId()]);
            foreach ($medias as $media) {
                $mediaGroup->applyMedia($media);
            }

            $this->addFlash('success', 'Les données ont bien été enregistrés.');
            $em->flush();

            return $this->redirectToRoute('josmanoa_index_media');
        }

        return $this->render(
            'media/create.html.twig',
            [
                'form' => $form->createView(),
                'uploadId' => $mediaGroup->getUploadId(),
            ]
        );
    }

    /**
     * Create a new Media.
     *
     * @Route("/edit/{id}", name="josmanoa_edit_media")
     * @ParamConverter("media", class="App:Media")
     */
    public function editAction(Request $request, Media $media, EntityManagerInterface $em, MediaRepository $repository)
    {
        $form = $this->createForm(MediaType::class, $media);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $medias = $repository->findBy(['uploadId' => $mediaGroup->getUploadId()]);
            // foreach ($medias as $media) {
            //     $mediaGroup->applyMedia($media);
            // }

            $em->flush();
            $this->addFlash('success', 'Les données ont bien été enregistrés.');

            //return $this->redirectToRoute('josmanoa_index_media');
        }

        return $this->render(
            'media/edit.html.twig',
            ['form' => $form->createView()]
        );
    }
}
