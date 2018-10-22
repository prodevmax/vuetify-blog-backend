<?php

declare(strict_types=1);

namespace App\UI\Web\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController {
    /**
     * @Route(
     *     "/",
     *     name="home",
     *     methods={"GET"}
     * )
     *
     * @return Response
     */
    public function homepage()

    {
        return $this->render('homepage', ['title' => 'Welcome']);
    }
}