<?php

namespace AdminBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('title', TextType::class, array(
                'label' => 'Наименование',
                'attr' => array('class' => 'form-control'),
            ))
            ->add('xml_title', TextType::class, array(
                'label' => 'Наименование для xml',
                'attr' => array('class' => 'form-control'),
            ))
            ->add('content', TextareaType::class, array(
                'label' => 'Контент',
                'attr' => array('class' => 'form-control'),
            ))
            ->add('parent_category', EntityType::class,  array(
                'label' => 'Родительская категория',
                'attr' => array('class' => 'form-control'),
                'class' => 'AdminBundle:Category',
                'multiple' => false,
                'expanded' => false,
                'required' => false,
            ))
            ->add('extra_fields', EntityType::class, array(
                'label' => 'Дополнительные поля',
                'attr' => array('class' => 'form-control form-group-checkboxes', 'data-role' => 'category-checkbox-group'),
                'class' => 'AdminBundle:Extra_fields',
                'multiple' => true,
                'expanded' => true))
            ->add('file', FileType::class, array(
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
            'data_class' => 'AdminBundle\Entity\Category'
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
