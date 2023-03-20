<?php

namespace App\Form;

use App\Entity\Indicator;
use App\Entity\Observation;
use App\Repository\IndicatorRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $readonly = $options['readonly'];
        $isAdmin = $options['isAdmin'];
        $locale = $options['locale'];
        $allowedRoles = array_combine($options['allowedRoles'],$options['allowedRoles']);
        $builder
            ->add('id', HiddenType::class)
            ->add('year',null,[
                'disabled' => $readonly,
                'label' => 'observation.year',
            ])
            ->add('month',null,[
                'disabled' => $readonly,
                'label' => 'observation.month',
            ])
            ->add('indicator', EntityType::class, [
                'class' => Indicator::class,
                'query_builder' => function (IndicatorRepository $er) use ($allowedRoles, $isAdmin) {
                    if (!$isAdmin) {
                        return $er->findByRolesQB($allowedRoles);
                    } else {
                        return $er->findAllQB();
                    }
                },
                'label' => 'observation.indicator',
                'choice_label' => function ($indicator) use ($locale) {
                    return $locale === 'es' ? $indicator->getDescriptionEs() : $indicator->getDescriptionEu();
                },
                'disabled' => $readonly,
            ])
            ->add('value', NumberType::class,[
                'disabled' => $readonly,
                'label' => 'observation.value',
            ])
            ->add('notes', null,[
                'disabled' => $readonly,
                'label' => 'observation.notes',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Observation::class,
            'readonly' => false,
            'locale' => 'eu',
            'allowedRoles' => ["ROLE_ADIERAZLEAK"],
            'isAdmin' => false,
        ]);
    }
}
