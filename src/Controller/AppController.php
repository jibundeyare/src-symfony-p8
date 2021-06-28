<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class AppController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(): Response
    {
        $message = 'Hello Symfony!';

        return $this->render('app/index.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * @Route("/redir", name="app_redir")
     */
    public function redir(): Response
    {
        return $this->redirectToRoute('school_year_index');
    }
}
