<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LengowHomeController extends AbstractController
{
    /**
     * @Route("/", name="lengow_home")
     */
    public function index()
    {
        return $this->render('lengow_home/index.html.twig');
    }
}
