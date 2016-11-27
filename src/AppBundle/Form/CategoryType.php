<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Наименование',
                'attr' => array('class' => 'form-control'),
            ))
            ->add('xml_title', 'text', array(
                'label' => 'Наименование для xml',
                'attr' => array('class' => 'form-control'),
            ))
            ->add('content', 'textarea', array(
                'label' => 'Контент',
                'attr' => array('class' => 'form-control'),
            ))
            ->add('parent_category', 'entity',  array(
                'label' => 'Родительская категория',
                'attr' => array('class' => 'form-control'),
                'class' => 'AppBundle:Category',
                'empty_value' => 'Без категории',
                'multiple' => false,
                'expanded' => false,
                'required' => false,
            ))
            ->add('extra_fields','entity', array(
                'label' => 'Дополнительные поля',
                'attr' => array('class' => 'form-control form-group-checkboxes', 'data-role' => 'category-checkbox-group'),
                'class' => 'AppBundle:Extra_fields',
                'multiple' => true,
                'expanded' => true))
            ->add('file', 'file', array(
                'required' => false,
                'label' => 'Изображение',
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Category'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_category';
    }
}
