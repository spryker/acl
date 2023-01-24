<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Acl\Communication\Plugin\EventDispatcher;

use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\EventDispatcher\EventDispatcherInterface;
use Spryker\Shared\EventDispatcherExtension\Dependency\Plugin\EventDispatcherPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @method \Spryker\Zed\Acl\Communication\AclCommunicationFactory getFactory()
 * @method \Spryker\Zed\Acl\Business\AclFacadeInterface getFacade()
 * @method \Spryker\Zed\Acl\AclConfig getConfig()
 * @method \Spryker\Zed\Acl\Persistence\AclQueryContainerInterface getQueryContainer()
 */
class AccessControlEventDispatcherPlugin extends AbstractPlugin implements EventDispatcherPluginInterface
{
    /**
     * {@inheritDoc}
     * - Adds a listener to the `\Symfony\Component\HttpKernel\KernelEvents::REQUEST` which checks if the user is allowed to access the current resource.
     *
     * @api
     *
     * @param \Spryker\Shared\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Spryker\Shared\EventDispatcher\EventDispatcherInterface
     */
    public function extend(EventDispatcherInterface $eventDispatcher, ContainerInterface $container): EventDispatcherInterface
    {
        $eventDispatcher->addListener(KernelEvents::REQUEST, function (RequestEvent $event): RequestEvent {
            return $this->onKernelRequest($event);
        });

        return $eventDispatcher;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     *
     * @return \Symfony\Component\HttpKernel\Event\RequestEvent
     */
    protected function onKernelRequest(RequestEvent $event): RequestEvent
    {
        $aclFacade = $this->getFacade();
        $config = $this->getFactory()->getConfig();

        $request = $event->getRequest();

        $module = $request->attributes->get('module');
        $controller = $request->attributes->get('controller');
        $action = $request->attributes->get('action');

        if (!$this->isMainRequest($event) || $aclFacade->isIgnorable($module, $controller, $action)) {
            return $event;
        }

        if (!$aclFacade->hasCurrentUser() || !$aclFacade->checkAccess($aclFacade->getCurrentUser(), $module, $controller, $action)) {
            $event->setResponse(new RedirectResponse($config->getAccessDeniedUri()));

            return $event;
        }

        return $event;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     *
     * @return bool
     */
    protected function isMainRequest(RequestEvent $event): bool
    {
        if (method_exists($event, 'isMasterRequest')) {
            return $event->isMasterRequest();
        }

        return $event->isMainRequest();
    }
}
