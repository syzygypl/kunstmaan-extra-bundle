<?php

namespace ArsThanea\KunstmaanExtraBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;

abstract class AdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator implements SortableInterface
{
    public function getQueryBuilder($adapt = false)
    {
        $qb = parent::getQueryBuilder();

        if ($adapt) {
            $this->adaptQueryBuilder($qb);
        }

        return $qb;
    }

    /**
     * @return string
     */
    public function getSortableField()
    {
        return 'weight';
    }
}
