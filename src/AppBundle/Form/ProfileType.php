<?php
namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseReg;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder
           
            ->add('avatar', VichFileType::class, array(
                'data_class' => null,
				'allow_delete' => true, 
                'property_path' => 'avatar',
                'required' => false,
            ))
			 ->add('newsletterSubscriber', CheckboxType::class, array(
                
                'required' => false,
                'data' => false
            ))
			
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