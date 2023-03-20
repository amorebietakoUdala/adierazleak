<?php

namespace App\Form;

use App\Entity\Indicator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IndicatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $readonly = $options['readonly'];
        $allowedRoles = array_combine($options['allowedRoles'],$options['allowedRoles']);
        $builder
            ->add('id', HiddenType::class)
            ->add('descriptionEs', null,[
                'label' => 'indicator.descriptionEs',
                'disabled' => $readonly,
            ])
            ->add('descriptionEu', null,[
                'label' => 'indicator.descriptionEu',
                'disabled' => $readonly,
            ])
            ->add('requiredRoles', ChoiceType::class, [
                'label' => 'indicator.requiredRoles',
                'choices' => $allowedRoles,
                'multiple' => true,
                'expanded' => false,
                'disabled' => $readonly,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Indicator::class,
            'readonly' => false,
            'allowedRoles' => ["ROLE_ADIERAZLEAK"],
        ]);
    }
}
