<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Acl\Dependency\Facade;

use Generated\Shared\Transfer\RouterActionCollectionTransfer;
use Generated\Shared\Transfer\RouterBundleCollectionTransfer;
use Generated\Shared\Transfer\RouterControllerCollectionTransfer;

interface AclToRouterFacadeInterface
{
    public function getRouterBundleCollection(): RouterBundleCollectionTransfer;

    public function getRouterControllerCollection(string $bundle): RouterControllerCollectionTransfer;

    public function getRouterActionCollection(string $bundle, string $controller): RouterActionCollectionTransfer;
}
