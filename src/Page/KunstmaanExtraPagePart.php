<?php

namespace ArsThanea\KunstmaanExtraBundle\Page;

use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

abstract class KunstmaanExtraPagePart extends AbstractPagePart
{

    const PAGEPART = true;

    /**
     * @return string — without the "Bundle" suffix"
     */
    abstract public function getBundleName();

    public function getDefaultAdminType()
    {
        $type = substr(str_replace('\\Entity\\', '\\Form\\', $this->getType()), 0, - strlen('PagePart')) . 'AdminType';

        return $type;
    }

    public function getDefaultView()
    {
        $name = $this->getPagePartName();

        return sprintf('@%s/PageParts/%s/%s.default.html.twig', $this->getBundleName(), substr($name, 0, - strlen('PagePart')), $name);
    }

    public function getAdminView()
    {
        return strtr($this->getDefaultView(), ['.default.' => '.admin.']);
    }

    private function getType()
    {
        return ClassLookup::getClass($this);
    }

    private function getPagePartName()
    {
        $type = array_reverse(explode("\\", $this->getType()));

        return reset($type);
    }
}
