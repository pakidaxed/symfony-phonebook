<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * Just rendering the users info, for now, you can add whatever you want here
     * for example EDIT user password or smth, or other option when the app gets bigger
     * @Route("/settings", name="app_user_settings")
     * @return Response
     */
    public function settings()
    {
        return $this->render('user/settings.html.twig', ['user' => $this->getUser()]);
    }
}