<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Acl\Communication\Form\DataProvider;

use Spryker\Zed\Acl\Business\AclFacade;

class AclRoleFormDataProvider
{

    /**
     * @var \Spryker\Zed\Acl\Business\AclFacade
     */
    protected $aclFacade;

    /**
     * @param \Spryker\Zed\Acl\Business\AclFacade $aclFacade
     */
    public function __construct(AclFacade $aclFacade)
    {
        $this->aclFacade = $aclFacade;
    }

    /**
     * @param int $idAclRole
     *
     * @return array
     */
    public function getData($idAclRole)
    {
        $roleTransfer = $this->aclFacade->getRoleById($idAclRole);

        return $roleTransfer->toArray();
    }

}
