<?php

namespace ArsThanea\KunstmaanExtraBundle\Imagine;

use GuzzleHttp\Client;
use Liip\ImagineBundle\Binary\BinaryInterface;
use Liip\ImagineBundle\Imagine\Filter\PostProcessor\PostProcessorInterface;
use Liip\ImagineBundle\Model\Binary;

class TinypngComPostProcessor implements PostProcessorInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $apiKey;

    public function __construct(Client $client, $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }


    /**
     * @param BinaryInterface $binary
     *
     * @return BinaryInterface
     */
    public function process(BinaryInterface $binary)
    {
        $binary->getContent();

        $response = $this->client->request('POST', 'https://api.tinify.com/shrink', [
            'auth' => ['api', $this->apiKey],
            'body' => $binary->getContent(),
        ]);

        $data = json_decode($response->getBody(), true);
        $url = $data['output']['url'];

        return new Binary(file_get_contents($url), $binary->getMimeType(), $binary->getFormat());
    }
}
