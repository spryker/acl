<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Acl\Business\Model;

use ArrayObject;
use Generated\Shared\Transfer\GroupsTransfer;
use Generated\Shared\Transfer\RolesTransfer;
use Generated\Shared\Transfer\RoleTransfer;
use Generated\Shared\Transfer\RulesTransfer;
use Generated\Shared\Transfer\RuleTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Orm\Zed\Acl\Persistence\SpyAclRule;
use Spryker\Shared\Acl\AclConstants;
use Spryker\Zed\Acl\AclConfig;
use Spryker\Zed\Acl\Business\Exception\RuleNotFoundException;
use Spryker\Zed\Acl\Dependency\Facade\AclToUserInterface;
use Spryker\Zed\Acl\Persistence\AclQueryContainerInterface;
use Spryker\Zed\User\Business\Exception\UserNotFoundException;

class Rule implements RuleInterface
{
    /**
     * @var array<array<array<array<bool>>>>
     */
    protected static array $cache = [];

    /**
     * @var array<\Generated\Shared\Transfer\GroupsTransfer>
     */
    protected static array $groupsTransferCache = [];

    /**
     * @var array<\Generated\Shared\Transfer\RulesTransfer>
     */
    protected static array $rulesTransferCache = [];

    protected static ?RulesTransfer $ignoredRulesCache = null;

    protected static ?RuleValidatorInterface $ignoredRuleValidator = null;

    protected static ?RuleValidatorInterface $ruleValidator = null;

    /**
     * @param \Spryker\Zed\Acl\Business\Model\GroupInterface $group
     * @param \Spryker\Zed\Acl\Persistence\AclQueryContainerInterface $queryContainer
     * @param \Spryker\Zed\Acl\Dependency\Facade\AclToUserInterface $userFacade
     * @param \Spryker\Zed\Acl\Business\Model\RuleValidatorInterface $rulesValidator
     * @param \Spryker\Zed\Acl\AclConfig $config
     * @param array<\Spryker\Zed\AclExtension\Dependency\Plugin\AclAccessCheckerStrategyPluginInterface> $aclAccessCheckerStrategyPlugins
     */
    public function __construct(
        protected GroupInterface $group,
        protected AclQueryContainerInterface $queryContainer,
        protected AclToUserInterface $userFacade,
        protected RuleValidatorInterface $rulesValidator,
        protected RuleValidatorInterface $ignoreRulesValidator,
        protected AclConfig $config,
        protected array $aclAccessCheckerStrategyPlugins
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\RuleTransfer $ruleTransfer
     *
     * @return \Generated\Shared\Transfer\RuleTransfer
     */
    public function addRule(RuleTransfer $ruleTransfer)
    {
        return $this->save($ruleTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\RuleTransfer $ruleTransfer
     *
     * @throws \Spryker\Zed\Acl\Business\Exception\RuleNotFoundException
     *
     * @return \Generated\Shared\Transfer\RuleTransfer
     */
    public function save(RuleTransfer $ruleTransfer)
    {
        $aclRuleEntity = new SpyAclRule();

        if ($ruleTransfer->getIdAclRule() !== null && $this->hasRule($ruleTransfer->getIdAclRule()) === true) {
            throw new RuleNotFoundException();
        }

        $aclRuleEntity->fromArray($ruleTransfer->toArray());
        $aclRuleEntity->save();

        $ruleTransfer = new RuleTransfer();
        $ruleTransfer->fromArray($aclRuleEntity->toArray(), true);

        return $ruleTransfer;
    }

    /**
     * @param int $idRule
     *
     * @return bool
     */
    public function hasRule($idRule)
    {
        $aclRuleEntity = $this->queryContainer->queryRuleById($idRule)->count();

        return $aclRuleEntity > 0;
    }

    /**
     * @param int $idRole
     *
     * @return \Generated\Shared\Transfer\RulesTransfer
     */
    public function getRoleRules($idRole)
    {
        $roleTransfer = new RoleTransfer();
        $roleTransfer->setIdAclRole($idRole);

        $rolesTransfer = new RolesTransfer();
        $rolesTransfer->addRole($roleTransfer);

        $rules = $this->findByRoles($rolesTransfer);

        return $rules;
    }

    /**
     * @param int $idAclRole
     * @param string $bundle
     * @param string $controller
     * @param string $action
     * @param string $type
     *
     * @return bool
     */
    public function existsRoleRule($idAclRole, $bundle, $controller, $action, $type)
    {
        $query = $this->queryContainer
            ->queryRuleByPathAndRole($idAclRole, $bundle, $controller, $action, $type);

        return ($query->count() > 0);
    }

    /**
     * @deprecated Will be removed in the next major.
     *
     * @param \Generated\Shared\Transfer\RolesTransfer $roles
     * @param string $bundle
     * @param string $controller
     * @param string $action
     *
     * @return \Generated\Shared\Transfer\RulesTransfer
     */
    public function findByRoles(
        RolesTransfer $roles,
        $bundle = AclConstants::VALIDATOR_WILDCARD,
        $controller = AclConstants::VALIDATOR_WILDCARD,
        $action = AclConstants::VALIDATOR_WILDCARD
    ) {
        /** @var array<\Orm\Zed\Acl\Persistence\SpyAclRule> $ruleCollection */
        $ruleCollection = $this->queryContainer->queryRuleByPathAndRoles($roles, $bundle, $controller, $action)->find();

        $rulesTransfer = new RulesTransfer();

        foreach ($ruleCollection as $ruleEntity) {
            $ruleTransfer = new RuleTransfer();
            $ruleTransfer->fromArray($ruleEntity->toArray(), true);
            $rulesTransfer->addRule($ruleTransfer);
        }

        return $rulesTransfer;
    }

    /**
     * @param int $idGroup
     *
     * @return \Generated\Shared\Transfer\RulesTransfer
     */
    public function getRulesForGroupId($idGroup)
    {
        /** @var \Propel\Runtime\Collection\ObjectCollection $relationshipCollection */
        $relationshipCollection = $this->queryContainer->queryGroupHasRole($idGroup)->find();
        /** @var array<\Orm\Zed\Acl\Persistence\SpyAclRole> $roleCollection */
        $roleCollection = $this->queryContainer->queryGroupRules($relationshipCollection)->find();

        $rulesTransfer = new RulesTransfer();

        foreach ($roleCollection as $ruleEntity) {
            $ruleTransfer = new RuleTransfer();
            $ruleTransfer->fromArray($ruleEntity->toArray(), true);
            $rulesTransfer->addRule($ruleTransfer);
        }

        return $rulesTransfer;
    }

    protected function getRulesForGroupIds(array $groupIds): RulesTransfer
    {
        /** @var \Propel\Runtime\Collection\ObjectCollection $relationshipCollection */
        $relationshipCollection = $this->queryContainer->queryGroupHasRoles($groupIds)->find();
        $roleCollection = $this->queryContainer->queryGroupRules($relationshipCollection)->find();

        $rulesTransfer = new RulesTransfer();

        foreach ($roleCollection as $ruleEntity) {
            $ruleTransfer = new RuleTransfer();
            $ruleTransfer->fromArray($ruleEntity->toArray(), true);
            $rulesTransfer->addRule($ruleTransfer);
        }

        return $rulesTransfer;
    }

    /**
     * @param int $id
     *
     * @throws \Spryker\Zed\Acl\Business\Exception\RuleNotFoundException
     *
     * @return \Generated\Shared\Transfer\RuleTransfer
     */
    public function getRuleById($id)
    {
        $aclRuleEntity = $this->queryContainer->queryRuleById($id)->findOne();

        if ($aclRuleEntity === null) {
            throw new RuleNotFoundException();
        }

        $ruleTransfer = new RuleTransfer();
        $ruleTransfer->fromArray($aclRuleEntity->toArray(), true);

        return $ruleTransfer;
    }

    /**
     * @param int $id
     *
     * @throws \Spryker\Zed\Acl\Business\Exception\RuleNotFoundException
     *
     * @return bool
     */
    public function removeRuleById($id)
    {
        $aclRuleEntity = $this->queryContainer->queryRuleById($id)->delete();

        if ($aclRuleEntity <= 0) {
            throw new RuleNotFoundException();
        }

        return true;
    }

    /**
     * @param string $bundle
     * @param string $controller
     * @param string $action
     *
     * @return bool
     */
    public function isIgnorable($bundle, $controller, $action)
    {
        $this->ignoreRulesValidator->setRules($this->getIgnorableRules());

        return $this->ignoreRulesValidator->isAccessible($bundle, $controller, $action);
    }

    protected function cachedIsIgnorable(string $bundle, string $controller, string $action, UserTransfer $userTransfer): bool
    {
        if ($this->isCacheShouldBeIgnored($userTransfer)) {
            $this->isIgnorable($bundle, $controller, $action);
        }

        if (static::$ignoredRuleValidator === null) {
            static::$ignoredRuleValidator = $this->ignoreRulesValidator;
            static::$ignoredRuleValidator->setRules($this->getIgnorableRules());
        }

        return static::$ignoredRuleValidator->isAccessible($bundle, $controller, $action);
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @throws \Spryker\Zed\User\Business\Exception\UserNotFoundException
     *
     * @return void
     */
    public function registerSystemUserRules(UserTransfer $userTransfer)
    {
        $credentials = $this->config->getCredentials();

        $credential = array_filter($credentials, function ($username) use ($userTransfer) {
            return $username === $userTransfer->getUsername();
        }, ARRAY_FILTER_USE_KEY);

        if (count($credential) === 0) {
            throw new UserNotFoundException();
        }

        foreach ($credential[$userTransfer->getUsername()]['rules'] as $rule) {
            $this->config->setRules($rule['bundle'], $rule['controller'], $rule['action'], $rule['type']);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     * @param string $bundle
     * @param string $controller
     * @param string $action
     *
     * @return bool
     */
    public function isAllowed(UserTransfer $userTransfer, $bundle, $controller, $action): bool
    {
        if ($this->isCacheShouldBeIgnored($userTransfer)) {
            return $this->executeIsAllowed($userTransfer, $bundle, $controller, $action);
        }

        if (!isset(static::$cache[$userTransfer->getIdUser()][$bundle][$controller][$action])) {
            static::$cache[$userTransfer->getIdUser()][$bundle][$controller][$action] = $this->executeIsAllowed($userTransfer, $bundle, $controller, $action);
        }

        return static::$cache[$userTransfer->getIdUser()][$bundle][$controller][$action];
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     * @param string $bundle
     * @param string $controller
     * @param string $action
     *
     * @return bool
     */
    protected function executeIsAllowed(UserTransfer $userTransfer, $bundle, $controller, $action)
    {
        if ($this->userFacade->isSystemUser($userTransfer)) {
            $this->registerSystemUserRules($userTransfer);
        }

        if ($this->cachedIsIgnorable($bundle, $controller, $action, $userTransfer)) {
            return true;
        }

        if (!$userTransfer->getIdUser()) {
            return false;
        }

        $groupsTransfer = $this->getGroupsTransferByIdUser($userTransfer);
        if (!$groupsTransfer->getGroups()) {
            return false;
        }

        $isAllowed = $this->executeAclAccessCheckerStrategyPlugins($userTransfer, $bundle, $controller, $action);
        if ($isAllowed !== null) {
            return $isAllowed;
        }

        return $this->cachableIsAllowed($groupsTransfer, $userTransfer, $bundle, $controller, $action);
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\GroupsTransfer
     */
    protected function getGroupsTransferByIdUser(UserTransfer $userTransfer): GroupsTransfer
    {
        $idUser = $userTransfer->getIdUser();

        if ($this->isCacheShouldBeIgnored($userTransfer)) {
            return $this->group->getUserGroups($idUser);
        }

        if (isset(static::$groupsTransferCache[$idUser])) {
            return static::$groupsTransferCache[$idUser];
        }

        $groupsTransfer = $this->group->getUserGroups($idUser);
        static::$groupsTransferCache[$idUser] = $groupsTransfer;

        return $groupsTransfer;
    }

    /**
     * @param array<int> $groupIds
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\RulesTransfer
     */
    protected function getRulesTransferByGroupIds(array $groupIds, UserTransfer $userTransfer): RulesTransfer
    {
        if ($this->isCacheShouldBeIgnored($userTransfer)) {
            return $this->getRulesForGroupIds($groupIds);
        }

        $cacheKey = implode('-', $groupIds);
        if (isset(static::$rulesTransferCache[$cacheKey])) {
            return static::$rulesTransferCache[$cacheKey];
        }

        $rulesTransfer = $this->getRulesForGroupIds($groupIds);
        static::$rulesTransferCache[$cacheKey] = $rulesTransfer;

        return $rulesTransfer;
    }

    public function cachableIsAllowed(GroupsTransfer $groupsTransfer, UserTransfer $userTransfer, string $bundle, string $controller, string $action): bool
    {
        if ($this->isCacheShouldBeIgnored($userTransfer) || static::$ruleValidator === null) {
            $this->provideUserRuleWhitelist();

            $groupIds = [];
            foreach ($groupsTransfer->getGroups() as $groupEntity) {
                $groupIds[] = $groupEntity->getIdAclGroup();
            }
            $rulesTransfer = $this->getRulesTransferByGroupIds($groupIds, $userTransfer);

            if (!$rulesTransfer->getRules()->count()) {
                return false;
            }

            $this->rulesValidator->setRules($rulesTransfer);

            static::$ruleValidator = $this->rulesValidator;

            return $this->rulesValidator->isAccessible($bundle, $controller, $action);
        }

        return static::$ruleValidator->isAccessible($bundle, $controller, $action);
    }

    /**
     * @return void
     */
    protected function provideUserRuleWhitelist()
    {
        $ruleWhitelist = $this->config->getUserRuleWhitelist();

        foreach ($ruleWhitelist as $rule) {
            $rulesTransfer = new RuleTransfer();
            $rulesTransfer->fromArray($rule, true);
            $this->rulesValidator->addRule($rulesTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     * @param string $bundle
     * @param string $controller
     * @param string $action
     *
     * @return bool|null
     */
    protected function executeAclAccessCheckerStrategyPlugins(
        UserTransfer $userTransfer,
        string $bundle,
        string $controller,
        string $action
    ): ?bool {
        $isAllowed = null;
        $ruleTransfer = (new RuleTransfer())->setBundle($bundle)->setAction($action)->setController($controller);

        foreach ($this->aclAccessCheckerStrategyPlugins as $aclAccessCheckerStrategyPlugin) {
            if ($aclAccessCheckerStrategyPlugin->isApplicable($userTransfer, $ruleTransfer)) {
                $isAllowed = $aclAccessCheckerStrategyPlugin->checkAccess($userTransfer, $ruleTransfer);

                break;
            }
        }

        return $isAllowed;
    }

    /**
     * @return \Generated\Shared\Transfer\RulesTransfer
     */
    public function getIgnorableRules(): RulesTransfer
    {
        if (static::$ignoredRulesCache === null) {
            $ignoredRules = $this->config->getRules();

            $ruleTransfers = [];
            foreach ($ignoredRules as $arrayRule) {
                $ruleTransfer = new RuleTransfer();
                $ruleTransfer->setBundle($arrayRule['bundle']);
                $ruleTransfer->setController($arrayRule['controller']);
                $ruleTransfer->setAction($arrayRule['action']);
                $ruleTransfer->setType($arrayRule['type']);
                $ruleTransfers[] = $ruleTransfer;
            }

            static::$ignoredRulesCache = (new RulesTransfer())->setRules(new ArrayObject($ruleTransfers));
        }

        return static::$ignoredRulesCache;
    }

    protected function isCacheShouldBeIgnored(UserTransfer $userTransfer): bool
    {
        /*
        * HOTFIX from task FRW-9814: Bypass ACL cache for merchant agents to prevent token persistence issues
        *
        * Problem: Merchant agents may have incomplete token initialization during session restoration,
        * causing the ACL cache to store incorrect "access denied" results. This happens because:
        *
        * Performance Impact: Minimal - only affects merchant agent users, regular users keep cache.
        * Alternative: Complex conditional caching based on token state (more error-prone).
        */
        if (APPLICATION === 'MERCHANT_PORTAL' && $userTransfer->getIsMerchantAgent()) {
            return true;
        }

        return false;
    }
}
