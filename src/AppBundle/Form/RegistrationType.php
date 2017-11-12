<?php
namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseReg;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder
			 ->add('acceptCgu',CheckboxType::class, array(
				 'required'=>false,
				 'attr'=> array(
					 'onclick' => 'enableSubmit')
			 ))
			 ->add('newsletterSubscriber', CheckboxType::class, array(
               'required' => false,
                'data' => false
            ))
			->add('confirmationStatus',CheckboxType::class, array('required'=>false))
			 ->add('firstName')
            ->add('lastName')
            ->add('birth', BirthdayType::class)
            ->add('institution');
   }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }
    public function getParent()
    {
        return BaseReg::class;
    }


}