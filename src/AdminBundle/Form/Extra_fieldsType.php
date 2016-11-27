<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Extra_fieldsType extends AbstractType
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
            ->add('type', 'choice', array(
                'label' => 'Тип',
                'attr' => array('class' => 'form-control'),
                'choices'  => array(
                    'text' => 'Текст', 
                    'number' => 'Число'
                    )
                )
            )
            ->add('show_it', 'checkbox', array(
                'label' => 'Отображать в фильтре',
                'required' => false,
            ))
            ->add('showcase', 'checkbox', array(
                'label' => 'Отображать в витрине',
                'required' => false,
            ))
            // ->add('created_at')
            // ->add('updated_at')
            // ->add('categories')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\Extra_fields'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_extra_fields';
    }
}
