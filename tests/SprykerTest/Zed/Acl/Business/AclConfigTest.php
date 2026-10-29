<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Acl\Business;

use ArrayObject;
use Codeception\Test\Unit;
use ReflectionClass;
use ReflectionProperty;
use Spryker\Shared\Acl\AclConstants;
use Spryker\Shared\Config\Config;
use Spryker\Zed\Acl\AclConfig;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Acl
 * @group Business
 * @group AclConfigTest
 * Add your own group annotations below this line
 */
class AclConfigTest extends Unit
{
    protected ?ArrayObject $configCache = null;

    public function setUp(): void
    {
        parent::setUp();

        $reflectionProperty = $this->getConfigReflectionProperty();
        $this->configCache = $reflectionProperty->getValue();
    }

    public function tearDown(): void
    {
        $reflectionProperty = $this->getConfigReflectionProperty();
        $reflectionProperty->setValue($this->configCache);

        parent::tearDown();
    }

    public function testGetRulesReturnsRulesSetAtRuntimeWhenNoEnvironmentConfigIsDefined(): void
    {
        $this->unsetAclDefaultRulesConfig();

        $aclConfig = new AclConfig();
        $aclConfig->setRules('sales', 'order', 'view', AclConstants::ALLOW);

        $this->assertSame(
            [
                [
                    'bundle' => 'sales',
                    'controller' => 'order',
                    'action' => 'view',
                    'type' => AclConstants::ALLOW,
                ],
            ],
            $aclConfig->getRules(),
        );
    }

    public function testGetRulesMergesEnvironmentConfiguredRulesWithRulesSetAtRuntime(): void
    {
        $this->setAclDefaultRulesConfig([
            [
                'bundle' => 'cms',
                'controller' => 'page',
                'action' => 'view',
                'type' => AclConstants::ALLOW,
            ],
        ]);

        $aclConfig = new AclConfig();
        $aclConfig->setRules('sales', 'order', 'view', AclConstants::ALLOW);

        $this->assertSame(
            [
                [
                    'bundle' => 'cms',
                    'controller' => 'page',
                    'action' => 'view',
                    'type' => AclConstants::ALLOW,
                ],
                [
                    'bundle' => 'sales',
                    'controller' => 'order',
                    'action' => 'view',
                    'type' => AclConstants::ALLOW,
                ],
            ],
            $aclConfig->getRules(),
        );
    }

    protected function unsetAclDefaultRulesConfig(): void
    {
        $configProperty = $this->getConfigReflectionProperty();
        $config = $configProperty->getValue();
        unset($config[AclConstants::ACL_DEFAULT_RULES]);

        $configProperty->setValue($config);
    }

    /**
     * @param array<int, array<string, string>> $rules
     */
    protected function setAclDefaultRulesConfig(array $rules): void
    {
        $configProperty = $this->getConfigReflectionProperty();
        $config = $configProperty->getValue();
        $config[AclConstants::ACL_DEFAULT_RULES] = $rules;

        $configProperty->setValue($config);
    }

    protected function getConfigReflectionProperty(): ReflectionProperty
    {
        $reflection = new ReflectionClass(Config::class);

        return $reflection->getProperty('config');
    }
}
