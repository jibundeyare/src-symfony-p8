<?php

namespace App\DataFixtures;

use App\Entity\Student;
use App\Entity\User;
use App\Entity\SchoolYear;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    private $faker;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = FakerFactory::create('fr_FR');
    }

    public function load(ObjectManager $manager)
    {
        $schoolYearCount = 10;
        $studentsPerSchoolYear = 25;

        $this->loadAdmins($manager, 3);
        $schoolYears = $this->loadSchoolYears($manager, $schoolYearCount);
        $students = $this->loadStudents($manager, $schoolYears, $studentsPerSchoolYear, $studentsPerSchoolYear * $schoolYearCount);

        $manager->flush();
    }

    public function loadAdmins(ObjectManager $manager, int $count)
    {
        $user = new User();
        $user->setEmail('admin@example.com');
        // hashage du mot de passe
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        $user->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        for ($i = 1; $i < $count; $i++) {
            $user = new User();
            $user->setEmail($this->faker->email());
            // hashage du mot de passe
            $password = $this->encoder->encodePassword($user, '123');
            $user->setPassword($password);
            $user->setRoles(['ROLE_ADMIN']);

            $manager->persist($user);
        }
    }

    public function loadSchoolYears(ObjectManager $manager, int $count)
    {
        $schoolYears = [];

        $schoolYear = new SchoolYear();
        $schoolYear->setName('Lorem ipsum');
        $schoolYear->setStartDate(\DateTime::createFromFormat('Y-m-d H:i:s', '2010-01-01 00:00:00'));
        // récupération de la date de début
        $startDate = $schoolYear->getStartDate();
        // création de la date de fin à  partir de la date de début
        $endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $startDate->format('Y-m-d H:i:s'));
        // ajout d'un interval de 4 mois à la date de début
        $endDate->add(new \DateInterval('P4M'));
        $schoolYear->setEndDate($endDate);

        $manager->persist($schoolYear);
        $schoolYears[] = $schoolYear;

        for ($i = 1; $i < $count; $i++) {
            $schoolYear = new SchoolYear();
            $schoolYear->setName($this->faker->name());
            $schoolYear->setStartDate($this->faker->dateTimeThisDecade());
            // récupération de la date de début
            $startDate = $schoolYear->getStartDate();
            // création de la date de fin à  partir de la date de début
            $endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $startDate->format('Y-m-d H:i:s'));
            // ajout d'un interval de 4 mois à la date de début
            $endDate->add(new \DateInterval('P4M'));
            $schoolYear->setEndDate($endDate);

            $manager->persist($schoolYear);
            $schoolYears[] = $schoolYear;
        }

        return $schoolYears;
    }

    public function loadStudents(ObjectManager $manager, array $schoolYears, int $studentsPerSchoolYear, int $count)
    {
        $students = [];
        $schoolYearIndex = 0;

        $schoolYear = $schoolYears[$schoolYearIndex];

        $user = new User();
        $user->setEmail('student@example.com');
        // hashage du mot de passe
        $password = $this->encoder->encodePassword($user, '123');
        $user->setPassword($password);
        $user->setRoles(['ROLE_STUDENT']);

        $manager->persist($user);

        $student = new Student();
        $student->setFirstname('Student');
        $student->setLastname('Student');
        $student->setPhone('0612345678');
        $student->setSchoolYear($schoolYear);
        $student->setUser($user);

        $manager->persist($student);
        $students[] = $student;

        for ($i = 1; $i <= $count; $i++) {
            $schoolYear = $schoolYears[$schoolYearIndex];

            if ($i % $studentsPerSchoolYear == 0) {
                $schoolYearIndex++;
            }

            $user = new User();
            $user->setEmail($this->faker->email());
            // hashage du mot de passe
            $password = $this->encoder->encodePassword($user, '123');
            $user->setPassword($password);
            $user->setRoles(['ROLE_STUDENT']);

            $manager->persist($user);

            $student = new Student();
            $student->setFirstname($this->faker->firstname());
            $student->setLastname($this->faker->lastname());
            $student->setPhone($this->faker->phoneNumber());
            $student->setSchoolYear($schoolYear);
            $student->setUser($user);

            $manager->persist($student);
            $students[] = $student;
        }

        return $students;
    }
}
