<?php

namespace App\Form;

use App\Entity\Indicator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IndicatorSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $allowedRoles = array_combine($options['allowedRoles'],$options['allowedRoles']);
        unset($allowedRoles["ROLE_ADIERAZLEAK"]);
        $builder
            ->add('requiredRoles', ChoiceType::class, [
                'label' => 'indicator.requiredRoles',
                'choices' => $allowedRoles,
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           'data_class' => null,
           'allowedRoles' => ["ROLE_ADIERAZLEAK"],
        ]);
    }
}
