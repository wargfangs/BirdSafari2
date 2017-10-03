<?php
namespace Birds\ObservationsBundle\Form;

use AppBundle\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;

class ObservationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('birdname')
            ->add('date',DateTimeType::class)
            ->add('latitude')
            ->add('longitude')
            ->add('user')
            ->add('image',ImageType::class);



    }





}