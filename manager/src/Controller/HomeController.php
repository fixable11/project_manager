<?php

declare(strict_types=1);

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Index method
     *
     * @Route("/", name="home")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->render('app/home.html.twig');
    }
}