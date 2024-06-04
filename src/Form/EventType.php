<?php

// src/Form/EventType.php

namespace App\Form;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => false
            ])
            ->add('place', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Chez moi, chez max, ...'
                ]
            ])
            ->add('max_players', IntegerType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Places maximum : 4, 6, ...'
                ]
            ])
            ->add('participants', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Participants',
                'data' => [$options['user']],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'user' => null
        ]);
    }
}
