<?php

namespace ArsThanea\KunstmaanExtraBundle\Twig;

use ArsThanea\KunstmaanExtraBundle\Url\AssetUrlServiceInterface;

class AssetUrlTwigExtension extends \Twig_Extension
{
    /**
     * @var AssetUrlServiceInterface
     */
    private $urlService;

    public function __construct(AssetUrlServiceInterface $urlService)
    {
        $this->urlService = $urlService;
    }

    public function getFunctions()
    {
        return [
            'asset_url' => new \Twig_SimpleFunction('asset_url', [$this->urlService, 'getAssetUrl']),
            'assets_url' => new \Twig_SimpleFunction('assets_url', [$this->urlService, 'getAssetUrl']),
        ];
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'kunstmaan_extra_asset_url';
    }
}
