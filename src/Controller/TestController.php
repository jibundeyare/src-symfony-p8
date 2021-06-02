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

        // récupération du user dont l'id est égal à 1
        $admin = $userRepository->find(1);
        dump($admin);

        // récupération de la liste complète des students
        $students = $studentRepository->findAll();
        dump($students);

        // récupération du student dont l'id est égal à 1
        $firstStudent = $studentRepository->find(1);
        dump($firstStudent);

        exit();
    }
}
