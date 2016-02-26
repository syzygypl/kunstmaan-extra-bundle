<?php

namespace ArsThanea\KunstmaanExtraBundle\Url;

use ArsThanea\KunstmaanExtraBundle\SiteTree\PublicNodeVersions;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PageUrlService
{
    /**
     * @var PublicNodeVersions
     */
    private $nodeVersions;

    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @param PublicNodeVersions    $nodeVersions
     * @param UrlGeneratorInterface $generator
     */
    public function __construct(PublicNodeVersions $nodeVersions, UrlGeneratorInterface $generator)
    {
        $this->nodeVersions = $nodeVersions;
        $this->generator = $generator;
    }

    public function generate(HasNodeInterface $page, $absolute = false, array $parameters = [])
    {
        $nodeVersion = $this->nodeVersions->getNodeVersionFor($page);
        if (null === $nodeVersion) {
            throw new \InvalidArgumentException('This Page has no Node associated with it!');
        }

        $nodeTranslation = $nodeVersion->getNodeTranslation();

        return $this->generator->generate(
            "_slug",
            ["url" => $nodeTranslation->getUrl(), "_locale" => $nodeTranslation->getLang()] + $parameters,
            $absolute ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH
        );
    }

}