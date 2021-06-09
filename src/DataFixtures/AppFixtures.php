<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\SchoolYear;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    private $encoder;
    private $faker;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        // Création d'une instance de faker localisée en
        // français (fr) de France (FR).
        $this->faker = FakerFactory::create('fr_FR');
    }

    public static function getGroups(): array
    {
        // Cette fixture fait partie du groupe "test".
        // Cela permet de cibler seulement certains fixtures
        // quand on exécute la commande doctrine:fixtures:load.
        // Pour que la méthode statique getGroups() soit prise
        // en compte, il faut que la classe implémente
        // l'interface FixtureGroupInterface.
        return ['test'];
    }

    public function load(ObjectManager $manager)
    {
        // Définition du nombre d'objets qu'il faut créer.
        $schoolYearCount = 10;
        $studentsPerSchoolYear = 24;
        // Le nombre de students à créer dépend du nombre  de
        // school years et du nombre de students par school year.
        $studentsCount = $studentsPerSchoolYear * $schoolYearCount;
        $studentsPerProject = 3;

        // Le nombre de projects à créer dépend du nombre  de
        // students et du nombre de students par project.
        if ($studentsCount % $studentsPerProject == 0) {
            // Il y a suffisamment de projects pour chaque student.
            // C-à-d que le reste de la division euclidienne (le modulo)
            // est nulle.
            // La division renvoit un float, c'est pourquoi il est nécessaire
            // de type caster la valeur de la division en (int).
            $projectsCount = (int) ($studentsCount / $studentsPerProject);
        } else {
            // Il n'y a pas suffisamment de projects pour chaque student.
            // C-à-d que le reste de la division euclidienne (le modulo)
            // n'est pas nulle.
            // On rajoute un project supplémentaire.
            // La division renvoit un float, c'est pourquoi il est nécessaire
            // de type caster la valeur de la division en (int).
            $projectsCount = (int) ($studentsCount / $studentsPerProject) + 1;
        }

        // Appel des fonctions qui vont créer les objets dans la BDD.
        // La fonction loadAdmins() ne renvoit pas de données mais les autres
        // fontions renvoit des données qui sont nécessaires à d'autres fonctions.
        $this->loadAdmins($manager, 3);
        $schoolYears = $this->loadSchoolYears($manager, $schoolYearCount);
        // La fonction loadStudents() a besoin de la liste des school years.
        $students = $this->loadStudents($manager, $schoolYears, $studentsPerSchoolYear, $studentsCount);
        // La fonction loadProjects() a besoin de la liste des students.
        $projects = $this->loadProjects($manager, $students, $studentsPerProject, $projectsCount);
        // La fonction loadTeachers() a besoin de la liste des projects.
        $teachers = $this->loadTeachers($manager, $projects, 20);

        // @todo créer les clients et les tags

        // Exécution des requêtes.
        // C-à-d envoi de la requête SQL à la BDD.
        $manager->flush();
    }

    public function loadAdmins(ObjectManager $manager, int $count)
    {
        // Création d'un nouveau user.
        // Ici il s'agit d'un compte admin.
        $user = new User();
        $user->setEmail('admin@example.com');
        // Hachage du mot de passe.
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        // Le format de la chaîne de caractères ROLE_FOO_BAR_BAZ
        // est libre mais il vaut mieux suivre la convention
        // proposée par Symfony.
        $user->setRoles(['ROLE_ADMIN']);

        // Demande d'enregistrement d'un objet dans la BDD.
        $manager->persist($user);

        // Création de users avec des données aléatoires.
        // On démarre la boucle for avec $i = 1 et non $i = 0
        // car on a « déjà fait le premier tour » de la boucle
        // quand on a créé notre premier user ci-dessus.
        // Si le développeur demande N users, il faut retrancher
        // le user qui a été créé ci-dessus et en créer N-1
        // dans la boucle for.
        for ($i = 1; $i < $count; $i++) {
            // Création d'un nouveau user.
            $user = new User();
            $user->setEmail($this->faker->email());
            // Hachage du mot de passe.
            $password = $this->encoder->encodePassword($user, '123');
            $user->setPassword($password);
            // Le format de la chaîne de caractères ROLE_FOO_BAR_BAZ
            // est libre mais il vaut mieux suivre la convention
            // proposée par Symfony.
            $user->setRoles(['ROLE_ADMIN']);

            // Demande d'enregistrement d'un objet dans la BDD.
            $manager->persist($user);
        }
    }

    public function loadSchoolYears(ObjectManager $manager, int $count)
    {
        // Création d'un tableau qui contiendra les school years qu'on va créer.
        // La fonction va pouvoir renvoyer ce tableau pour que d'autres fonctions
        // de création d'objets puissent les utiliser.
        $schoolYears = [];

        // Création d'une nouvelle school year.
        $schoolYear = new SchoolYear();
        $schoolYear->setName('Lorem ipsum');
        // La fonction \DateTime::createFromFormat() permet de créer un objet
        // de type DateTime en fournissant une date sous forme de chaîne
        // de caractères.
        // Y : année en 4 chiffres
        // m : mois en 2 chiffres (le 0 est affiché quand m < 10)
        // d : jour en 2 chiffres (le 0 est affiché quand d < 10)
        // H : heure en 2 chiffres (le 0 est affiché quand H < 10)
        // i : minute en 2 chiffres (le 0 est affiché quand i < 10)
        // s : seconde en 2 chiffres (le 0 est affiché quand s < 10)
        $schoolYear->setStartDate(\DateTime::createFromFormat('Y-m-d H:i:s', '2010-01-01 00:00:00'));
        // Récupération de la date de début.
        $startDate = $schoolYear->getStartDate();
        // Création d'une date de fin à partir de la date de début.
        $endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $startDate->format('Y-m-d H:i:s'));
        // Ajout d'un interval de 4 mois à la date de début.
        $endDate->add(new \DateInterval('P4M'));
        $schoolYear->setEndDate($endDate);

        // Demande d'enregistrement d'un objet dans la BDD.
        $manager->persist($schoolYear);

        // Ajout de la première school year créée à la liste.
        $schoolYears[] = $schoolYear;

        // Création de school years avec des données aléatoires.
        // On démarre la boucle for avec $i = 1 et non $i = 0
        // car on a « déjà fait le premier tour » de la boucle
        // quand on a créé notre première school year ci-dessus.
        // Si le développeur demande N school years, il faut retrancher
        // la school year qui a été créé ci-dessus et en créer N-1
        // dans la boucle for.
        for ($i = 1; $i < $count; $i++) {
            // Création d'une nouvelle school year.
            $schoolYear = new SchoolYear();
            $schoolYear->setName($this->faker->name());
            // Affectation d'une date aléatoire entre maintenant et il y a 10 ans.
            $schoolYear->setStartDate($this->faker->dateTimeThisDecade());
            // Récupération de la date de début.
            $startDate = $schoolYear->getStartDate();
            // Création d'une date de fin à partir de la date de début.
            $endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $startDate->format('Y-m-d H:i:s'));
            // Ajout d'un interval de 4 mois à la date de début.
            $endDate->add(new \DateInterval('P4M'));
            $schoolYear->setEndDate($endDate);

            // Demande d'enregistrement d'un objet dans la BDD.
            $manager->persist($schoolYear);

            // Ajout de la school year créée à la liste.
            $schoolYears[] = $schoolYear;
        }

        // Renvoi de la liste des school years créées.
        return $schoolYears;
    }

    public function loadStudents(ObjectManager $manager, array $schoolYears, int $studentsPerSchoolYear, int $count)
    {
        // Création d'un tableau qui contiendra les students qu'on va créer.
        // La fonction va pouvoir renvoyer ce tableau pour que d'autres fonctions
        // de création d'objets puissent les utiliser.
        $students = [];

        // Création d'un compteur qui contient l'index de la school year en cours
        // dans le tableau des school years.
        // Assez logiquement, le premier index est 0 car on commence par
        // utiliser la première school year.
        $schoolYearIndex = 0;

        // Récupération d'une school year précisée par l'index $schoolYearIndex.
        $schoolYear = $schoolYears[$schoolYearIndex];

        // Création d'un nouveau user.
        $user = new User();
        $user->setEmail('student@example.com');
        // Hachage du mot de passe.
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        // Le format de la chaîne de caractères ROLE_FOO_BAR_BAZ
        // est libre mais il vaut mieux suivre la convention
        // proposée par Symfony.
        $user->setRoles(['ROLE_STUDENT']);

        // Demande d'enregistrement d'un objet dans la BDD.
        $manager->persist($user);

        // Création d'un nouveau student.
        $student = new Student();
        $student->setFirstname('Student');
        $student->setLastname('Student');
        $student->setPhone('0612345678');
        // Association d'un student et d'une school year
        $student->setSchoolYear($schoolYear);
        // Association d'un student et d'un user.
        $student->setUser($user);

        // Demande d'enregistrement d'un objet dans la BDD
        $manager->persist($student);

        // Ajout du premier student créé à la liste.
        $students[] = $student;

        // Création de students avec des données aléatoires.
        // On démarre la boucle for avec $i = 1 et non $i = 0
        // car on a « déjà fait le premier tour » de la boucle
        // quand on a créé notre premier student ci-dessus.
        // Si le développeur demande N students, il faut retrancher
        // le student qui a été créé ci-dessus et en créer N-1
        // dans la boucle for.
        for ($i = 1; $i < $count; $i++) {
            // Récupération d'une school year précisée par l'index $schoolYearIndex.
            $schoolYear = $schoolYears[$schoolYearIndex];

            // Si le nombre de student par school year pour la school year
            // en cours a été atteint, on incrémente l'index des school years
            // pour que la school year suivante soit utilisée au prochain tour. 
            if ($i % $studentsPerSchoolYear == 0) {
                $schoolYearIndex++;
            }

            // Création d'un nouveau user.
            $user = new User();
            $user->setEmail($this->faker->email());
            // Hachage du mot de passe.
            $password = $this->encoder->encodePassword($user, '123');
            $user->setPassword($password);
            // Le format de la chaîne de caractères ROLE_FOO_BAR_BAZ
            // est libre mais il vaut mieux suivre la convention
            // proposée par Symfony.
            $user->setRoles(['ROLE_STUDENT']);

            // Demande d'enregistrement d'un objet dans la BDD
            $manager->persist($user);

            // Création d'un nouveau student.
            $student = new Student();
            $student->setFirstname($this->faker->firstname());
            $student->setLastname($this->faker->lastname());
            $student->setPhone($this->faker->phoneNumber());
            // Association d'un student et d'une school year.
            $student->setSchoolYear($schoolYear);
            // Association d'un student et d'un user.
            $student->setUser($user);

            // Demande d'enregistrement d'un objet dans la BDD.
            $manager->persist($student);

            // Ajout du student créé à la liste.
            $students[] = $student;
        }

        // Renvoi de la liste des students créés.
        return $students;
    }

    public function loadProjects(ObjectManager $manager, array $students, int $studentsPerProject, int $count)
    {
        // Création d'un tableau qui contiendra les projects qu'on va créer.
        // La fonction va pouvoir renvoyer ce tableau pour que d'autres fonctions
        // de création d'objets puissent les utiliser.
        $projects = [];

        // Création d'un compteur qui contient l'index du student en cours
        // dans le tableau des students.
        // Assez logiquement, le premier index est 0 car on commence par
        // utiliser le premier student.
        $studentIndex = 0;

        // Création d'un nouveau project.
        $project = new Project();
        $project->setName('Hackathon');

        // Association d'un project et de plusieurs students.
        while (true) {
            // Récupération d'un student précisé par l'index $studentIndex.
            $student = $students[$studentIndex];
            // Association d'un project et d'un student.
            $project->addStudent($student);

            // Incrémentation de l'index des students.
            // Au prochain tour, c'est le student suivant qui sera utilisé.
            $studentIndex++;

            // Si le nombre de student par project pour le project
            // en cours a été atteint, on sort de la boucle. 
            if (($studentIndex + 1) % $studentsPerProject == 0) {
                break;
            }
        }

        // Demande d'enregistrement d'un objet dans la BDD.
        $manager->persist($project);

        // Ajout du premier project créé à la liste.
        $projects[] = $project;

        // Création de projects avec des données aléatoires.
        // On démarre la boucle for avec $i = 1 et non $i = 0
        // car on a « déjà fait le premier tour » de la boucle
        // quand on a créé notre premier project ci-dessus.
        // Si le développeur demande N projects, il faut retrancher
        // le project qui a été créé ci-dessus et en créer N-1
        // dans la boucle for.
        for ($i = 1; $i < $count; $i++) {
            // Création d'un nouveau project.
            $project = new Project();
            // Affectation d'une phrase aléatoire d'environ 2 mots.
            $project->setName($this->faker->sentence(2));

            // Association d'un project et de plusieurs students.
            while (true) {
                // Récupération d'un student précisé par l'index $studentIndex.
                $student = $students[$studentIndex];
                // Association d'un project et d'un student.
                $project->addStudent($student);

                // Incrémentation de l'index des students.
                // Au prochain tour, c'est le student suivant qui sera utilisé.
                $studentIndex++;

                // Si le nombre de student par project pour le project
                // en cours a été atteint, on sort de la boucle. 
                if (($studentIndex + 1) % $studentsPerProject == 0) {
                    break;
                }
            }
        
            // Demande d'enregistrement d'un objet dans la BDD.
            $manager->persist($project);

            // Ajout du project créé à la liste.
            $projects[] = $project;
        }

        // Renvoi de la liste des projects créés.
        return $projects;
    }

    public function loadTeachers(ObjectManager $manager, array $projects, int $count)
    {
        // Création d'un tableau qui contiendra les teachers qu'on va créer.
        // La fonction va pouvoir renvoyer ce tableau pour que d'autres fonctions
        // de création d'objets puissent les utiliser.
        $teachers = [];

        // Création d'un nouveau user.
        $user = new User();
        $user->setEmail('teacher@example.com');
        // Hachage du mot de passe.
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        // Le format de la chaîne de caractères ROLE_FOO_BAR_BAZ
        // est libre mais il vaut mieux suivre la convention
        // proposée par Symfony.
        $user->setRoles(['ROLE_TEACHER']);

        // Demande d'enregistrement d'un objet dans la BDD.
        $manager->persist($user);

        // Création d'un nouveau teacher.
        $teacher = new Teacher();
        $teacher->setFirstname('Teacher');
        $teacher->setLastname('Teacher');
        $teacher->setPhone('0612345678');
        // Association d'un teacher et d'un user.
        $teacher->setUser($user);

        // Récupération du premier project de la liste
        // et suppression de ce project de la liste.
        $firstProject = array_shift($projects);
        // Association d'un teacher et d'un project.
        $teacher->addProject($firstProject);

        // Demande d'enregistrement d'un objet dans la BDD.
        $manager->persist($teacher);

        // Ajout du premier teacher créé à la liste.
        $teachers[] = $teacher;

        // Création de teachers avec des données aléatoires.
        // On démarre la boucle for avec $i = 1 et non $i = 0
        // car on a « déjà fait le premier tour » de la boucle
        // quand on a créé notre premier teacher ci-dessus.
        // Si le développeur demande N teachers, il faut retrancher
        // le teacher qui a été créé ci-dessus et en créer N-1
        // dans la boucle for.
        for ($i = 1; $i < $count; $i++) {
            // Création d'un nouveau user.
            $user = new User();
            $user->setEmail($this->faker->email());
            // Hachage du mot de passe.
            $password = $this->encoder->encodePassword($user, '123');
            $user->setPassword($password);
            // Le format de la chaîne de caractères ROLE_FOO_BAR_BAZ
            // est libre mais il vaut mieux suivre la convention
            // proposée par Symfony.
            $user->setRoles(['ROLE_TEACHER']);

            // Demande d'enregistrement d'un objet dans la BDD.
            $manager->persist($user);

            // Création d'un nouveau teacher.
            $teacher = new Teacher();
            $teacher->setFirstname($this->faker->firstname());
            $teacher->setLastname($this->faker->lastname());
            $teacher->setPhone($this->faker->phoneNumber());
            // Association d'un teacher et d'un user.
            $teacher->setUser($user);

            // Génération aléatoire du nombre de projects associés au teacher.
            $projectsCount = random_int(0, 10);
            // Création d'une liste aléatoire de projects.
            // Cette liste contient exactement le nombre de projects
            // précisé par $projectsCount.
            $randomProjects = $this->faker->randomElements($projects, $projectsCount);

            // Association d'un teacher et de plusieurs projects.
            foreach ($randomProjects as $randomProject) {
                // Association d'un teacher et d'un project.
                $teacher->addProject($randomProject);
            }

            // Demande d'enregistrement d'un objet dans la BDD.
            $manager->persist($teacher);

            // Ajout du teacher créé à la liste.
            $teachers[] = $teacher;    
        }

        // Renvoi de la liste des teachers créés.
        return $teachers;
    }
}
