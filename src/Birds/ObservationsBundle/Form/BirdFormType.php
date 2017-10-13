<?php
namespace Birds\ObservationsBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BirdFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('bird',EntityType::class, array(
            'label'=>'Sélectionnez un type d\'oiseau dans la liste',
            'class'=>'BirdsObservationsBundle:Birds',
            'choice_label' => 'lbNom',
            'query_builder'=> function(EntityRepository $br){
                return $br->createQueryBuilder('b')->orderBy('b.lbNom','ASC');
            },
            'multiple' => false
            ));



    }

    /**
     * {@inheritdoc}
     *//*
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Birds\ObservationsBundle\Entity\Birds'
        ));

    }*/

}