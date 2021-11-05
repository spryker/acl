<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Acl\Persistence;

use Generated\Shared\Transfer\RolesTransfer;
use Orm\Zed\Acl\Persistence\Map\SpyAclGroupsHasRolesTableMap;
use Orm\Zed\Acl\Persistence\Map\SpyAclGroupTableMap;
use Orm\Zed\Acl\Persistence\Map\SpyAclRoleTableMap;
use Orm\Zed\Acl\Persistence\Map\SpyAclRuleTableMap;
use Orm\Zed\Acl\Persistence\Map\SpyAclUserHasGroupTableMap;
use Orm\Zed\User\Persistence\Map\SpyUserTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Shared\Acl\AclConstants;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * @method \Spryker\Zed\Acl\Persistence\AclPersistenceFactory getFactory()
 */
class AclQueryContainer extends AbstractQueryContainer implements AclQueryContainerInterface
{
    /**
     * @var string
     */
    public const ROLE_NAME = 'role_name';

    /**
     * @var string
     */
    public const TYPE = 'type';

    /**
     * @var string
     */
    public const BUNDLE = 'bundle';

    /**
     * @var string
     */
    public const CONTROLLER = 'controller';

    /**
     * @var string
     */
    public const ACTION = 'action';

    /**
     * @var string
     */
    public const HAS_ROLE = 'has_role';

    /**
     * @var string
     */
    public const SPY_ACL_GROUPS_HAS_ROLES = 'SpyAclGroupsHasRoles';

    /**
     * @var string
     */
    public const GROUP_NAME = 'group_name';

    /**
     * @var string
     */
    public const ID_ACL_GROUP = 'id_acl_group';

    /**
     * @var string
     */
    public const GROUP_JOIN = 'groupJoin';

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $name
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclGroupQuery
     */
    public function queryGroupByName($name)
    {
        $query = $this->queryGroup();

        $query->filterByName($name);

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $id
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclGroupQuery
     */
    public function queryGroupById($id)
    {
        $query = $this->queryGroup();

        $query->filterByIdAclGroup($id);

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclGroupQuery
     */
    public function queryGroup()
    {
        return $this->getFactory()->createGroupQuery();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclRoleQuery
     */
    public function queryRole()
    {
        return $this->getFactory()->createRoleQuery();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $id
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclRoleQuery
     */
    public function queryRoleById($id)
    {
        $query = $this->getFactory()->createRoleQuery();

        $query->filterByIdAclRole($id);

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idRole
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclGroupsHasRolesQuery
     */
    public function queryRoleHasGroup($idRole)
    {
        $query = $this->getFactory()->createGroupHasRoleQuery();
        $query->filterByFkAclRole($idRole);

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $name
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclRoleQuery
     */
    public function queryRoleByName($name)
    {
        $query = $this->getFactory()->createRoleQuery();

        $query->filterByName($name);

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idGroup
     * @param int $idRole
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclGroupsHasRolesQuery
     */
    public function queryGroupHasRoleById($idGroup, $idRole)
    {
        $query = $this->getFactory()->createGroupHasRoleQuery();

        $query->filterByFkAclGroup($idGroup)
            ->filterByFkAclRole($idRole);

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idGroup
     * @param int $idUser
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclUserHasGroupQuery
     */
    public function queryUserHasGroupById($idGroup, $idUser)
    {
        $query = $this->getFactory()->createUserHasRoleQuery();

        $query->filterByFkAclGroup($idGroup)
              ->filterByFkUser($idUser);

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idGroup
     *
     * @return \Orm\Zed\User\Persistence\SpyUserQuery
     */
    public function queryGroupUsers($idGroup)
    {
        $query = $this->getFactory()->createUserQuery();

        $join = new Join();

        $join->addCondition(
            SpyUserTableMap::COL_ID_USER,
            SpyAclUserHasGroupTableMap::COL_FK_USER,
        );

        $query->addJoinObject($join, static::GROUP_JOIN);

        $condition = sprintf('%s = %s', SpyAclUserHasGroupTableMap::COL_FK_ACL_GROUP, $idGroup);
        $query->addJoinCondition(
            static::GROUP_JOIN,
            $condition,
        );

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idGroup
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclRoleQuery
     */
    public function queryGroupRoles($idGroup)
    {
        $query = $this->getFactory()->createRoleQuery();

        $query->useSpyAclGroupsHasRolesQuery()
            ->filterByFkAclGroup($idGroup)
            ->endUse();

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $id
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclRuleQuery
     */
    public function queryRuleById($id)
    {
        $query = $this->getFactory()->createRuleQuery();

        $query->filterByIdAclRule($id);

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $roleId
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclRuleQuery
     */
    public function queryRuleByRoleId($roleId)
    {
        $query = $this->getFactory()->createRuleQuery();
        $query->filterByFkAclRole($roleId);

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Propel\Runtime\Collection\ObjectCollection $relationshipCollection
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclRuleQuery
     */
    public function queryGroupRules(ObjectCollection $relationshipCollection)
    {
        $query = $this->getFactory()->createRuleQuery();
        $query->useAclRoleQuery()->filterBySpyAclGroupsHasRoles($relationshipCollection)->endUse();

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idGroup
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclGroupsHasRolesQuery
     */
    public function queryGroupHasRole($idGroup)
    {
        $query = $this->getFactory()->createGroupHasRoleQuery();
        $query->filterByFkAclGroup($idGroup);

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @deprecated Broken and will be removed in the next major.
     *
     * @param \Generated\Shared\Transfer\RolesTransfer $roles
     * @param string $bundle
     * @param string $controller
     * @param string $action
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclRuleQuery
     */
    public function queryRuleByPathAndRoles(
        RolesTransfer $roles,
        $bundle = AclConstants::VALIDATOR_WILDCARD,
        $controller = AclConstants::VALIDATOR_WILDCARD,
        $action = AclConstants::VALIDATOR_WILDCARD
    ) {
        $query = $this->getFactory()->createRuleQuery();

        if ($bundle !== AclConstants::VALIDATOR_WILDCARD) {
            $query->filterByBundle($bundle);
        }

        if ($controller !== AclConstants::VALIDATOR_WILDCARD) {
            $query->filterByController($controller);
        }

        if ($action !== AclConstants::VALIDATOR_WILDCARD) {
            $query->filterByAction($action);
        }

        $inRoles = [];
        foreach ($roles->getRoles() as $role) {
            $inRoles[] = $role->getIdAclRole();
        }

        $query->filterByFkAclRole($inRoles, Criteria::IN);

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idAclRole
     * @param string $bundle
     * @param string $controller
     * @param string $action
     * @param string $type
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclRuleQuery
     */
    public function queryRuleByPathAndRole($idAclRole, $bundle, $controller, $action, $type)
    {
        $query = $this->getFactory()->createRuleQuery();
        $query->filterByFkAclRole($idAclRole, Criteria::EQUAL)
            ->filterByBundle($bundle, Criteria::EQUAL)
            ->filterByController($controller, Criteria::EQUAL)
            ->filterByAction($action, Criteria::EQUAL)
            ->filterByType($type, Criteria::EQUAL);

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idUser
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclGroupQuery
     */
    public function queryUserGroupByIdUser($idUser)
    {
        $query = $this->getFactory()->createGroupQuery();
        $query->useSpyAclUserHasGroupQuery()
            ->filterByFkUser($idUser)
            ->endUse();

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return \Orm\Zed\User\Persistence\SpyUserQuery
     */
    public function queryUsersWithGroup()
    {
        $query = $this->getFactory()->createUserQuery();

        $query->addJoin(
            SpyUserTableMap::COL_ID_USER,
            SpyAclUserHasGroupTableMap::COL_FK_USER,
            Criteria::LEFT_JOIN,
        );

        $query->addJoin(
            SpyAclUserHasGroupTableMap::COL_FK_ACL_GROUP,
            SpyAclGroupTableMap::COL_ID_ACL_GROUP,
            Criteria::LEFT_JOIN,
        );

        $query->withColumn(SpyAclGroupTableMap::COL_NAME, static::GROUP_NAME);
        $query->withColumn(SpyAclGroupTableMap::COL_ID_ACL_GROUP, static::ID_ACL_GROUP);

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idGroup
     *
     * @return \Orm\Zed\Acl\Persistence\SpyAclRoleQuery
     */
    public function queryRulesFromGroup($idGroup)
    {
        $query = $this->getFactory()->createRoleQuery();
        $query->joinAclRule();
        $query->leftJoinSpyAclGroupsHasRoles();

        $condition = sprintf('%s = %s', SpyAclGroupsHasRolesTableMap::COL_FK_ACL_GROUP, $idGroup);
        $query->addJoinCondition(
            static::SPY_ACL_GROUPS_HAS_ROLES,
            $condition,
        );

        $hasRole = sprintf('COUNT(%s)', SpyAclGroupsHasRolesTableMap::COL_FK_ACL_ROLE);

        $query->withColumn(SpyAclRoleTableMap::COL_NAME, static::ROLE_NAME);
        $query->withColumn(SpyAclRuleTableMap::COL_TYPE, static::TYPE);
        $query->withColumn(SpyAclRuleTableMap::COL_BUNDLE, static::BUNDLE);
        $query->withColumn(SpyAclRuleTableMap::COL_CONTROLLER, static::CONTROLLER);
        $query->withColumn(SpyAclRuleTableMap::COL_ACTION, static::ACTION);
        $query->withColumn($hasRole, static::HAS_ROLE);

        return $query;
    }
}
