<?php

namespace App\Service;

use App\Form\StudentSearchType;
// La classe "FormFactoryInterface" permet de créer des formulaire
use Symfony\Component\Form\FormFactoryInterface;

class StudentSearchFormViewFactory
{
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function create()
    {
        // Création du formulaire de recherche d'un student puis
        // création de la vue du formulaire.
        return $this->formFactory
            ->create(StudentSearchType::class)
            ->createView()
        ;
    }
}