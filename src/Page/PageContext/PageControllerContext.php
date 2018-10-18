<?php

namespace ArsThanea\KunstmaanExtraBundle\Page\PageContext;

use ArsThanea\KunstmaanExtraBundle\Page\PageController\PageControllerInterface;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PageControllerContext implements PageContextProviderInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    /**
     * @return string|array — one or multiple keys
     */
    public function getContextName()
    {
        return null;
    }

    /**
     * @param HasNodeInterface $page
     * @param array $context
     *
     * @return mixed
     */
    public function getContextValue(HasNodeInterface $page, array $context)
    {
        $controllerClass = substr(str_replace('Entity', 'Controller', ClassLookup::getClass($page)), 0, -strlen('Page')) . 'Controller';

        if (false === class_exists($controllerClass)) {
            return [];
        }

        /** @var PageControllerInterface|ContainerAwareInterface $controller */
        $controller = $this->container->has($controllerClass)
            ? $this->container->get($controllerClass)
            : new $controllerClass;

        if (false === $controller instanceof PageControllerInterface) {
            throw new \RuntimeException("$controllerClass needs to implement " . PageControllerInterface::class);
        }

        if ($controller instanceof ContainerAwareInterface) {
            $controller->setContainer($this->container);
        }

        return $controller->serviceAction($page, $context);
    }

}
