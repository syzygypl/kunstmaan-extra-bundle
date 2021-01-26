<?php

namespace ArsThanea\KunstmaanExtraBundle\Page\PageContext;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

class PageContext
{
    /**
     * @var PageContextProviderInterface[]
     */
    private $providers;

    /**
     * @param ServiceLocator $providers
     */
    public function __construct(ServiceLocator $providers)
    {
        $this->providers = $providers;
    }

    public function getPageContext(HasNodeInterface $page)
    {
        $context = [];

        foreach ($this->providers as $provider) {
            $keys = $provider->getContextName();
            $values = $provider->getContextValue($page, $context);

            if (null === $keys && is_array($values)) {
                $keys = array_keys($values);
            }

            if (false === is_array($keys)) {
                $keys = [$keys];
                $values = [$values];
            }

            if (sizeof($keys) !== sizeof($values)) {
                throw new ContextMismatchException($provider, $keys, sizeof($values));
            }

            $context += array_combine($keys, $values);
        }

        return $context;
    }
}
