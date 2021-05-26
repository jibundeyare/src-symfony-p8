<?php

namespace App\DataFixtures;

use App\Entity\Student;
use App\Entity\User;
use App\Entity\SchoolYear;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    private $faker;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = \Faker\Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager)
    {
        $schoolYears = $this->loadSchoolYears($manager);
        $students = $this->loadStudents($manager, $schoolYears);

        $manager->flush();
    }

    public function loadSchoolYears(ObjectManager $manager)
    {
        $schoolYear = new SchoolYear();
        $schoolYear->setName($this->faker->name());
        $schoolYear->setStartDate($this->faker->dateTimeThisDecade());
        // récupération de la date de début
        $startDate = $schoolYear->getStartDate();
        // ajout d'un interval de 4 mois à la date de début
        $endDate = $startDate->add(new \DateInterval('P4M'));
        $schoolYear->setEndDate($endDate);

        $manager->persist($schoolYear);

        return [$schoolYear];
    }

    public function loadStudents(ObjectManager $manager, array $schoolYears)
    {
        $schoolYear = $schoolYears[0];

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

        return [$student];
    }
}
