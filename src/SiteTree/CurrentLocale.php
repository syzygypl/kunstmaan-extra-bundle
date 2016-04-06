<?php

namespace ArsThanea\KunstmaanExtraBundle\SiteTree;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CurrentLocale implements CurrentLocaleInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;

    /**
     * @param RequestStack $requestStack
     * @param DomainConfigurationInterface $domainConfiguration
     */
    public function __construct(RequestStack $requestStack, DomainConfigurationInterface $domainConfiguration)
    {
        $this->requestStack = $requestStack;
        $this->domainConfiguration = $domainConfiguration;
    }

    public function getCurrentLocale()
    {
        $request = $this->requestStack->getCurrentRequest();

        return $request ? $request->getLocale() : $this->domainConfiguration->getDefaultLocale();
    }

}
