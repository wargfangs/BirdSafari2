<?php
namespace Birds\ObservationsBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BirdFormMultipleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('bird',EntityType::class, array(
            'label'=>'Espèce d\'oiseau',
            'class'=>'BirdsObservationsBundle:Birds',
            'choice_label' => 'lbNom',
            'query_builder'=> function(EntityRepository $br){
                return $br->createQueryBuilder('b')->orderBy('b.lbNom','ASC');
            },
            'multiple' => true,
            ));
    }

}