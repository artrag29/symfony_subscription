<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomePageController extends AbstractController
{
    /**
     * Home Page.
     */
    public function homepage(): Response
    {
        return $this->render('home_page.html.twig');
    }
}
