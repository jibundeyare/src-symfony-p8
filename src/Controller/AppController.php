<?php

namespace App\Controller;

use App\Form\StudentSearchType;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/search", name="app_search", methods={"GET","POST"})
     */
    public function search(Request $request, StudentRepository $studentRepository): Response
    {
        // Initialisation du résultat de la recherche.
        // On part d'une liste vide. 
        $students = [];

        // Création du formulaire de recherche
        $studentSearchForm = $this->createForm(StudentSearchType::class);
        $studentSearchForm->handleRequest($request);

        if ($studentSearchForm->isSubmitted() && $studentSearchForm->isValid()) {
            $keyword = $studentSearchForm->getData()['keyword'];
            $students = $studentRepository->findByFirstnameOrLastname($keyword);
        }

        // On vérifie si l'utilisateur est un student
        // Note : on peut aussi utiliser in_array('ROLE_STUDENT', $user->getRoles())
        // au lieu de $this->isGranted('ROLE_STUDENT').
        if ($this->isGranted('ROLE_STUDENT')) {
            // L'utilisateur est un student

            // On récupère le compte de l'utilisateur authentifié
            $user = $this->getUser();

            // On récupère le profil student lié au compte utilisateur
            $student = $studentRepository->findOneByUser($user);

            // On récupère la school year de l'utilisateur
            $schoolYear = $student->getSchoolYear();

            // On récupère la liste des students de la school year
            $schoolYearStudents = $schoolYear->getStudents();

            // Le student n'a le droit de voir que les students de la même school year.
            // On va donc croiser le résultat de la recherche avec les students de la
            // school year.

            // Initialisation de la liste filtrée (qui est d'abord vide)
            $filteredStudents = [];

            // On passe revue chaque student du résultat de recherche et on vérifie
            // s'il fait partie de la school year.
            foreach ($students as $student) {
                // Si le student passé en revue fait partie de la school year, on
                // l'ajoute à la liste filtrée.
                if ($schoolYearStudents->contains($student)) {
                    $filteredStudents[] = $student;
                }
            }

            // On écrase la liste originale avec le contenu de la liste filtrée.
            $students = $filteredStudents;
        }

        return $this->render('student/index.html.twig', [
            'students' => $students,
            'studentSearchForm' => $studentSearchForm->createView(),
        ]);
    }
}
