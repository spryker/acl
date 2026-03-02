<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Acl\Persistence;

use Generated\Shared\Transfer\GroupTransfer;
use Generated\Shared\Transfer\RoleTransfer;
use Orm\Zed\Acl\Persistence\SpyAclGroup;
use Orm\Zed\Acl\Persistence\SpyAclRole;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \Spryker\Zed\Acl\Persistence\AclPersistenceFactory getFactory()
 */
class AclEntityManager extends AbstractEntityManager implements AclEntityManagerInterface
{
    public function createGroup(GroupTransfer $groupTransfer): GroupTransfer
    {
        $groupTransfer->requireName();

        $aclMapper = $this->getFactory()->createAclMapper();
        $aclGroupEntity = $aclMapper->mapAclGroupTransferToGroupEntity($groupTransfer, new SpyAclGroup());

        $aclGroupEntity->save();

        return $aclMapper->mapAclGroupEntityToGroupTransfer($aclGroupEntity, new GroupTransfer());
    }

    public function createRole(RoleTransfer $roleTransfer): RoleTransfer
    {
        $roleTransfer->requireName();

        $aclMapper = $this->getFactory()->createAclMapper();
        $aclRoleEntity = $aclMapper->mapAclRoleTransferToRoleEntity($roleTransfer, new SpyAclRole());

        $aclRoleEntity->save();

        return $aclMapper->mapAclRoleEntityToRoleTransfer($aclRoleEntity, $roleTransfer);
    }
}
