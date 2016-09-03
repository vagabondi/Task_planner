<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskType extends AbstractType
{
    private $user;
    public function __construct(User $user)
    {
        $this->user=$user;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('deadline', 'date')
            ->add('priority', 'choice', array(
                'choices' => array(
                    'wazne i pilne' => 'wazne i pilne',
                    'wazne i niepilne' => 'wazne i niepilne',
                    'pilne i niewazne' => 'pilne i niewazne',
                    'niewazne i niepilne' => 'niewazne i niepilne'
                ),
                'choices_as_values' => true
            ))
            ->add('status', 'choice', array(
                'choices' => array(
                    'niewykonane' => 'niewykonane',
                    'w trakcie' => 'w trakcie',
                    'zakonczone' => 'zakonczone'
                ),
                'choices_as_values' => true
        ))
            ->add('category', 'entity', array(
                'class' => 'AppBundle\Entity\Category',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('cat')->where('cat.user = :user')->setParameter('user', $this->user);
                },
                'choice_label' => 'name'
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Task'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_task';
    }
}
