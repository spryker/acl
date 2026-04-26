<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Acl\Helper;

use Codeception\Module;
use Generated\Shared\DataBuilder\GroupBuilder;
use Generated\Shared\DataBuilder\RoleBuilder;
use Generated\Shared\DataBuilder\RuleBuilder;
use Generated\Shared\Transfer\AclRoleCriteriaTransfer;
use Generated\Shared\Transfer\GroupCriteriaTransfer;
use Generated\Shared\Transfer\GroupTransfer;
use Generated\Shared\Transfer\RoleTransfer;
use Generated\Shared\Transfer\RuleTransfer;
use Orm\Zed\Acl\Persistence\SpyAclGroup;
use Orm\Zed\Acl\Persistence\SpyAclGroupQuery;
use Orm\Zed\Acl\Persistence\SpyAclGroupsHasRolesQuery;
use Orm\Zed\Acl\Persistence\SpyAclRoleQuery;
use Orm\Zed\Acl\Persistence\SpyAclRuleQuery;
use SprykerTest\Shared\Testify\Helper\LocatorHelperTrait;

class AclHelper extends Module
{
    use LocatorHelperTrait;

    public function haveGroup(array $seedData = []): GroupTransfer
    {
        if (!isset($seedData['name'])) {
            $seedData['name'] = uniqid('acl-group-', true);
        }

        /** @var \Generated\Shared\Transfer\GroupTransfer $groupTransfer */
        $groupTransfer = (new GroupBuilder($seedData))->build();
        $aclGroupEntity = $this->createAclGroupEntity();
        $aclGroupEntity->fromArray($groupTransfer->toArray());

        $aclGroupEntity->save();

        $groupTransfer->fromArray($aclGroupEntity->toArray(), true);

        return $groupTransfer;
    }

    public function haveRole(array $seedData = []): RoleTransfer
    {
        if (!isset($seedData['name'])) {
            $seedData['name'] = uniqid('acl-role-', true);
        }

        /** @var \Generated\Shared\Transfer\RoleTransfer $roleTransfer */
        $roleTransfer = (new RoleBuilder($seedData))->build();

        $roleTransfer = $this->getLocator()
            ->acl()
            ->facade()
            ->updateRole($roleTransfer); // update method of facade works for create new records as well

        return $roleTransfer;
    }

    public function addUserToGroup(int $idUser, int $idGroup): int
    {
        return $this->getLocator()
            ->acl()
            ->facade()
            ->addUserToGroup($idUser, $idGroup);
    }

    public function haveRule(array $seedData = []): RuleTransfer
    {
        /** @var \Generated\Shared\Transfer\RuleTransfer $ruleTransfer */
        $ruleTransfer = (new RuleBuilder($seedData))->build();

        return $this->getLocator()
            ->acl()
            ->facade()
            ->addRule($ruleTransfer);
    }

    public function addRoleToGroup(int $idRole, int $idGroup): int
    {
        return $this->getLocator()
            ->acl()
            ->facade()
            ->addRoleToGroup($idRole, $idGroup);
    }

    public function deleteRoles(AclRoleCriteriaTransfer $aclRoleCriteriaTransfer): void
    {
        $aclRoleQuery = $this->getAclRoleQuery();
        if ($aclRoleCriteriaTransfer->getNames()) {
            $aclRoleQuery->filterByName_In($aclRoleCriteriaTransfer->getNames());
        }
        if ($aclRoleCriteriaTransfer->getName()) {
            $aclRoleQuery->filterByName($aclRoleCriteriaTransfer->getName());
        }

        $aclRoleQuery = $this->filterAclRoleByReference($aclRoleCriteriaTransfer, $aclRoleQuery);

        $aclRoleQuery->delete();
    }

    public function deleteGroups(GroupCriteriaTransfer $groupCriteriaTransfer): void
    {
        $aclGroupQuery = $this->getAclGroupQuery();
        if ($groupCriteriaTransfer->getNames()) {
            $aclGroupQuery->filterByName_In($groupCriteriaTransfer->getNames());
        }
        if ($groupCriteriaTransfer->getName()) {
            $aclGroupQuery->filterByName($groupCriteriaTransfer->getName());
        }

        if ($groupCriteriaTransfer->getReference()) {
            $aclGroupQuery->filterByReference($groupCriteriaTransfer->getReference());
        }

        $aclGroupQuery->delete();
    }

    public function ensureAclRoleTableIsEmpty(): void
    {
        SpyAclRoleQuery::create()->deleteAll();
    }

    public function ensureAclGroupTableIsEmpty(): void
    {
        SpyAclGroupQuery::create()->deleteAll();
    }

    /**
     * @param array<int> $idAclRules
     */
    public function deleteRules(array $idAclRules): void
    {
        SpyAclRuleQuery::create()
            ->filterByIdAclRule_In($idAclRules)
            ->delete();
    }

    public function deleteRoleGroupLink(int $idAclRole, int $idAclGroup): void
    {
        SpyAclGroupsHasRolesQuery::create()
            ->filterByFkAclRole($idAclRole)
            ->filterByFkAclGroup($idAclGroup)
            ->delete();
    }

    private function createAclGroupEntity(): SpyAclGroup
    {
        return new SpyAclGroup();
    }

    protected function getAclRoleQuery(): SpyAclRoleQuery
    {
        return SpyAclRoleQuery::create();
    }

    protected function getAclGroupQuery(): SpyAclGroupQuery
    {
        return SpyAclGroupQuery::create();
    }

    /**
     * @deprecated Will be removed in the next major without replacement.
     *
     * @param \Generated\Shared\Transfer\AclRoleCriteriaTransfer $aclRoleCriteriaTransfer
     * @param \Orm\Zed\Acl\Persistence\SpyAclRoleQuery $aclRoleQuery
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclRoleQuery
     */
    protected function filterAclRoleByReference(
        AclRoleCriteriaTransfer $aclRoleCriteriaTransfer,
        SpyAclRoleQuery $aclRoleQuery
    ): SpyAclRoleQuery {
        if ($aclRoleCriteriaTransfer->getReference()) {
            $aclRoleQuery->filterByReference($aclRoleCriteriaTransfer->getReference());
        }

        return $aclRoleQuery;
    }
}
