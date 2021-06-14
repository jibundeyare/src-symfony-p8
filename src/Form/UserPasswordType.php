<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                // Le champ plainPassword ne correspond à aucun attribut de
                // l'entité User. C'est pourquoi il ne doit pas être affecté
                // à l'instance de l'entité. L'option 'mapped' => false
                // permet de désactiver l'affectation automatique. 
                'mapped' => false,
                'type' => PasswordType::class,
                // 'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => [
                    'class' => 'password-field',
                    'autocomplete' => 'new-password'
                ]],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
                'constraints' => [
                    // Obligation de valeurs de longueur comprise entre 6 et 190.
                    new Length([
                        'min' => 6,
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
