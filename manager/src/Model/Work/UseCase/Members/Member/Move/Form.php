<?php

declare(strict_types=1);

namespace App\Model\Work\UseCase\Members\Member\Move;

use App\ReadModel\Work\Members\GroupFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private $groups;

    /**
     * Form constructor.
     *
     * @param GroupFetcher $groups
     */
    public function __construct(GroupFetcher $groups)
    {
        $this->groups = $groups;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('group', Type\ChoiceType::class, ['choices' => array_flip($this->groups->assoc())]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Command::class,
        ));
    }
}