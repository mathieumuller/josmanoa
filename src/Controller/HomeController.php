<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used for public home.
 *
 * @author Mathieu Muller <mathieu.muller1006@gmail.com>
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="josmanoa_home")
     */
    public function index()
    {
        return $this->render('home.html.twig');
    }
}
