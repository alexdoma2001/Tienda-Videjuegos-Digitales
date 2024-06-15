<?php

namespace App\Form;

use App\Entity\Videojuego;
use App\Entity\Categoria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class AgregarVideojuegoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre del Videojuego',
                'attr' => ['class' => 'form-control']
            ])
            ->add('descripcion', TextType::class, [
                'label' => 'Descripción',
                'attr' => ['class' => 'form-control']
            ])
            ->add('precio', NumberType::class, [
                'label' => 'Precio del juego',
                'scale' => 2,
                'attr' => ['class' => 'form-control', 'step' => '0.01'], //Para que me admita decimales
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese el dinero a ingresar',
                    ]),
                    new Range([
                        'min' => 0,
                        'minMessage' => 'El dinero a ingresar debe ser igual o más de {{ limit }}',
                        'notInRangeMessage' => 'El dinero a ingresar debe ser al menos {{ limit }}',
                    ]),
                ],
            ])
            ->add('urlFoto', TextType::class, [
                'label' => 'URL de la Foto',
                'attr' => ['class' => 'form-control']
            ])
            ->add('categoria', EntityType::class, [
                'class' => Categoria::class,
                'choice_label' => 'nombre',
                'label' => 'Categoría',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Videojuego::class,
        ]);
    }
}
