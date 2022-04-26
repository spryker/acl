<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Acl\Communication\Form;

use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method \Spryker\Zed\Acl\Business\AclFacadeInterface getFacade()
 * @method \Spryker\Zed\Acl\Communication\AclCommunicationFactory getFactory()
 * @method \Spryker\Zed\Acl\Persistence\AclQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\Acl\AclConfig getConfig()
 * @method \Spryker\Zed\Acl\Persistence\AclRepositoryInterface getRepository()
 */
class RuleForm extends AbstractType
{
    /**
     * @var string
     */
    public const OPTION_TYPE = 'option_type';

    /**
     * @var string
     */
    public const FIELD_BUNDLE = 'bundle';

    /**
     * @var string
     */
    public const FIELD_CONTROLLER = 'controller';

    /**
     * @var string
     */
    public const FIELD_ACTION = 'action';

    /**
     * @var string
     */
    public const FIELD_TYPE = 'type';

    /**
     * @var string
     */
    public const FIELD_FK_ACL_ROLE = 'fk_acl_role';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(static::OPTION_TYPE);
    }

    /**
     * @deprecated Use {@link configureOptions()} instead.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addBundleField($builder)
            ->addControllerField($builder)
            ->addActionField($builder)
            ->addPermissionField($builder, $options[static::OPTION_TYPE])
            ->addRoleFkField($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addBundleField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_BUNDLE, TextType::class, [
            'label' => 'Bundle',
            'constraints' => [
                new NotBlank(),
                new Length(['max' => 45]),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addControllerField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_CONTROLLER, TextType::class, [
            'label' => 'Controller',
            'constraints' => [
                new NotBlank(),
                new Length(['max' => 45]),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addActionField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ACTION, TextType::class, [
            'label' => 'Action',
            'constraints' => [
                new NotBlank(),
                new Length(['max' => 45]),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $choices
     *
     * @return $this
     */
    protected function addPermissionField(FormBuilderInterface $builder, array $choices)
    {
        $builder->add(static::FIELD_TYPE, ChoiceType::class, [
            'label' => 'Permission',
            'choices' => $choices,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addRoleFkField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_FK_ACL_ROLE, HiddenType::class, []);

        return $this;
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'ruleset';
    }

    /**
     * @deprecated Use {@link getBlockPrefix()} instead.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
