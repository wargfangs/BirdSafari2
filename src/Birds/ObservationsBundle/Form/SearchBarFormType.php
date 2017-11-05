<?php

namespace Birds\ObservationsBundle\Form;


use Birds\ObservationsBundle\Repository\BirdsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchBarFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add('searchBar', SearchType::class, array('label'=>"Barre de recherche"))
            ->add('parametreAvances', CheckboxType::class, array('label'=>"Paramètres avancés", 'required'=>false ))
            ->add('DateDebut',DateType::class, array('label'=>"Date de début", 'data'=>new \DateTime("2017-09-01")))
            ->add('DateFin',DateType::class, array('label'=>"Date de fin", 'data'=>new \DateTime("now")))
            ->add('HeureDebut', IntegerType::class, array('label'=>"Heure de début", 'data'=>0))
            ->add('HeureFin', IntegerType::class, array('label'=>"Heure de fin", 'data'=>23))
            ->add('ActiverCarte', CheckboxType::class, array('label'=>"Activer la recherche géographique", 'required'=>false ))
            ->add('latitude',NumberType::class, array('scale'=>10,'label'=>"Latitude", 'attr'=>array('hidden'=>true) ))
            ->add('longitude',NumberType::class,array('scale'=>10,'label'=>"Longitude" , 'attr'=>array('hidden'=>true) ))
            ->add('distanceDuCentre',RangeType::class, array(
                'attr'=>array(
                    'min' => 100,
                    'max' => 35000,
                    'class'=> "slider",
                    'value'=>10000,
                    'id' => 'searchSlider'
                ),
                'label'=>"Distance du marqueur"
            ));
    }




}
