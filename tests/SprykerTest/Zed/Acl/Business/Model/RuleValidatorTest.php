<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Acl\Business\Model;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\RulesTransfer;
use Generated\Shared\Transfer\RuleTransfer;
use Spryker\Shared\Acl\AclConstants;
use Spryker\Zed\Acl\Business\Model\RuleValidator;
use Spryker\Zed\Acl\Business\Model\RuleValidatorInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Acl
 * @group Business
 * @group Model
 * @group RuleValidatorTest
 *
 * Add your own group annotations below this line
 */
class RuleValidatorTest extends Unit
{
    /**
     * @dataProvider isAccessibleWithAllowRulesDataProvider
     *
     * @param array<\Generated\Shared\Transfer\RuleTransfer> $allowRules
     * @param array<\Generated\Shared\Transfer\RuleTransfer> $denyRules
     * @param string $bundle
     * @param string $controller
     * @param string $action
     * @param bool $expectedResult
     *
     * @return void
     */
    public function testIsAccessibleWithAllowRules(
        array $allowRules,
        array $denyRules,
        string $bundle,
        string $controller,
        string $action,
        bool $expectedResult
    ): void {
        // Arrange
        $ruleValidator = $this->getRuleValidator();
        $rulesTransfer = new RulesTransfer();

        foreach ($allowRules as $rule) {
            $rulesTransfer->addRule($rule);
        }

        foreach ($denyRules as $rule) {
            $rulesTransfer->addRule($rule);
        }

        $ruleValidator->setRules($rulesTransfer);

        // Act
        $result = $ruleValidator->isAccessible($bundle, $controller, $action);

        // Assert
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider isAccessibleWithDenyRulesDataProvider
     *
     * @param array<\Generated\Shared\Transfer\RuleTransfer> $allowRules
     * @param array<\Generated\Shared\Transfer\RuleTransfer> $denyRules
     * @param string $bundle
     * @param string $controller
     * @param string $action
     * @param bool $expectedResult
     *
     * @return void
     */
    public function testIsAccessibleWithDenyRules(
        array $allowRules,
        array $denyRules,
        string $bundle,
        string $controller,
        string $action,
        bool $expectedResult
    ): void {
        // Arrange
        $ruleValidator = $this->getRuleValidator();
        $rulesTransfer = new RulesTransfer();

        foreach ($allowRules as $rule) {
            $rulesTransfer->addRule($rule);
        }

        foreach ($denyRules as $rule) {
            $rulesTransfer->addRule($rule);
        }

        $ruleValidator->setRules($rulesTransfer);

        // Act
        $result = $ruleValidator->isAccessible($bundle, $controller, $action);

        // Assert
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider isAccessibleWithWildcardsDataProvider
     *
     * @param array<\Generated\Shared\Transfer\RuleTransfer> $allowRules
     * @param array<\Generated\Shared\Transfer\RuleTransfer> $denyRules
     * @param string $bundle
     * @param string $controller
     * @param string $action
     * @param bool $expectedResult
     *
     * @return void
     */
    public function testIsAccessibleWithWildcards(
        array $allowRules,
        array $denyRules,
        string $bundle,
        string $controller,
        string $action,
        bool $expectedResult
    ): void {
        // Arrange
        $ruleValidator = $this->getRuleValidator();
        $rulesTransfer = new RulesTransfer();

        foreach ($allowRules as $rule) {
            $rulesTransfer->addRule($rule);
        }

        foreach ($denyRules as $rule) {
            $rulesTransfer->addRule($rule);
        }

        $ruleValidator->setRules($rulesTransfer);

        // Act
        $result = $ruleValidator->isAccessible($bundle, $controller, $action);

        // Assert
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider isAccessibleWithMixedRulesDataProvider
     *
     * @param array<\Generated\Shared\Transfer\RuleTransfer> $allowRules
     * @param array<\Generated\Shared\Transfer\RuleTransfer> $denyRules
     * @param string $bundle
     * @param string $controller
     * @param string $action
     * @param bool $expectedResult
     *
     * @return void
     */
    public function testIsAccessibleWithMixedRules(
        array $allowRules,
        array $denyRules,
        string $bundle,
        string $controller,
        string $action,
        bool $expectedResult
    ): void {
        // Arrange
        $ruleValidator = $this->getRuleValidator();
        $rulesTransfer = new RulesTransfer();

        foreach ($allowRules as $rule) {
            $rulesTransfer->addRule($rule);
        }

        foreach ($denyRules as $rule) {
            $rulesTransfer->addRule($rule);
        }

        $ruleValidator->setRules($rulesTransfer);

        // Act
        $result = $ruleValidator->isAccessible($bundle, $controller, $action);

        // Assert
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function isAccessibleWithAllowRulesDataProvider(): array
    {
        return [
            'exact match with allow rule should return true' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', 'index', 'list'),
                ],
                'denyRules' => [],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => true,
            ],
            'no matching allow rule should return false' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', 'index', 'list'),
                ],
                'denyRules' => [],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'edit',
                'expectedResult' => false,
            ],
            'multiple allow rules with one matching should return true' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', 'index', 'list'),
                    $this->createRuleTransfer('allow', 'category', 'index', 'list'),
                    $this->createRuleTransfer('allow', 'order', 'view', 'details'),
                ],
                'denyRules' => [],
                'bundle' => 'category',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => true,
            ],
            'no rules should return false' => [
                'allowRules' => [],
                'denyRules' => [],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => false,
            ],
            'allow rule with different bundle should return false' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', 'index', 'list'),
                ],
                'denyRules' => [],
                'bundle' => 'category',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => false,
            ],
            'allow rule with different controller should return false' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', 'index', 'list'),
                ],
                'denyRules' => [],
                'bundle' => 'product',
                'controller' => 'edit',
                'action' => 'list',
                'expectedResult' => false,
            ],
            'allow rule with different action should return false' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', 'index', 'list'),
                ],
                'denyRules' => [],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'delete',
                'expectedResult' => false,
            ],
        ];
    }

    /**
     * @return array
     */
    public function isAccessibleWithDenyRulesDataProvider(): array
    {
        return [
            'deny rule takes precedence over allow rule' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', 'index', 'list'),
                ],
                'denyRules' => [
                    $this->createRuleTransfer('deny', 'product', 'index', 'list'),
                ],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => false,
            ],
            'deny rule without matching allow rule should return false' => [
                'allowRules' => [],
                'denyRules' => [
                    $this->createRuleTransfer('deny', 'product', 'index', 'list'),
                ],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => false,
            ],
            'deny rule with allow for different resource should return false for denied' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'category', 'index', 'list'),
                ],
                'denyRules' => [
                    $this->createRuleTransfer('deny', 'product', 'index', 'list'),
                ],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => false,
            ],
            'allow rule for resource not in deny list should return true' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'category', 'index', 'list'),
                ],
                'denyRules' => [
                    $this->createRuleTransfer('deny', 'product', 'index', 'list'),
                ],
                'bundle' => 'category',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => true,
            ],
            'multiple deny rules with one matching should return false' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', 'index', 'list'),
                ],
                'denyRules' => [
                    $this->createRuleTransfer('deny', 'category', 'index', 'list'),
                    $this->createRuleTransfer('deny', 'product', 'index', 'list'),
                    $this->createRuleTransfer('deny', 'order', 'view', 'details'),
                ],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => false,
            ],
        ];
    }

    /**
     * @return array
     */
    public function isAccessibleWithWildcardsDataProvider(): array
    {
        return [
            'wildcard bundle with allow rule should match any bundle' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', AclConstants::VALIDATOR_WILDCARD, 'index', 'list'),
                ],
                'denyRules' => [],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => true,
            ],
            'wildcard bundle with allow rule should match any bundle but have wrong action' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', AclConstants::VALIDATOR_WILDCARD, 'index', 'list'),
                ],
                'denyRules' => [],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'edit',
                'expectedResult' => false,
            ],
            'wildcard controller with allow rule should match any controller' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', AclConstants::VALIDATOR_WILDCARD, 'list'),
                ],
                'denyRules' => [],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => true,
            ],
            'wildcard controller with allow rule should match any controller but have wrong action' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', AclConstants::VALIDATOR_WILDCARD, 'list'),
                ],
                'denyRules' => [],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'edit',
                'expectedResult' => false,
            ],
            'wildcard action with allow rule should match any action' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', 'index', AclConstants::VALIDATOR_WILDCARD),
                ],
                'denyRules' => [],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => true,
            ],
            'wildcard action with allow rule should match any action but have wrong controller' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', 'index', AclConstants::VALIDATOR_WILDCARD),
                ],
                'denyRules' => [],
                'bundle' => 'product',
                'controller' => 'save',
                'action' => 'list',
                'expectedResult' => false,
            ],
            'all wildcards with allow rule should match everything' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', AclConstants::VALIDATOR_WILDCARD, AclConstants::VALIDATOR_WILDCARD, AclConstants::VALIDATOR_WILDCARD),
                ],
                'denyRules' => [],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => true,
            ],
            'wildcard bundle with deny rule should deny any bundle' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', 'index', 'list'),
                ],
                'denyRules' => [
                    $this->createRuleTransfer('deny', AclConstants::VALIDATOR_WILDCARD, 'index', 'list'),
                ],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => false,
            ],
            'wildcard controller with deny rule should deny any controller' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', 'index', 'list'),
                ],
                'denyRules' => [
                    $this->createRuleTransfer('deny', 'product', AclConstants::VALIDATOR_WILDCARD, 'list'),
                ],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => false,
            ],
            'wildcard action with deny rule should deny any action' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', 'index', 'list'),
                ],
                'denyRules' => [
                    $this->createRuleTransfer('deny', 'product', 'index', AclConstants::VALIDATOR_WILDCARD),
                ],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => false,
            ],
            'all wildcards with deny rule should deny everything' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', 'index', 'list'),
                ],
                'denyRules' => [
                    $this->createRuleTransfer('deny', AclConstants::VALIDATOR_WILDCARD, AclConstants::VALIDATOR_WILDCARD, AclConstants::VALIDATOR_WILDCARD),
                ],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => false,
            ],
            'wildcard bundle and controller with specific action' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', AclConstants::VALIDATOR_WILDCARD, AclConstants::VALIDATOR_WILDCARD, 'list'),
                ],
                'denyRules' => [],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => true,
            ],
            'wildcard bundle and action with specific controller' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', AclConstants::VALIDATOR_WILDCARD, 'index', AclConstants::VALIDATOR_WILDCARD),
                ],
                'denyRules' => [],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => true,
            ],
            'wildcard controller and action with specific bundle' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', AclConstants::VALIDATOR_WILDCARD, AclConstants::VALIDATOR_WILDCARD),
                ],
                'denyRules' => [],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => true,
            ],
        ];
    }

    /**
     * @return array
     */
    public function isAccessibleWithMixedRulesDataProvider(): array
    {
        return [
            'specific deny overrides wildcard allow' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', AclConstants::VALIDATOR_WILDCARD, AclConstants::VALIDATOR_WILDCARD, AclConstants::VALIDATOR_WILDCARD),
                ],
                'denyRules' => [
                    $this->createRuleTransfer('deny', 'product', 'index', 'delete'),
                ],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'delete',
                'expectedResult' => false,
            ],
            'wildcard deny overrides specific allow' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', 'index', 'list'),
                ],
                'denyRules' => [
                    $this->createRuleTransfer('deny', AclConstants::VALIDATOR_WILDCARD, AclConstants::VALIDATOR_WILDCARD, AclConstants::VALIDATOR_WILDCARD),
                ],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => false,
            ],
            'allow all with specific deny for one action' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', AclConstants::VALIDATOR_WILDCARD, AclConstants::VALIDATOR_WILDCARD),
                ],
                'denyRules' => [
                    $this->createRuleTransfer('deny', 'product', 'index', 'delete'),
                ],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => true,
            ],
            'complex scenario with multiple wildcards and specific rules' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', AclConstants::VALIDATOR_WILDCARD, 'list'),
                    $this->createRuleTransfer('allow', 'category', 'index', AclConstants::VALIDATOR_WILDCARD),
                ],
                'denyRules' => [
                    $this->createRuleTransfer('deny', 'product', 'admin', AclConstants::VALIDATOR_WILDCARD),
                ],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => true,
            ],
            'complex scenario where deny matches' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', AclConstants::VALIDATOR_WILDCARD, 'list'),
                    $this->createRuleTransfer('allow', 'category', 'index', AclConstants::VALIDATOR_WILDCARD),
                ],
                'denyRules' => [
                    $this->createRuleTransfer('deny', 'product', 'admin', AclConstants::VALIDATOR_WILDCARD),
                ],
                'bundle' => 'product',
                'controller' => 'admin',
                'action' => 'list',
                'expectedResult' => false,
            ],
            'multiple allow rules with partial wildcard overlap' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', AclConstants::VALIDATOR_WILDCARD, 'list'),
                    $this->createRuleTransfer('allow', AclConstants::VALIDATOR_WILDCARD, 'index', 'view'),
                ],
                'denyRules' => [],
                'bundle' => 'order',
                'controller' => 'index',
                'action' => 'view',
                'expectedResult' => true,
            ],
            'deny with wildcard bundle blocks specific allow' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', 'index', 'list'),
                ],
                'denyRules' => [
                    $this->createRuleTransfer('deny', AclConstants::VALIDATOR_WILDCARD, 'index', 'list'),
                ],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => false,
            ],
            'allow access when only different deny rule exists' => [
                'allowRules' => [
                    $this->createRuleTransfer('allow', 'product', 'index', 'list'),
                ],
                'denyRules' => [
                    $this->createRuleTransfer('deny', 'product', 'index', 'delete'),
                ],
                'bundle' => 'product',
                'controller' => 'index',
                'action' => 'list',
                'expectedResult' => true,
            ],
        ];
    }

    /**
     * @param string $type
     * @param string $bundle
     * @param string $controller
     * @param string $action
     *
     * @return \Generated\Shared\Transfer\RuleTransfer
     */
    protected function createRuleTransfer(string $type, string $bundle, string $controller, string $action): RuleTransfer
    {
        return (new RuleTransfer())
            ->setType($type)
            ->setBundle($bundle)
            ->setController($controller)
            ->setAction($action);
    }

    public function getRuleValidator(): RuleValidatorInterface
    {
        return new RuleValidator();
    }
}
