<?php
namespace Birds\ObservationsBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class BirdFormMultipleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('bird',ChoiceType::class, array(
            'label'=>'Espèce d\'oiseau',
            'class'=>'BirdsObservationsBundle:Birds',
            'choice_label' => 'lbNom',
            'query_builder'=> function(EntityRepository $br){
                return ;
            },
            'multiple' => true,
            ));
    }

}