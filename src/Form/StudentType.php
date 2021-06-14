<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\SchoolYear;
use App\Entity\Student;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
            ->add('projects', EntityType::class, [
                // looks for choices from this entity
                'class' => Project::class,
            
                // uses the User.username property as the visible option string
                'choice_label' => function (Project $project) {
                    $clients = $project->getClients();

                    $clientNames = '';

                    foreach ($clients as $client) {
                        if ($clientNames) {
                            $clientNames .= ', ';
                        }

                        $clientNames .= "{$client->getFirstname()} {$client->getLastname()}";
                    }

                    $result  = '';

                    if ($clientNames) {
                        $result = "{$project->getName()} ({$clientNames})";
                    } else {
                        $result = "{$project->getName()}";
                    }

                    return $result;
                },
            
                // used to render a select box, check boxes or radios
                'multiple' => true,
                // 'expanded' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC')
                    ;
                },
            ])
            ->add('tags', EntityType::class, [
                // looks for choices from this entity
                'class' => Tag::class,
            
                // uses the User.username property as the visible option string
                'choice_label' => 'name',

                // used to render a select box, check boxes or radios
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('user', EntityType::class, [
                // looks for choices from this entity
                'class' => User::class,
            
                // uses the User.username property as the visible option string
                'choice_label' => 'username',
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
