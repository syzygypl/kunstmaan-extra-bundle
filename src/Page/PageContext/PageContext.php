<?php

namespace ArsThanea\KunstmaanExtraBundle\Page\PageContext;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;

class PageContext
{
    /**
     * @var PageContextProviderInterface[]
     */
    private $providers;

    /**
     * @param PageContextProviderInterface[] $providers
     */
    public function __construct($providers)
    {
        $this->providers = $providers;
    }

    public function getPageContext(HasNodeInterface $page)
    {
        $context = [];

        foreach ($this->providers as $provider) {
            $keys = $provider->getContextName();
            $values = $provider->getContextValue($page, $context);

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
