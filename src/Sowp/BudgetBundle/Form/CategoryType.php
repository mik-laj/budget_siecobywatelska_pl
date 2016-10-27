<?php

namespace Sowp\BudgetBundle\Form;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Form\Transformer\EntityToIdObjectTransformer;
use Sowp\BudgetBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
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
            ->add('title');

        if(!$options['api']){
            $builder->add('parent');
        }else{
            $categoryTransformer = new EntityToIdObjectTransformer($this->om, Category::class);
            $builder->add($builder->create('parent', TextType::class)->addModelTransformer($categoryTransformer));
        }

    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sowp\BudgetBundle\Entity\Category',
            'api' => false
        ));
    }
}
