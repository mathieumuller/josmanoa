<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Media;
use App\Form\AlbumType;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used to manage album.
 *
 * @author Mathieu Muller <mathieu.muller1006@gmail.com>
 *
 * @Route("/album")
 */
class AlbumController extends AbstractController
{
    /**
     * Create a new Album.
     *
     * @Route("/", name="josmanoa_index_album")
     */
    public function indexAction(AlbumRepository $repository)
    {
        return $this->render(
            'album/index.html.twig',
            ['albums' => $repository->findAll()]
        );
    }

    /**
     * Create a new Album.
     *
     * @Route("/create", name="josmanoa_create_album")
     */
    public function createAction(Request $request, EntityManagerInterface $em, AlbumRepository $repository)
    {
        $album = $repository->createNew();
        $form = $this->createForm(AlbumType::class, $album);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($album);
            $em->flush();
        }

        return $this->render(
            'album/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Show an Album.
     *
     * @Route("/show/{id}", name="josmanoa_show_album")
     * @ParamConverter("album", class="App:Album")
     */
    public function showAction(Request $request, Album $album)
    {
        return $this->render(
            'album/show.html.twig',
            ['album' => $album]
        );
    }

    /**
     * Set a media as Album cover.
     *
     * @Route("/album/{album_id}/cover/{media_id}", name="josmanoa_change_album_cover")
     * @ParamConverter("album", class="App:Album", options={"id" = "album_id"})
     * @ParamConverter("media", class="App:Media", options={"id" = "media_id"})
     */
    public function coverAlbumAction(Media $media, Album $album, EntityManagerInterface $em)
    {
        $album->setCover($media);
        $em->flush();

        return new JsonResponse([
            'notify' => true,
            'status' => 'success',
            'message' => "La couverture de l'album a bien été modifiée",
        ]);
    }

    /**
     * Set a media as Album cover.
     *
     * @Route("/album/{album_id}/remove/{media_id}", name="josmanoa_remove_from_album")
     * @ParamConverter("album", class="App:Album", options={"id" = "album_id"})
     * @ParamConverter("media", class="App:Media", options={"id" = "media_id"})
     */
    public function removeFromAlbumAction(Media $media, Album $album, EntityManagerInterface $em)
    {
        $album->removeMedia($media);
        $em->flush();

        return new JsonResponse([
            'notify' => true,
            'status' => 'success',
            'message' => "Le média a bien été retiré de l'album",
        ]);
    }
}
