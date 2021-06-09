<?php

namespace App\Controller;

use App\Entity\User;
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
        // Récupération de l'entity manager.
        $entityManager = $this->getDoctrine()->getManager();

        // Récupération du user dont l'id est 2.
        $user = $userRepository->find(2);

        if ($user) {
            // Changement de son adresse email.
            $user->setEmail('foo@example.com');
            // Exécution des requêtes.
            // C-à-d envoi de la requête SQL à la BDD.
            $entityManager->flush();
            
            // Demande de suppression d'un user.
            $entityManager->remove($user);
            // Exécution des requêtes.
            // C-à-d envoi de la requête SQL à la BDD.
            $entityManager->flush();
        }

        // On peut vérifier que l'admin qui a été supprimé en douce
        // n'apparaît plus dans la liste complète des admins.
        // Récupération de la liste complète des admins.
        $admins = $userRepository->findAllAdmins();
        dump($admins);

        // On peut désactiver le filtrage des objets supprimés
        // en douce.
        $entityManager->getFilters()->disable('softdeleteable');
        // Les admins supprimés en douce sont de nouveau visibles.
        $admins = $userRepository->findAllAdmins();
        dump($admins);

        // On peut aussi réactiver le filtrage des objets supprimés
        // en douce.
        $entityManager->getFilters()->enable('softdeleteable');

        // La chaîne de caractères qu'on veut rechercher dans le prénom
        // ou le nom de famille des students.
        $name = 'remy';
        // Le repository renvoit tous les students qui contiennent la
        // chaîne de caractères recherchée dans le prénom ou le nom de famille.
        $students = $studentRepository->findByFirstnameOrLastname($name);
        dump($students);

        // Récupération de la liste complète des admins.
        $admins = $userRepository->findAllAdmins();
        // Récupération du dernier admin de la liste.
        $lastAdmin = end($admins);

        dump($lastAdmin);

        // Vérification de l'email de l'admin car on ne veut
        // pas supprimer tous les comptes admin.
        if ($lastAdmin->getEmail() != 'admin@example.com') {
            // Demande de suppression d'un objet.
            $entityManager->remove($lastAdmin);
            // Exécution des requêtes.
            // C-à-d envoi de la requête SQL à la BDD.
            $entityManager->flush();
        }

        // Récupération de la liste complète des school years.
        $schoolYears = $schoolYearRepository->findAll();
        // Affectation d'une school year à la variable $schoolYear.
        $schoolYear = $schoolYears[5];

        // Récupération de la liste complète des projects.
        $projects = $projectRepository->findAll();
        // Affectation du premier projet à la variable $project1.
        $project1 = $projects[0];
        // Affectation du second projet à la variable $project2.
        $project2 = $projects[1];
        // Affectation du troisième projet à la variable $project3.
        $project3 = $projects[2];

        // Affichage de la liste des students reliés à un project.
        foreach ($project1->getStudents() as $studentProject1) {
            dump($studentProject1);
        }

        // Récupération du premier student.
        $student = $studentRepository->findAll()[10];
        dump($student);
        // Changement du téléphone du student.
        $student->setPhone('0687654321');
        // Changement de relation avec une school year.
        $student->setSchoolYear($schoolYear);
        // Suppression d'une relation avec un project.
        $student->removeProject($project1);
        // Ajout d'une relation avec un project.
        $student->addProject($project2);
        // Ajout d'une relation avec un project.
        $student->addProject($project3);
        // Exécution des requêtes.
        // C-à-d envoi de la requête SQL à la BDD.
        $entityManager->flush();
        dump($student);

        // Récupération de la liste complète des users.
        $users = $userRepository->findAll();
        dump($users);

        // Récupération de la liste complète des users qui ont le rôle ROLE_ADMIN.
        $admins = $userRepository->findAllAdmins();
        dump($admins);

        // Récupération de la liste complète des users qui ont le rôle ROLE_STUDENT.
        $studentRoles = $userRepository->findByRole('ROLE_STUDENT');

        // Récupération d'un user de rôle ROLE_STUDENT.
        $user = $studentRoles[0];
        // Récupération du profil student à partir du compte user.
        // On précise qu'on se limite aux users qui ont le rôle ROLE_STUDENT.
        $student = $studentRepository->findOneByUser($user, 'ROLE_STUDENT');
        dump($user);
        dump($student);

        // Récupération du user dont l'id est égal à 1.
        $admin = $userRepository->find(1);
        dump($admin);

        // Récupération de la liste complète des students.
        $students = $studentRepository->findAll();
        dump($students);

        // Récupération du compte user d'un student.
        $student = $students[0];
        $user = $student->getUser();
        dump($user);

        // récupération du student dont l'id est égal à 1
        $student = $studentRepository->find(1);
        dump($student);

        exit();
    }
}
