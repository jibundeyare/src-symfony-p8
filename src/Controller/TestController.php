<?php

namespace App\Controller;

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
    public function index(StudentRepository $studentRepository, UserRepository $userRepository): Response
    {
        // récupération de la liste complète des users
        $users = $userRepository->findAll();
        dump($users);

        // récupération de la liste complète des users qui ont le rôle ROLE_STUDENT
        $studentRoles = $userRepository->findByRole('ROLE_STUDENT');

        // récupération d'un user de rôle ROLE_STUDENT
        $user = $studentRoles[0];
        // récupération du profil student à partir du compte user
        $student = $studentRepository->findOneByUser($user, 'ROLE_STUDENT');
        dump($user);
        dump($student);
        exit();

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
