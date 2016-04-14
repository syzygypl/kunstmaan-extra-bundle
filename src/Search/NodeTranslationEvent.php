<?php

namespace ArsThanea\KunstmaanExtraBundle\Search;

use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Symfony\Component\EventDispatcher\Event;

class NodeTranslationEvent extends Event
{
    const NODE_TRANSLATION_INDEX_BEFORE = 'kunstmaan_extra.before_index';

    /**
     * @var NodeTranslation
     */
    private $nodeTranslation;

    /**
     * @var bool
     */
    private $indexable = true;

    /**
     * NodeTranslationEvent constructor.
     *
     * @param NodeTranslation $nodeTranslation
     */
    public function __construct(NodeTranslation $nodeTranslation)
    {
        $this->nodeTranslation = $nodeTranslation;
    }

    /**
     * @return boolean
     */
    public function isIndexable()
    {
        return $this->indexable;
    }

    /**
     * @param boolean $indexable
     *
     * @return $this
     */
    public function setIndexable($indexable)
    {
        $this->indexable = (bool)$indexable;

        return $this;
    }


    /**
     * @return NodeTranslation
     */
    public function getNodeTranslation()
    {
        return $this->nodeTranslation;
    }

}
