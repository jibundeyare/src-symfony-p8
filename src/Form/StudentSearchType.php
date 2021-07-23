<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Length;

class StudentSearchType extends AbstractType
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Affectation de l'attribut action du formulaire.
            // Les données du formulaire sont envoyées vers la route "app_search".
            ->setAction($this->urlGenerator->generate('app_search'))
            ->add('keyword', SearchType::class, [
                // Le champ n'est pas obligatoire
                'required' => false,
                'attr' => [
                    // Sélection des classes CSS
                    'class' => 'form-control mr-sm-2',
                    // Affectation d'un placeholder 
                    'placeholder' => 'Search',
                ],
                // Masquage du label (le nom) du champ avec la classe bootstrap "d-none"
                'label_attr' => [
                    'class' => 'd-none',
                ],
                // Contraintes de validation
                'constraints' => [
                    new Length([
                        // Longueur min
                        'min' => 3,
                        // Longueur max
                        'max' => 255,
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Désactivation de la protection CSRF.
            // Elle n'est pas indispensable car c'est juste un formulaire de recherche.
            'csrf_protection' => false,
            'attr' => [
                // Sélection des classes CSS
                'class' => 'form-inline my-2 my-lg-0',
                // Attribut d'accessibilité
                'aria-label' => "Search",
            ],
        ]);
    }

    // (Optionnel) Cette fonction permet de choisir le préfixe du formulaire.
    // Normalement le préfixe devrait être "student_search" et le champ
    // keyword devraient avoir un attribut name du type "student_search[keyword]".
    // En choisissant un préfixe nulle, le name du champ devient simplement "keyword".
    // public function getBlockPrefix()
    // {
    //     return null;
    // }
}