<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Acl\Business\Writer;

use Generated\Shared\Transfer\RoleTransfer;

interface RoleWriterInterface
{
    public function createRole(RoleTransfer $roleTransfer): RoleTransfer;
}
