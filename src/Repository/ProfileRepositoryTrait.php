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
     * Cette fonction permet de récupérer un profil à partir d'un user.
     * Un profil n'est pas un type de données strict, c'est juste une appelation
     * qui permet de désigner un objet de type Client, Student ou Teacher.
     * 
     * @param $role string optional nom d'un rôle comme 'ROLE_ADMIN', 'ROLE_STUDENT', etc
     * @return App\Entity\Client|App\Entity\Student|App\Entity\Teacher
     */
    public function findOneByUser(User $user, string $role = '')
    {
        // 'p' sera l'alias qui permet de désigner un profil
        return $this->createQueryBuilder('p')
            ->innerJoin('p.user', 'u')
            // ajout d'un filtre qui ne retient que le profil
            // qui possède une relation avec la variable :user
            ->andWhere('p.user = :user')
            // ajout d'un filtre qui ne retient que les users
            // qui contiennent (opérateur LIKE) la chaîne de
            // caractères contenue dans la variable :role
            ->andWhere('u.roles LIKE :role')
            // affactation d'une valeur à la variable :user
            ->setParameter('user', $user)
            // affactation d'une valeur à la variable :role
            // le symbole % est joker qui veut dire
            // « match toutes les chaînes de caractères »
            ->setParameter('role', "%{$role}%")
            // récupération d'une requête qui n'attend qu'à être exécutée
            ->getQuery()
            // exécution de la requête
            // récupération d'une variable qui peut contenir
            // un profil ou la valeur nulle
            ->getOneOrNullResult()
        ;
    }
}
