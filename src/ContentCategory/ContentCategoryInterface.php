<?php

namespace ArsThanea\KunstmaanExtraBundle\ContentCategory;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;

interface ContentCategoryInterface
{

    /**
     * @param HasNodeInterface $page
     *
     * @return Category
     */
    public function getRootCategory(HasNodeInterface $page);

    /**
     * @param HasNodeInterface $page
     *
     * @return Category
     */
    public function getMainCategory(HasNodeInterface $page);

    /**
     * @param HasNodeInterface $page
     *
     * @param bool             $hidden
     *
     * @return Category|null
     */
    public function getParentCategory(HasNodeInterface $page, $hidden = false);

    /**
     * @param HasNodeInterface $page
     * @param bool             $full
     *
     * @return Category[]
     */
    public function getBreadcrumbs(HasNodeInterface $page, $full = false);
}
