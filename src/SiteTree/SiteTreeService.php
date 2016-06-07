<?php

namespace ArsThanea\KunstmaanExtraBundle\SiteTree;

use ArsThanea\KunstmaanExtraBundle\ContentCategory\Category;
use Doctrine\ORM\Query\Expr\Join;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Repository\NodeRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteTreeService
{
    /**
     * @var NodeRepository
     */
    private $nodeRepository;

    /**
     * @var PublicNodeVersions
     */
    private $publicNodeVersions;
    /**
     * @var CurrentLocaleInterface
     */
    private $currentLocale;

    public function __construct(NodeRepository $nodeRepository, PublicNodeVersions $publicNodeVersions, CurrentLocaleInterface $currentLocale)
    {
        $this->nodeRepository = $nodeRepository;
        $this->publicNodeVersions = $publicNodeVersions;
        $this->currentLocale = $currentLocale;
    }

    /**
     * @param HasNodeInterface|Node|null $parent
     * @param array                      $options
     *
     * @return Branch
     */
    public function getChildren($parent = null, array $options = [])
    {
        if ($parent instanceof HasNodeInterface) {
            $node = $this->publicNodeVersions->getNodeFor($parent);
        } elseif ($parent instanceof Category) {
            $node = $this->nodeRepository->find($parent->getNodeId());
        } elseif ($parent instanceof Node) {
            $node = $parent;
        } else {
            $node = null;
        }

        return $this->getNodeChildren(['parent' => $node] + $options);
    }

    /**
     * @param array $options
     *
     * @return mixed
     */
    private function getNodeChildren(array $options)
    {
        /** @noinspection PhpUnusedParameterInspection */
        $options = (new OptionsResolver)->setDefaults([
            'depth'           => 1,
            'refName'         => null,
            'parent'          => null,
            'lang'            => $this->currentLocale->getCurrentLocale(),
            'include_root'    => false,
            'include_hidden'  => false,
            'include_offline' => false,
            'flatten'         => false,
            'limit'           => 0,
        ])
            ->setNormalizer('refName', function ($options, $value) {
                return $value ? (array)$value : [];
            })
            ->setAllowedTypes('parent', ['null', Node::class])
            ->setAllowedTypes('depth', 'integer')
            ->setAllowedTypes('limit', 'integer')
            ->setAllowedTypes('include_root', 'bool')
            ->setAllowedTypes('include_hidden', 'bool')
            ->setAllowedTypes('flatten', 'bool')
            ->resolve($options);

        $qb = $this->nodeRepository
            ->createQueryBuilder('node')
            ->leftJoin('node.nodeTranslations', 'nt', Join::WITH, 'nt.node = node and nt.lang = :lang')
            ->leftJoin('nt.publicNodeVersion', 'nv')
            ->leftJoin('node.parent', 'parent')
            ->select('parent.id as parentId', 'node.id', 'nt.title', 'nt.url', 'nt.lang', 'nv.refId', 'nv.refEntityName', 'node.internalName')
            ->where('nt.lang = :lang')
            ->andWhere('node.deleted = 0')
            ->orderBy('node.lvl, nt.weight')
            ->setParameter('lang', $options['lang']);

        if ($options['limit']) {
            $qb->setMaxResults($options['limit']);
        }

        if (false === $options['include_hidden']) {
            $qb->andWhere('node.hiddenFromNav = 0');
        }

        if (false === $options['include_offline']) {
            $qb->andWhere('nt.online = 1');
        }

        if ($options['refName']) {
            $qb
                ->andWhere('node.refEntityName in (:refName)')
                ->setParameter('refName', $options['refName']);
        }

        $level = 0;
        $nodeId = null;
        if ($options['parent']) {
            /** @var Node $parent */
            $parent = $options['parent'];
            $nodeId = $parent->getId();
            $level = $parent->getLevel();


            $qb->andWhere('node.lft >= :minLeft')
                ->andWhere('node.rgt <= :maxRight')
                ->setParameter('minLeft', $parent->getLeft())
                ->setParameter('maxRight', $parent->getRight());

            if (false === $options['include_root']) {
                $qb->andWhere('node.id != :nodeId')
                    ->setParameter('nodeId', $nodeId);
            }
        }

        if ($options['depth']) {
            $qb->andWhere('node.lvl <= :maxLevel')
                ->setParameter('maxLevel', $level + $options['depth']);
        }

        $children = $qb->getQuery()->getResult();

        $flatten = $options['flatten'];

        /** @noinspection PhpInternalEntityUsedInspection */
        return array_reduce($children, function (TreeBuilder $treeBuilder, $item) use ($nodeId, $flatten) {
            $branch = new Branch($item['title'], $item['id'], $item['url'], $item['lang'], $item['refId'], $item['refEntityName'], $item['internalName']);

            if ($branch->getNodeId() === $nodeId) {
                $item['parentId'] = null;
            } elseif ($flatten) {
                $item['parentId'] = $nodeId;
            }

            return $treeBuilder->add($item['parentId'], $branch);
        }, new TreeBuilder)->getRoot();

    }

}
