<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RecordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('upc', TextType::class, array('attr' => array('class' => 'upc-input')))
            ->add('asin', TextType::class, array('attr' => array('class' => 'asin-input')))
            ->add('cddbid', TextType::class, array('attr' => array('class' => 'cddbid-input')))
            ->add('artist', TextType::class, array('attr' => array('class' => 'artist-input')))
            ->add('title', TextType::class, array('attr' => array('class' => 'title-input')))
            ->add('year', TextType::class, array('attr' => array('class' => 'year-input')))
            ->add('mediaType', TextType::class, array('attr' => array('class' => 'mediatype-input')))
            ->add('mediaCount', TextType::class, array('attr' => array('class' => 'mediacount-input')))
            ->add('coverImageUrl', TextType::class, array('attr' => array('class' => 'coverimageurl-input')))
            ->add('recordLabel', TextType::class, array('attr' => array('class' => 'recordlabel-input')))
            ->add('genre', TextType::class, array('attr' => array('class' => 'genre-input')))
            //->add('tracksLists')
            ->add('tracksListsInString', TextareaType::class, array('mapped' => false, 'label' => 'Tracks lists. Separators: \'|\' between tracks and \'||\' between media.', 'attr' => array('cols' => 50, 'rows' => 10 )));
            //->add('ts', 'datetime')

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Record'
        ));
    }
}
