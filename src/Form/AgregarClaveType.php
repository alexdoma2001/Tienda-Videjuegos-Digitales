<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AgregarClaveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codigo', TextType::class, [
                'label' => 'Clave',
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'off'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('confirmar_codigo', TextType::class, [
                'mapped' => false,
                'label' => 'Confirmar Clave',
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'off'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
