<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use App\Repository\SchoolYearRepository;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index(
        ProjectRepository $projectRepository,
        SchoolYearRepository $schoolYearRepository,
        StudentRepository $studentRepository,
        UserRepository $userRepository): Response
    {
        // récupération de l'entity manager
        $entityManager = $this->getDoctrine()->getManager();

        // récupération de la liste complète des admin
        $admins = $userRepository->findAllAdmins();
        // récupération du dernier admin de la liste
        $lastAdmin = end($admins);

        dump($lastAdmin);

        // vérification de l'email de l'admin
        // car on ne veut pas supprimer tous les comptes admin
        if ($lastAdmin->getEmail() != 'admin@example.com') {
            // demande de suppression d'un objet
            $entityManager->remove($lastAdmin);
            // exécution de la requête SQL
            $entityManager->flush();
        }

        // récupération de la liste complète des school years
        $schoolYears = $schoolYearRepository->findAll();
        // affectation d'une school year à la variable $schoolYear
        $schoolYear = $schoolYears[5];

        // récupération de la liste complète des projects
        $projects = $projectRepository->findAll();
        // affectation du premier projet à la variable $project1
        $project1 = $projects[0];
        // affectation du second projet à la variable $project2
        $project2 = $projects[1];
        // affectation du troisième projet à la variable $project3
        $project3 = $projects[2];

        // affichage de la liste des students reliés à un project
        foreach ($project1->getStudents() as $studentProject1) {
            dump($studentProject1);
        }

        // récupération du premier student
        $student = $studentRepository->findAll()[10];
        dump($student);
        // changement du téléphone du student
        $student->setPhone('0687654321');
        // changement de relation avec une school year
        $student->setSchoolYear($schoolYear);
        // suppression d'une relation avec un project
        $student->removeProject($project1);
        // ajout d'une relation avec un project
        $student->addProject($project2);
        // ajout d'une relation avec un project
        $student->addProject($project3);
        // enregistrement du changement dans la BDD
        $entityManager->flush();
        dump($student);

        // récupération de la liste complète des users
        $users = $userRepository->findAll();
        dump($users);

        // récupération de la liste complète des users qui ont le rôle ROLE_ADMIN
        $admins = $userRepository->findAllAdmins();
        dump($admins);

        // récupération de la liste complète des users qui ont le rôle ROLE_STUDENT
        $studentRoles = $userRepository->findByRole('ROLE_STUDENT');

        // récupération d'un user de rôle ROLE_STUDENT
        $user = $studentRoles[0];
        // récupération du profil student à partir du compte user
        // on précise qu'on se limite aux users qui ont le rôle ROLE_STUDENT 
        $student = $studentRepository->findOneByUser($user, 'ROLE_STUDENT');
        dump($user);
        dump($student);

        // récupération du user dont l'id est égal à 1
        $admin = $userRepository->find(1);
        dump($admin);

        // récupération de la liste complète des students
        $students = $studentRepository->findAll();
        dump($students);

        // récupération du compte user d'un student
        $firstStudent = $students[0];
        $user = $firstStudent->getUser();
        dump($user);

        // récupération du student dont l'id est égal à 1
        $firstStudent = $studentRepository->find(1);
        dump($firstStudent);

        exit();
    }
}
