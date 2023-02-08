<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Acl\Business\Model;

use Generated\Shared\Transfer\RoleTransfer;
use Generated\Shared\Transfer\RuleTransfer;
use Generated\Shared\Transfer\UserCollectionTransfer;
use Generated\Shared\Transfer\UserConditionsTransfer;
use Generated\Shared\Transfer\UserCriteriaTransfer;
use Spryker\Zed\Acl\Business\Acl\AclConfigReaderInterface;
use Spryker\Zed\Acl\Business\Exception\GroupNotFoundException;
use Spryker\Zed\Acl\Business\Writer\RoleWriterInterface;
use Spryker\Zed\Acl\Dependency\Facade\AclToUserInterface;
use Spryker\Zed\User\Business\Exception\UserNotFoundException;

class Installer implements InstallerInterface
{
    /**
     * @var \Spryker\Zed\Acl\Business\Model\GroupInterface
     */
    protected $group;

    /**
     * @var \Spryker\Zed\Acl\Business\Model\RoleInterface
     */
    protected $role;

    /**
     * @var \Spryker\Zed\Acl\Business\Model\RuleInterface
     */
    protected $rule;

    /**
     * @var \Spryker\Zed\Acl\Dependency\Facade\AclToUserInterface
     */
    protected $userFacade;

    /**
     * @var array<\Spryker\Zed\AclExtension\Dependency\Plugin\AclInstallerPluginInterface>
     */
    protected $aclInstallerPlugins;

    /**
     * @var \Spryker\Zed\Acl\Business\Acl\AclConfigReaderInterface
     */
    protected $aclConfigReader;

    /**
     * @var \Spryker\Zed\Acl\Business\Writer\RoleWriterInterface
     */
    protected $roleWriter;

    /**
     * @param \Spryker\Zed\Acl\Business\Model\GroupInterface $group
     * @param \Spryker\Zed\Acl\Business\Model\RoleInterface $role
     * @param \Spryker\Zed\Acl\Business\Model\RuleInterface $rule
     * @param \Spryker\Zed\Acl\Dependency\Facade\AclToUserInterface $userFacade
     * @param \Spryker\Zed\Acl\Business\Acl\AclConfigReaderInterface $aclConfigReader
     * @param \Spryker\Zed\Acl\Business\Writer\RoleWriterInterface $roleWriter
     * @param array<\Spryker\Zed\AclExtension\Dependency\Plugin\AclInstallerPluginInterface> $aclInstallerPlugins
     */
    public function __construct(
        GroupInterface $group,
        RoleInterface $role,
        RuleInterface $rule,
        AclToUserInterface $userFacade,
        AclConfigReaderInterface $aclConfigReader,
        RoleWriterInterface $roleWriter,
        array $aclInstallerPlugins
    ) {
        $this->group = $group;
        $this->role = $role;
        $this->rule = $rule;
        $this->userFacade = $userFacade;
        $this->aclConfigReader = $aclConfigReader;
        $this->roleWriter = $roleWriter;
        $this->aclInstallerPlugins = $aclInstallerPlugins;
    }

    /**
     * Main Installation Method
     *
     * @return void
     */
    public function install(): void
    {
        $this->installGroups();
        $this->installRoles();
        $this->installUserGroupRelations();
    }

    /**
     * @return void
     */
    protected function installGroups(): void
    {
        foreach ($this->getGroups() as $groupTransfer) {
            $groupTransfer->requireName();
            if ($this->group->hasGroupName($groupTransfer->getName())) {
                continue;
            }
            $this->group->save($groupTransfer);
        }
    }

    /**
     * @return void
     */
    protected function installRoles(): void
    {
        foreach ($this->getRoles() as $roleTransfer) {
            $roleTransfer->requireName()
                ->requireAclGroup()
                ->getAclGroup()
                    ->requireName();

            $existingRoleTransfer = $this->role->findRoleByName($roleTransfer->getName());
            if (!$existingRoleTransfer) {
                $existingRoleTransfer = $this->createRole($roleTransfer);
            }

            foreach ($roleTransfer->getAclRules() as $ruleTransfer) {
                $this->addRuleToRole($ruleTransfer, $existingRoleTransfer);
            }
        }
    }

    /**
     * @param \Generated\Shared\Transfer\RoleTransfer $roleTransfer
     *
     * @throws \Spryker\Zed\Acl\Business\Exception\GroupNotFoundException
     *
     * @return \Generated\Shared\Transfer\RoleTransfer
     */
    protected function createRole(RoleTransfer $roleTransfer): RoleTransfer
    {
        $groupTransfer = $this->group->getByName($roleTransfer->getAclGroup()->getName());
        if (!$groupTransfer->getIdAclGroup()) {
            throw new GroupNotFoundException(sprintf('The group with name %s was not found', $roleTransfer->getAclGroup()->getName()));
        }
        $roleTransfer = $this->roleWriter->createRole($roleTransfer);
        $this->group->addRoleToGroup($roleTransfer->getIdAclRole(), $groupTransfer->getIdAclGroup());

        return $roleTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\RuleTransfer $ruleTransfer
     * @param \Generated\Shared\Transfer\RoleTransfer $roleTransfer
     *
     * @return \Generated\Shared\Transfer\RuleTransfer
     */
    protected function addRuleToRole(RuleTransfer $ruleTransfer, RoleTransfer $roleTransfer): RuleTransfer
    {
        $ruleTransfer->requireAction()
            ->requireBundle()
            ->requireController()
            ->requireType();

        $existsRoleRule = $this->rule->existsRoleRule(
            $roleTransfer->getIdAclRole(),
            $ruleTransfer->getBundle(),
            $ruleTransfer->getController(),
            $ruleTransfer->getAction(),
            $ruleTransfer->getType(),
        );
        if ($existsRoleRule) {
            return $ruleTransfer;
        }
        $ruleTransfer->setFkAclRole($roleTransfer->getIdAclRole());

        return $this->rule->addRule($ruleTransfer);
    }

    /**
     * @throws \Spryker\Zed\Acl\Business\Exception\GroupNotFoundException
     * @throws \Spryker\Zed\User\Business\Exception\UserNotFoundException
     *
     * @return void
     */
    protected function installUserGroupRelations(): void
    {
        $userTransfers = $this->aclConfigReader->getUserGroupRelations();
        $userCollectionTransfer = $this->getUserCollectionTransfer($userTransfers);
        $indexedPersistedUserTransfers = $this->getUserTransfersIndexedByUsername($userCollectionTransfer);

        foreach ($userTransfers as $userTransfer) {
            foreach ($userTransfer->getAclGroups() as $groupTransfer) {
                $existingGroupTransfer = $this->group->getByName($groupTransfer->getName());
                if (!$existingGroupTransfer->getIdAclGroup()) {
                    throw new GroupNotFoundException(sprintf('The group with name %s was not found', $groupTransfer->getName()));
                }

                if (!isset($indexedPersistedUserTransfers[$userTransfer->getUsername()])) {
                    throw new UserNotFoundException(sprintf('The user with username %s was not found', $userTransfer->getUsername()));
                }
                $existingUserTransfer = $indexedPersistedUserTransfers[$userTransfer->getUsername()];

                if ($this->group->hasUser($existingGroupTransfer->getIdAclGroup(), $existingUserTransfer->getIdUser())) {
                    continue;
                }
                $this->group->addUser($existingGroupTransfer->getIdAclGroup(), $existingUserTransfer->getIdUser());
            }
        }
    }

    /**
     * @return array<\Generated\Shared\Transfer\RoleTransfer>
     */
    protected function getRoles(): array
    {
        $roleTransfers = $this->aclConfigReader->getRoles();

        foreach ($this->aclInstallerPlugins as $aclInstallerPlugin) {
            foreach ($aclInstallerPlugin->getRoles() as $roleTransfer) {
                $roleTransfers[] = $roleTransfer;
            }
        }

        return $roleTransfers;
    }

    /**
     * @return array<\Generated\Shared\Transfer\GroupTransfer>
     */
    protected function getGroups(): array
    {
        $groupTransfers = $this->aclConfigReader->getGroups();

        foreach ($this->aclInstallerPlugins as $aclInstallerPlugin) {
            foreach ($aclInstallerPlugin->getGroups() as $groupTransfer) {
                $groupTransfers[] = $groupTransfer;
            }
        }

        return $groupTransfers;
    }

    /**
     * @param array<\Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return \Generated\Shared\Transfer\UserCollectionTransfer
     */
    protected function getUserCollectionTransfer(array $userTransfers): UserCollectionTransfer
    {
        $userCriteriaTransfer = $this->createUserCriteriaTransfer($userTransfers);

        return $this->userFacade->getUserCollection($userCriteriaTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UserCollectionTransfer $userCollectionTransfer
     *
     * @return array<string, \Generated\Shared\Transfer\UserTransfer>
     */
    protected function getUserTransfersIndexedByUsername(UserCollectionTransfer $userCollectionTransfer): array
    {
        $indexedUserTransfers = [];
        foreach ($userCollectionTransfer->getUsers() as $userTransfer) {
            $indexedUserTransfers[$userTransfer->getUsernameOrFail()] = $userTransfer;
        }

        return $indexedUserTransfers;
    }

    /**
     * @param array<\Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return \Generated\Shared\Transfer\UserCriteriaTransfer
     */
    protected function createUserCriteriaTransfer(array $userTransfers): UserCriteriaTransfer
    {
        $userConditionsTransfer = (new UserConditionsTransfer())
            ->setUsernames($this->extractUsernamesFromUserTransfers($userTransfers));

        return (new UserCriteriaTransfer())->setUserConditions($userConditionsTransfer);
    }

    /**
     * @param array<\Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return array<string>
     */
    protected function extractUsernamesFromUserTransfers(array $userTransfers): array
    {
        $usernames = [];
        foreach ($userTransfers as $userTransfer) {
            if ($userTransfer->getUsername() !== null) {
                $usernames[] = $userTransfer->getUsername();
            }
        }

        return $usernames;
    }
}
