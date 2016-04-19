<?php

namespace ArsThanea\KunstmaanExtraBundle\Page\PageContext;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;

interface PageContextProviderInterface
{
    /**
     * @param HasNodeInterface $page
     * @param array            $context
     *
     * @return mixed
     */
    public function getContextValue(HasNodeInterface $page, array $context);

    /**
     * @return string|array — one or multiple keys
     */
    public function getContextName();
}
