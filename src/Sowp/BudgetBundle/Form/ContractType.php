<?php

namespace Sowp\BudgetBundle\Form;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Form\Transformer\EntityToIdObjectTransformer;
use Sowp\BudgetBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractType extends AbstractType
{
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('conclusionAt', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy'
                ]
            ])
            ->add('supplier')
            ->add('title')
            ->add('value');

        if($options['with_category']){
            if(!$options['api']){
                $builder->add('category');
            }else{
                $categoryTransformer = new EntityToIdObjectTransformer($this->om, Category::class);
                $builder->add($builder->create('category', TextType::class)->addModelTransformer($categoryTransformer));
            }
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
            'api' => false,
        ]);
    }
}
