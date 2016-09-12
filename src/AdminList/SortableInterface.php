<?php

namespace ArsThanea\KunstmaanExtraBundle\AdminList;

use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;

interface SortableInterface extends \Kunstmaan\AdminListBundle\AdminList\SortableInterface, AdminListConfiguratorInterface
{
    /**
     * @param bool $adapt
     * @return QueryBuilder
     */
    public function getQueryBuilder($adapt = false);

}
