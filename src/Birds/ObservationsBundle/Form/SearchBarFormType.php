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

        $em = $options['entity_manager'];
        $br = $em->getRepository("BirdsObservationsBundle:Birds");
        $liste = $br->getAllByArray();
        $listeFinale= array();
        foreach($liste as $entree)
        {
            $listeFinale []= $entree['lbNom'];

        }
        $builder->add('searchBar', TextType::class, array('label'=>"Barre de recherche"))
            ->add('parametreAvances', CheckboxType::class, array('label'=>"Paramètres avancés", 'required'=>false ))
            ->add('espece',ChoiceType::class,array('label'=>"Les oiseaux", 'choices'=> $listeFinale))
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
                    'max' => 400000
                ),
                'label'=>"Distance du marqueur"
            ));
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('entity_manager');
    }
    



}
