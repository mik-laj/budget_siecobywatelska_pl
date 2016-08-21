<?php

namespace Sowp\BudgetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('conclusionAt', DateTimeType::class)
            ->add('supplier')
            ->add('title')
            ->add('value');

        if($options['with_category']){
            $builder->add('category');
        }

    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Sowp\BudgetBundle\Entity\Contract',
            'with_category' => true,
        ]);
    }
}
