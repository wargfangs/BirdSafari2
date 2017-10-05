<?php
namespace Birds\ObservationsBundle\Form;

use AppBundle\Form\ImageType;
use Birds\ObservationsBundle\Entity\Observation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObservationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('birdname',BirdFormType::class)
            ->add('date',DateType::class)
            ->add('latitude')
            ->add('longitude')
            ->add('image',ImageType::class);



    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Birds\ObservationsBundle\Entity\Observation'
        ));
    }




}