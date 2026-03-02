<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Acl\Persistence;

use Generated\Shared\Transfer\GroupTransfer;
use Generated\Shared\Transfer\RoleTransfer;

interface AclEntityManagerInterface
{
    public function createGroup(GroupTransfer $groupTransfer): GroupTransfer;

    public function createRole(RoleTransfer $roleTransfer): RoleTransfer;
}
