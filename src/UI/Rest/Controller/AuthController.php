<?php

namespace App\UI\Rest\Controller;

use App\Domain\User\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class AuthController extends FOSRestController
{
    /**
     * @Rest\Post("/register")
     * @param Request $request
     * @return View
     */
    public function register(Request $request): View
    {
        $em = $this->getDoctrine()->getManager();
        $username = $request->get('username');
        $password = $request->get('password');

        $user = new User($username);
        $user->setPassword($this->get('security.password_encoder')->encodePassword($user, $password));
        $em->persist($user);
        $em->flush();
        return View::create($user, Response::HTTP_CREATED);
    }
}
