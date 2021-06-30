<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\SchoolYear;
use App\Entity\Student;
use App\Entity\Tag;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', RegistrationFormType::class, [
                'label_attr' => [
                    'class' => 'd-none',
                ]
            ])
            ->add('firstname')
            ->add('lastname')
            ->add('phone')
            ->add('schoolYear', EntityType::class, [
                // looks for choices from this entity
                'class' => SchoolYear::class,

                // uses the User.username property as the visible option string
                'choice_label' => function (SchoolYear $schoolYear) {
                    return "{$schoolYear->getName()} {$schoolYear->getStartDate()->format('m/Y')}";
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC')
                    ;
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
