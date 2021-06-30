<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swift_Mailer;

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

    /**
     * @Route("/mail", name="app_mail")
     */
    public function mail(Swift_Mailer $mailer): Response
    {
        $toEmail = 'contact@jibundeyare.com';

        // Choix du sujet du mail
        $message = (new \Swift_Message('Hello Swiftmailer!'))
            // Choix de l'adresse de l'expéditeur
            // Même si mettez une adresse "from" différente du compte SMTP,
            // l'adresse du compte SMTP sera visible dans l'entête du mail
            ->setFrom(['daishi.kaszer@gmail.com' => 'Daishi Kaszer'])
            // Choix de l'adresse du destinataire
            ->setTo([$toEmail])
            // Choix du message au format HTML
            ->setBody(
                $this->renderView('emails/hello.html.twig', [
                    'toEmail' => $toEmail
                ]),
                'text/html'
            )
            // Choix du message au format texte
            ->addPart(
                $this->renderView('emails/hello.txt.twig', [
                    'toEmail' => $toEmail
                ]),
                'text/plain'
            )
        ;

        $mailer->send($message);

        return new Response(
            'OK',
            Response::HTTP_OK
        );
    }
}
