<?php

// src/Form/IngresarSaldoType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Range;

class IngresarSaldoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numeroTarjeta', IntegerType::class, [
                'label' => 'Número de Tarjeta',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese el número de tarjeta',
                    ]),
                    new Regex([ //Es para meter expresiones regulares
                        'pattern' => '/^\d{12}$/',
                        'message' => 'Su tarjeta debe tener exactamente 12 caracteres',
                    ]),
                ],
            ])
            ->add('fechaValidez', DateType::class, [
                'label' => 'Fecha de Validez',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese la fecha de validez',
                    ]),
                ],
            ])
            ->add('cvc', IntegerType::class, [
                'label' => 'CVC',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese el CVC',
                    ]),
                    new Regex([
                        'pattern' => '/^\d{3}$/',
                        'message' => 'El CVC debe tener exactamente 3 dígitos',
                    ]),
                ],
            ])
            ->add('dineroIngresar', IntegerType::class, [
                'label' => 'Dinero a Ingresar',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese el dinero a ingresar',
                    ]),
                    new Range([
                        'min' => 1,
                        'minMessage' => 'El dinero a ingresar debe ser más de {{ limit }}',
                        'notInRangeMessage' => 'El dinero a ingresar debe ser al menos {{ limit }}',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}




