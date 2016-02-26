<?php

namespace ArsThanea\KunstmaanExtraBundle\SiteTree;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

class PublicNodeVersions
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var NodeVersion[][]
     */
    private $nodeVersions;

    /**
     * @var array  node.internal_name -> branch
     */
    private $internalNodes = [];

    /**
     * @var array  node_version.id -> ref_id
     */
    private $refs = [];

    /**
     * @var array  (ref_name, ref_id) -> node_id
     */
    private $nodeIds;

    /**
     * @var array  node_id -> ref_id
     */
    private $nodeRefs = [];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param HasNodeInterface $page
     *
     * @return NodeVersion|null
     */
    public function getNodeVersionFor(HasNodeInterface $page)
    {
        $this->initNodeVersions();

        $refName = ClassLookup::getClass($page);
        $id = $page->getId();

        if (isset($this->nodeVersions[$refName][$id])) {
            return $this->nodeVersions[$refName][$id] = $this->ensureEntity($this->nodeVersions[$refName][$id]);
        }

        // fallback:
        return $this->em->getRepository('KunstmaanNodeBundle:NodeVersion')->getNodeVersionFor($page);
    }

    /**
     * @param string $refName
     *
     * @return NodeVersion[]
     */
    public function getNodesOfType($refName)
    {
        $this->initNodeVersions();

        if (isset($this->nodeVersions[$refName])) {
            return $this->nodeVersions[$refName] = array_map([$this, 'ensureEntity'], $this->nodeVersions[$refName]);
        }

        return [];
    }

    /**
     * @param HasNodeInterface $page
     *
     * @return \Kunstmaan\NodeBundle\Entity\NodeTranslation|null
     */
    public function getNodeTranslationFor(HasNodeInterface $page)
    {
        $version = $this->getNodeVersionFor($page);

        return $version ? $version->getNodeTranslation() : null;
    }

    /**
     * @param HasNodeInterface $page
     *
     * @return \Kunstmaan\NodeBundle\Entity\Node|null
     */
    public function getNodeFor(HasNodeInterface $page)
    {
        $translation = $this->getNodeTranslationFor($page);

        return $translation ? $translation->getNode() : null;
    }

    /**
     * @param HasNodeInterface $page
     *
     * @return integer
     */
    public function getNodeIdFor(HasNodeInterface $page)
    {
        $this->initNodeVersions();

        $refName = ClassLookup::getClass($page);
        $refId = $page->getId();

        return isset($this->nodeIds[$refName][$refId]) ? $this->nodeIds[$refName][$refId] : null;
    }

    /**
     * Get refId by refName and nodeId
     *
     * @param string $refName
     * @param int    $nodeId
     *
     * @return integer|false
     */
    public function getCategoryRef($refName, $nodeId)
    {
        $this->initNodeVersions();

        return array_search($nodeId, $this->nodeIds[$refName]);
    }

    /**
     * Gets Page Id for given NodeVersion Id
     *
     * @param integer$nodeVersionId
     *
     * @return integer|null
     */
    public function getRef($nodeVersionId)
    {
        $this->initNodeVersions();

        return isset($this->refs[$nodeVersionId]) ? $this->refs[$nodeVersionId] : null;
    }

    /**
     * Gets Page Id for given Node Id
     *
     * @param integer $nodeId
     *
     * @return integer|null
     */
    public function getNodeRef($nodeId)
    {
        $this->initNodeVersions();

        return isset($this->nodeRefs[$nodeId]) ? $this->nodeRefs[$nodeId] : null;
    }

    /**
     * @param string $internalName
     *
     * @return Branch|null
     */
    public function getBranchByInternalName($internalName)
    {
        $this->initNodeVersions();

        return isset($this->internalNodes[$internalName]) ? $this->internalNodes[$internalName] : null;

    }

    private function initNodeVersions()
    {
        if (null !== $this->nodeVersions) {
            return;
        }

        $results = $this->em->getRepository('KunstmaanNodeBundle:NodeVersion')->createQueryBuilder('nv')
            ->select('nv.refEntityName', 'nv.refId', 'nv.id', 'n.internalName', 'nt.url', 'nt.title', 'n.id as nodeId')
            ->innerJoin('nv.nodeTranslation', 'nt', Join::WITH, 'nt.publicNodeVersion = nv.id')
            ->innerJoin('nt.node', 'n')
            ->where('nt.online = 1')
            ->andWhere('n.deleted = 0')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);

        foreach ($results as $item) {

            $id = $item['id'];
            $branch = new Branch($item['title'], $item['nodeId'], $item['url'], $item['refId'], $item['refEntityName'], $item['internalName']);

            $this->nodeVersions[$branch->getRefName()][$branch->getRefId()] = $id;
            $this->nodeIds[$branch->getRefName()][$branch->getRefId()] = $branch->getNodeId();
            $this->internalNodes[$branch->getInternalName()] = $branch;
            $this->refs[$id] = $branch->getRefId();
            $this->nodeRefs[$branch->getNodeId()] = $branch->getRefId();
        }
    }

    /**
     * @param array|NodeVersion $data
     *
     * @return NodeVersion
     */
    private function ensureEntity($data)
    {
        return is_scalar($data) ? $this->em->getRepository('KunstmaanNodeBundle:NodeVersion')->find($data) : $data;
    }

}
