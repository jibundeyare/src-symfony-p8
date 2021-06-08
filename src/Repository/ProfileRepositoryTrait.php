<?php

namespace App\Repository;

use App\Entity\User;

// Un trait est un ensemble de variables ou de fonctions
// qui peuvent être importées dans une classe.
// Un trait permet de mutualiser des fonctionnalités entre
// plusieurs classes.
// La différence avec une classe, c'est que quand un trait
// est importé, la classe n devient pas du type du trait.
// Autrement dit, un objet ne peut pas être du type d'un
// trait, alors qu'il peut être du type d'une classe.
Trait ProfileRepositoryTrait
{
    /**
     * Cette fonction permet de récupérer un profil à partir d'un user.
     * Un profil n'est pas un type de données strict, c'est juste une
     * appelation qui permet de désigner un objet de type Client,
     * Student ou Teacher.
     *
     * L'annotation param permet de donner des indications sur le
     * paramètre d'une fonction.
     * @param $role string optional nom d'un rôle comme 'ROLE_ADMIN', 'ROLE_STUDENT', etc
     * L'annotation return permet de donner des indications sur la
     * valeur de retour d'une fonction. La barre verticale dans
     * Foo|Bar|Baz permet de préciser que la valeur de retour est de
     * type Foo ou Bar ou Baz.
     * @return App\Entity\Client|App\Entity\Student|App\Entity\Teacher
     */
    public function findOneByUser(User $user, string $role = '')
    {
        // 'p' sera l'alias qui permet de désigner un profil
        return $this->createQueryBuilder('p')
            // Demande de jointure de l'objet user.
            // 'u' sera l'alias qui permet de désigner un user.
            ->innerJoin('p.user', 'u')
            // Ajout d'un filtre qui ne retient que le profil
            // qui possède une relation avec la variable :user.
            ->andWhere('p.user = :user')
            // Ajout d'un filtre qui ne retient que les users
            // qui contiennent (opérateur LIKE) la chaîne de
            // caractères contenue dans la variable :role.
            ->andWhere('u.roles LIKE :role')
            // Affectation d'une valeur à la variable :user.
            ->setParameter('user', $user)
            // Affectation d'une valeur à la variable :role.
            // Le symbole % est joker qui veut dire
            // « match toutes les chaînes de caractères ».
            ->setParameter('role', "%{$role}%")
            // Récupération d'une requête qui n'attend qu'à être exécutée.
            ->getQuery()
            // Exécution de la requête.
            // Récupération d'une variable qui peut contenir
            // un profil ou la valeur nulle.
            ->getOneOrNullResult()
        ;
    }
}
