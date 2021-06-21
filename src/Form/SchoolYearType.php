<?php

namespace App\Form;

use App\Entity\SchoolYear;
use App\Entity\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolYearType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('startDate')
            ->add('endDate')
            ->add('teachers', EntityType::class, [
                'class' => Teacher::class,
                // La fonction anonyme doit renvoyer une chaîne de caractères
                // qui contient le texte qui sera utilisé dans le menu déroulant. 
                'choice_label' => function(Teacher $teacher) {
                    // On renvoit les attributs firstname et lastname.
                    return "{$teacher->getFirstname()} {$teacher->getLastname()}";
                },
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SchoolYear::class,
        ]);
    }
}
