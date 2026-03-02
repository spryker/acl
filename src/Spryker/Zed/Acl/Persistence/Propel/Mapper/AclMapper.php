<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Acl\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\AclUserHasGroupCollectionTransfer;
use Generated\Shared\Transfer\AclUserHasGroupTransfer;
use Generated\Shared\Transfer\GroupTransfer;
use Generated\Shared\Transfer\RoleTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Orm\Zed\Acl\Persistence\SpyAclGroup;
use Orm\Zed\Acl\Persistence\SpyAclRole;
use Orm\Zed\Acl\Persistence\SpyAclUserHasGroup;
use Propel\Runtime\Collection\Collection;

class AclMapper
{
    public function mapAclGroupEntityToGroupTransfer(SpyAclGroup $aclGroup, GroupTransfer $groupTransfer): GroupTransfer
    {
        $groupTransfer->fromArray($aclGroup->toArray(), true);

        return $groupTransfer;
    }

    public function mapAclGroupTransferToGroupEntity(GroupTransfer $groupTransfer, SpyAclGroup $aclGroupEntity): SpyAclGroup
    {
        $aclGroupEntity->fromArray($groupTransfer->toArray());

        return $aclGroupEntity;
    }

    public function mapAclRoleEntityToRoleTransfer(SpyAclRole $aclRoleEntity, RoleTransfer $roleTransfer): RoleTransfer
    {
        $roleTransfer->fromArray($aclRoleEntity->toArray(), true);

        return $roleTransfer;
    }

    public function mapAclRoleTransferToRoleEntity(RoleTransfer $roleTransfer, SpyAclRole $aclRoleEntity): SpyAclRole
    {
        $aclRoleEntity->fromArray($roleTransfer->toArray());

        return $aclRoleEntity;
    }

    /**
     * @param \Propel\Runtime\Collection\Collection<\Orm\Zed\Acl\Persistence\SpyAclUserHasGroup> $aclUserHasGroupEntities
     * @param \Generated\Shared\Transfer\AclUserHasGroupCollectionTransfer $aclUserHasGroupCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\AclUserHasGroupCollectionTransfer
     */
    public function mapAclUserHasGroupEntitiesToAclUserHasGroupCollectionTransfer(
        Collection $aclUserHasGroupEntities,
        AclUserHasGroupCollectionTransfer $aclUserHasGroupCollectionTransfer
    ): AclUserHasGroupCollectionTransfer {
        foreach ($aclUserHasGroupEntities as $aclUserHasGroupEntity) {
            $aclUserHasGroupCollectionTransfer->addAclUserHasGroup(
                $this->mapAclUserHasGroupEntityToAclUserHasGroupTransfer(
                    $aclUserHasGroupEntity,
                    new AclUserHasGroupTransfer(),
                ),
            );
        }

        return $aclUserHasGroupCollectionTransfer;
    }

    protected function mapAclUserHasGroupEntityToAclUserHasGroupTransfer(
        SpyAclUserHasGroup $aclUserHasGroupEntity,
        AclUserHasGroupTransfer $aclUserHasGroupTransfer
    ): AclUserHasGroupTransfer {
        $aclUserHasGroupTransfer->setUser((new UserTransfer())->setIdUser($aclUserHasGroupEntity->getFkUser()))
            ->setGroup((new GroupTransfer())->setIdAclGroup($aclUserHasGroupEntity->getFkAclGroup()));

        return $aclUserHasGroupTransfer;
    }
}
