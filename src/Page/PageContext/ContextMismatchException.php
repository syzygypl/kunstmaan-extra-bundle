<?php

namespace ArsThanea\KunstmaanExtraBundle\Page\PageContext;

class ContextMismatchException extends \Exception
{
    /**
     * @var PageContextProviderInterface
     */
    private $context;

    /**
     * @param PageContextProviderInterface $context
     * @param array $keys
     * @param int $valuesCount
     */
    public function __construct(PageContextProviderInterface $context, $keys, $valuesCount)
    {
        $message = vsprintf('Unexpected number of values. Expected %d for keys %s, %d given', [
            sizeof($keys), implode(', ', $keys), $valuesCount
        ]);
        parent::__construct($message, 0);

        $this->context = $context;
    }

    /**
     * @return PageContextProviderInterface
     */
    public function getContext()
    {
        return $this->context;
    }
}
