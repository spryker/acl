<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Acl;

use Codeception\Actor;
use Generated\Shared\Transfer\RuleTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Orm\Zed\Acl\Persistence\SpyAclUserHasGroup;
use Orm\Zed\Acl\Persistence\SpyAclUserHasGroupQuery;
use ReflectionClass;
use Spryker\Zed\Acl\Business\Model\Rule;

/**
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class AclBusinessTester extends Actor
{
    use _generated\AclBusinessTesterActions;

    public function haveAclUserHasGroup(int $idUser, int $idAclGroup): void
    {
        (new SpyAclUserHasGroup())
            ->setFkUser($idUser)
            ->setFkAclGroup($idAclGroup)
            ->save();
    }

    public function ensureAclUserHasGroupTableIsEmpty(): void
    {
        $this->getAclUserHasGroupQuery()->deleteAll();
    }

    public function createUserWithRule(string $bundle, string $controller, string $action, string $type): UserTransfer
    {
        $userTransfer = $this->haveUser();
        $groupTransfer = $this->haveGroup();
        $roleTransfer = $this->haveRole();

        $this->haveRule([
            RuleTransfer::BUNDLE => $bundle,
            RuleTransfer::CONTROLLER => $controller,
            RuleTransfer::ACTION => $action,
            RuleTransfer::TYPE => $type,
            RuleTransfer::FK_ACL_ROLE => $roleTransfer->getIdAclRoleOrFail(),
        ]);
        $this->addRoleToGroup($roleTransfer->getIdAclRoleOrFail(), $groupTransfer->getIdAclGroupOrFail());
        $this->addUserToGroup($userTransfer->getIdUserOrFail(), $groupTransfer->getIdAclGroupOrFail());

        return $userTransfer;
    }

    public function clearRuleStaticCache(): void
    {
        $ruleReflectionClass = new ReflectionClass(Rule::class);

        $ruleReflectionClass->getProperty('cache')->setValue(null, []);
        $ruleReflectionClass->getProperty('groupsTransferCache')->setValue(null, []);
        $ruleReflectionClass->getProperty('rulesTransferCache')->setValue(null, []);
        $ruleReflectionClass->getProperty('ignoredRulesCache')->setValue(null, null);
        $ruleReflectionClass->getProperty('ignoredRuleValidator')->setValue(null, null);
        $ruleReflectionClass->getProperty('ruleValidators')->setValue(null, []);
    }

    protected function getAclUserHasGroupQuery(): SpyAclUserHasGroupQuery
    {
        return SpyAclUserHasGroupQuery::create();
    }
}
