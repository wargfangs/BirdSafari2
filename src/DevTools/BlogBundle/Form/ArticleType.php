<?php

namespace DevTools\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use AppBundle\Form\ImageType;
//use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//                ->add('modificationDate')
                ->add('title', TextType::class, array(
                    'label'=>'Titre'
                ))
//                ->add('creationDate', DateType::class)
                ->add('content', TextareaType::class, array(
                    'label'=>'Contenu'
                ))
                ->add('image', ImageType::class);
//                ->add('user');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DevTools\BlogBundle\Entity\Article'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'devtools_blogbundle_article';
    }


}
