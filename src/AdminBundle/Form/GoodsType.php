<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GoodsType extends AbstractType{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', 'entity', array(
                'label' => 'Category',
                'class' => 'AdminBundle:Category',
                'property' => 'title',
                'query_builder' => function(CategoryRepository $er) {
                    return $er->createQueryBuilder('e');
                },
                'empty_value' => 'Без категории',
                'attr' => array(
                    'class' => 'form-control',
                    'data-role' => 'good_create_category_list',
                ),
            ))
            ->add('short_title', 'text', array(
                'attr' => array('class' => 'form-control', 'placeholder' => '', 'data-role' => ''),
                'required' => true,
            ))
            ->add('title', 'text', array(
                'attr' => array('class' => 'form-control', 'placeholder' => '', 'data-role' => ''),
                'required' => true,
            ))
            ->add('xml_title', 'text', array(
                'attr' => array('class' => 'form-control', 'placeholder' => '', 'data-role' => ''),
                'required' => true,
            ))
            ->add('description', 'textarea', array(
                'attr' => array('class' => 'form-control', 'placeholder' => '', 'data-role' => ''),
                'required' => true,
            ))
            ->add('brand', 'text', array(
                'attr' => array('class' => 'form-control', 'placeholder' => '', 'data-role' => ''),
                'required' => true,
            ))
            ->add('article', 'text', array(
                'attr' => array('class' => 'form-control', 'placeholder' => '', 'data-role' => ''),
                'required' => true,
            ))
            ->add('images_stock', 'file', array(
                'attr' => array('class' => '', 'placeholder' => '', 'data-role' => 'good_image_field', 'multiple' => "multiple"),
                'required' => false,
            ))
            ->add('images_hidden_field', 'hidden', array(
                'attr' => array('class' => '', 'placeholder' => '', 'data-role' => 'good_image_hidden_field'),
                'required' => false,
            ))
            ->add('extra_fields_cache', 'hidden', array(
                'attr' => array('class' => '', 'placeholder' => '', 'data-role' => 'good_hidden_extra_fields'),
                'required' => false,
            ))
        ;
    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\Goods',
            'cascade_validation' => true,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_good';
    }
}