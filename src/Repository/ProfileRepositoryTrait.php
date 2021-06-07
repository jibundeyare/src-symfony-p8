<?php

namespace App\Repository;

use App\Entity\User;

// Un trait est un ensemble de variables ou de fonctions
// qui peuvent être importées dans une classe.
// Un trait permet de mutualiser une fontionnalité entre
// plusieurs classes.
// La différence avec une classe, c'est que quand un trait
// est importé, la classe n'est pas du type du trait.
// Autrement dit, un objet ne peut pas être de type trait,
// alors qu'il peut être du type d'un classe qu'il étend.
Trait ProfileRepositoryTrait
{
    /**
     * Cette fonction permet de récupérer un profil à partir d'un user
     * 
     * @return App\Entity\Client|App\Entity\Student|App\Entity\Teacher
     */
    public function findOneByUser(User $user, string $role = '')
    {
        // p comme profil
        return $this->createQueryBuilder('p')
            ->innerJoin('p.user', 'u')
            ->andWhere('p.user = :user')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('user', $user)
            ->setParameter('role', "%{$role}%")
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
