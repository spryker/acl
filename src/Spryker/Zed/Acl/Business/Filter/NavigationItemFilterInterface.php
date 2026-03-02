<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Acl\Business\Filter;

use Generated\Shared\Transfer\NavigationItemCollectionTransfer;

interface NavigationItemFilterInterface
{
    public function filterNavigationItemCollectionByAccessibility(
        NavigationItemCollectionTransfer $navigationItemCollectionTransfer
    ): NavigationItemCollectionTransfer;
}
