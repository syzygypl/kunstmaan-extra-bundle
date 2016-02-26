<?php


namespace ArsThanea\KunstmaanExtraBundle\SiteTree\RefIdProvider;


use ArsThanea\KunstmaanExtraBundle\SiteTree\HasRefInterface;
use Doctrine\Common\Collections\Collection;

class ChainRefIdProvider implements RefIdProviderInterface
{
    /**
     * @var Collection|RefIdProviderInterface[]
     */
    private $providers;

    /**
     * ChainRefIdProvider constructor.
     *
     * @param RefIdProviderInterface[]|Collection $providers
     */
    public function __construct(Collection $providers)
    {
        $this->providers = $providers;
    }


    /**
     * @param mixed $value
     *
     * @return HasRefInterface|null
     */
    public function getRefId($value)
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