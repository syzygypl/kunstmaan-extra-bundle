<?php


namespace ArsThanea\KunstmaanExtraBundle\SiteTree\RefIdProvider;


use ArsThanea\KunstmaanExtraBundle\SiteTree\HasRefInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ChainRefIdProvider implements RefIdProviderInterface
{
    /**
     * @var Collection|RefIdProviderInterface[]
     */
    private $providers;

    /**
     * ChainRefIdProvider constructor.
     *
     * @param ServiceLocator $providers
     */
    public function __construct(ServiceLocator $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @param mixed $value
     *
     * @return HasRefInterface|null
     */
    public function getRefId($value): ?HasRefInterface
    {
        foreach ($this->providers as $provider) {
            $ref = $provider->getRefId($value);

            if ($ref) {
                return $ref;
            }
        }

        return null;
    }
}
