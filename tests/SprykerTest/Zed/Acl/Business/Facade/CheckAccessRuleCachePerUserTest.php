<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Acl\Business\Facade;

use Codeception\Test\Unit;
use SprykerTest\Zed\Acl\AclBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Acl
 * @group Business
 * @group Facade
 * @group CheckAccessRuleCachePerUserTest
 * Add your own group annotations below this line
 * @group SecurityAuthorization
 */
class CheckAccessRuleCachePerUserTest extends Unit
{
    protected AclBusinessTester $tester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tester->clearRuleStaticCache();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->tester->clearRuleStaticCache();
    }

    public function testGivenTwoUsersWithDifferentRulesWhenCheckAccessIsCalledInOneProcessThenEachGetsOwnRules(): void
    {
        // Arrange
        $salesUserTransfer = $this->tester->createUserWithRule('sales', 'detail', 'index', 'allow');
        $customerUserTransfer = $this->tester->createUserWithRule('customer', 'index', 'index', 'allow');
        $aclFacade = $this->tester->getFacade();

        // Act
        $salesUserCanOpenSales = $aclFacade->checkAccess($salesUserTransfer, 'sales', 'detail', 'index');
        $customerUserCanOpenSales = $aclFacade->checkAccess($customerUserTransfer, 'sales', 'detail', 'index');

        // Assert
        $this->assertTrue($salesUserCanOpenSales);
        $this->assertFalse($customerUserCanOpenSales);
    }
}
