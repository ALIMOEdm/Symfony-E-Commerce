<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('category', EntityType::class, array(
                'label' => 'Category',
                'class' => 'AdminBundle:Category',
//                'property' => 'title',
                'query_builder' => function(CategoryRepository $er) {
                    return $er->createQueryBuilder('e');
                },
                'attr' => array(
                    'class' => 'form-control',
                    'data-role' => 'good_create_category_list',
                ),
            ))
            ->add('short_title', TextType::class, array(
                'attr' => array('class' => 'form-control', 'placeholder' => '', 'data-role' => ''),
                'required' => true,
            ))
            ->add('title', TextType::class, array(
                'attr' => array('class' => 'form-control', 'placeholder' => '', 'data-role' => ''),
                'required' => true,
            ))
            ->add('xml_title', TextType::class, array(
                'attr' => array('class' => 'form-control', 'placeholder' => '', 'data-role' => ''),
                'required' => true,
            ))
            ->add('description', TextareaType::class, array(
                'attr' => array('class' => 'form-control', 'placeholder' => '', 'data-role' => ''),
                'required' => true,
            ))
            ->add('brand', TextType::class, array(
                'attr' => array('class' => 'form-control', 'placeholder' => '', 'data-role' => ''),
                'required' => true,
            ))
            ->add('article', TextType::class, array(
                'attr' => array('class' => 'form-control', 'placeholder' => '', 'data-role' => ''),
                'required' => true,
            ))
            ->add('images_stock', FileType::class, array(
                'attr' => array('class' => '', 'placeholder' => '', 'data-role' => 'good_image_field', 'multiple' => "multiple"),
                'required' => false,
            ))
            ->add('images_hidden_field', HiddenType::class, array(
                'attr' => array('class' => '', 'placeholder' => '', 'data-role' => 'good_image_hidden_field'),
                'required' => false,
            ))
            ->add('extra_fields_cache',  HiddenType::class, array(
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