<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // Cette fonction n'est pas indispensable car on pourrait remplacer
    // son utilisation par un appel à $repository->findByRole('ROLE_ADMIN').
    // Mais elle présente l'avantage de provoquer une erreur de PHP si nous
    // nous trompons dans la syntaxe quand nous l'appelons. Par exemple
    // appeler $repository->findAllAmins() sans 'd' provoquera une erreur fatale.
    // Alors qu'appeler $repository->findByRole('ROLE_AMIN') ne provoquera pas
    // d'erreur fatale et sera donc plus difficile à déboguer.
    // En fonction des besoins, on peut décliner les autres fonctions :
    // findAllStudents(), findAllTeachers(), findAllClients(), etc.
    public function findAllAdmins()
    {
        return $this->findByRole('ROLE_ADMIN');
    }

    /**
     * @param $role string nom d'un rôle comme 'ROLE_ADMIN', 'ROLE_STUDENT', etc
     * @return User[] Returns an array of User objects
     */
    public function findByRole(string $role)
    {
        // Cette requête génère le code DQL suivant :
        // "SELECT u FROM App\Entity\User u WHERE u.roles LIKE :role ORDER BY u.email ASC"
        // 'u' sera l'alias qui permet de désigner un user.
        return $this->createQueryBuilder('u')
            // Ajout d'un filtre qui ne retient que les users
            // qui contiennent (opérateur LIKE) la chaîne de
            // caractères contenue dans la variable :role.
            ->andWhere('u.roles LIKE :role')
            // Affactation d'une valeur à la variable :role.
            // Le symbole % est joker qui veut dire
            // « match toutes les chaînes de caractères ».
            ->setParameter('role', "%{$role}%")
            // Tri par adresse email en ordre croissant (a, b, c, ...).
            ->orderBy('u.email', 'ASC')
            // Récupération d'une requête qui n'attend qu'à être exécutée.
            ->getQuery()
            // Exécution de la requête.
            // Récupération d'un tableau de résultat.
            // Ce tableau peut contenir, zéro, un ou plusieurs lignes.
            ->getResult()
        ;
    }

    // /**
    //  * @return Student[] Returns an array of Student objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
