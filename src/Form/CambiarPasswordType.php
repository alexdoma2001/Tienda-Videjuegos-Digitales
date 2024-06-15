<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class CambiarPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('passwordActual', PasswordType::class, [
                'label' => 'Contraseña Actual',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese su contraseña actual',
                    ]),
                    new UserPassword([
                        'message' => 'La contraseña actual no es correcta',
                    ]),
                ],
                'mapped' => false,
            ])
            ->add('nuevaPassword', RepeatedType::class, [ //RepeatedType es precisamente para que el usuario lo tenga que ingresar 2 veces
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Nueva Contraseña',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Por favor ingrese una nueva contraseña',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Su contraseña debe tener al menos {{ limit }} caracteres',
                            'max' => 4096,
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmar Nueva Contraseña',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Por favor confirme su nueva contraseña',
                        ]),
                    ],
                ],
                'invalid_message' => 'La nueva contraseña y la confirmación no coinciden',
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}



