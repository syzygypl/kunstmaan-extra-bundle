<?php

namespace ArsThanea\KunstmaanExtraBundle\Page\PageController;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;

interface PageControllerInterface
{

    /**
     * @param HasNodeInterface $page
     * @param array $context
     * @return array
     */
    public function serviceAction(HasNodeInterface $page, array $context);
}
