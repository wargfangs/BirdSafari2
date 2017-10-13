<?php
namespace Birds\ObservationsBundle\Form;

use AppBundle\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObservationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('birdname',BirdFormType::class)
            ->add('notSure',CheckboxType::class, array('required'=>false))
            ->add('image',ImageType::class)
            ->add('date',DateTimeType::class)
            ->add('latitude')
            ->add('longitude')
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ;



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