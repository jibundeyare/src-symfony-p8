<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\User;
use App\Form\StudentType;
use App\Form\SearchType;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/student")
 */
class StudentController extends AbstractController
{
    /**
     * @Route("/", name="student_index", methods={"GET","POST"})
     */
    public function index(Request $request, StudentRepository $studentRepository): Response
    {
        // @todo template: ajouter l'affichage des emails
        // @todo form: sort school years in student type
        // @todo form: tri multiple ne fonctionne pas

        // Par défaut, les utilisateurs voient tous les students.
        // Mais les students ne sont censés voir que les students
        // de la même school year qu'eux.
        $students = $studentRepository->findAll();

        if ($request->request->all()) {
            $search = $request->request->get('search');
            $students = $studentRepository->findByFirstnameOrLastname($search);
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
            $students = $schoolYear->getStudents();
        }

        return $this->render('student/index.html.twig', [
            'students' => $students,
        ]);
    }

    /**
     * @Route("/new", name="student_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user = $student->getUser();
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('user')->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('student_index');
        }

        return $this->render('student/new.html.twig', [
            'student' => $student,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="student_show", methods={"GET"})
     */
    public function show(Student $student, StudentRepository $studentRepository): Response
    {
        // Un student n'a pas le droit de consulter le profil d'un autre student.  
        // On vérifie si l'utilisateur est un student et si c'est le cas,
        // on vérifie s'il demande le même profile que son profile student.
        // S'il demande le profile d'un autre utilisateur on le redirige vers
        // son propre profile.
        $response = $this->redirectStudent('student_show', $student, $studentRepository);

        if ($response) {
            return $response;
        }

        return $this->render('student/show.html.twig', [
            'student' => $student,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="student_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Student $student, StudentRepository $studentRepository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        // Un student n'a pas le droit de modifier le profil d'un autre student.  
        // On vérifie si l'utilisateur est un student et si c'est le cas,
        // on vérifie s'il demande le même profile que son profile student.
        // S'il demande le profile d'un autre utilisateur on le redirige vers
        // son propre profile.
        $response = $this->redirectStudent('student_edit', $student, $studentRepository);

        if ($response) {
            return $response;
        }

        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user = $student->getUser();
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('user')->get('plainPassword')->getData()
                )
            );

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('student_index');
        }

        return $this->render('student/edit.html.twig', [
            'student' => $student,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="student_delete", methods={"POST"})
     */
    public function delete(Request $request, Student $student): Response
    {
        // Si' l'utilisateur est un student, on renvoie une exception car
        // il n'a pas la droit d'effacer de données.
        if ($this->isGranted('ROLE_STUDENT')) {
            throw new AccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($student);
            $entityManager->flush();
        }

        return $this->redirectToRoute('student_index');
    }

    private function redirectStudent(string $route, Student $student, StudentRepository $studentRepository)
    {
        // On vérifie si l'utilisateur est un student
        // Note : on peut aussi utiliser in_array('ROLE_STUDENT', $user->getRoles()) au
        // lieu de $this->isGranted('ROLE_STUDENT').
        if ($this->isGranted('ROLE_STUDENT')) {
            // L'utilisateur est un student
            
            // Récupération du compte de l'utilisateur qui est connecté
            $user = $this->getUser();
    
            // Récupèration du profil student
            $userStudent = $studentRepository->findOneByUser($user);

            // Comparaison du profil demandé par l'utilisateur et le profil de l'utilisateur
            // Si les deux sont différents, on redirige l'utilisateur vers la page de son profil
            if ($student->getId() != $userStudent->getId()) {
                return $this->redirectToRoute($route, [
                    'id' => $userStudent->getId(),
                ]);
            }
        }

        // Si aucune redirection n'est nécessaire, on renvoit une valeur nulle
        return null;
    }
}
