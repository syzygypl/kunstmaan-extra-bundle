<?php

namespace ArsThanea\KunstmaanExtraBundle\SiteTree;

use ArsThanea\KunstmaanExtraBundle\ContentCategory\Category;
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
     * @var CurrentLocaleInterface
     */
    private $currentLocale;

    /**
     * @var NodeVersion[][]  (ref_name, ref_id) -> node_version
     */
    private $nodeVersions;

    /**
     * @var Branch[]
     */
    private $branches;

    /**
     * @var array  node_version.id -> ref_id
     */
    private $refs = [];

    /**
     * @var array  node_id -> ref_id
     */
    private $nodeRefs = [];

    public function __construct(EntityManagerInterface $em, CurrentLocaleInterface $currentLocale)
    {
        $this->em = $em;
        $this->currentLocale = $currentLocale;
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
     * Warning: passing `0` as $lang will skip the language check (since 0 == "string")
     *
     * @param string $refName
     * @param string $lang
     *
     * @return Branch[]
     */
    public function getBranchesOfType($refName, $lang = null)
    {
        $this->initNodeVersions();

        $lang = null !== $lang ? $lang : $this->currentLocale->getCurrentLocale();

        return array_filter($this->branches, function (Branch $branch) use ($refName, $lang) {
            return $refName === $branch->getRefName() && $lang /* don’t use identity! */ == $branch->getLang();
        });
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

        $branch = array_reduce($this->branches, function (Branch $result = null, Branch $branch) use ($refName, $refId) {
            return ($refName === $branch->getRefName() && $refId === $branch->getRefId()) ? $branch : $result;
        });

        return $branch ? $branch->getNodeId() : null;
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
     * @param string  $lang
     *
     * @return int|null
     */
    public function getNodeRef($nodeId, $lang = null)
    {
        $this->initNodeVersions();

        $lang = $lang ?: $this->currentLocale->getCurrentLocale();

        return isset($this->nodeRefs[$lang][$nodeId]) ? $this->nodeRefs[$lang][$nodeId] : null;
    }

    /**
     * @param string $internalName
     * @param string $lang
     *
     * @return Branch|null
     */
    public function getBranchByInternalName($internalName, $lang = null)
    {
        $this->initNodeVersions();

        $lang = $lang ?: $this->currentLocale->getCurrentLocale();

        $matches = function (Branch $branch) use ($internalName, $lang) {
            return $internalName === $branch->getInternalName() && $lang === $branch->getLang();
        };

        return array_reduce($this->branches, function (Branch $result = null, Branch $branch) use ($matches) {
            return $matches($branch) ? $branch : $result;
        });
    }

    /**
     * @param Category $category
     * @param string   $lang
     *
     * @return Branch|null
     */
    public function getBranchByCategory(Category $category, $lang = null)
    {
        $this->initNodeVersions();

        $lang = $lang ?: $this->currentLocale->getCurrentLocale();

        $matches = function (Branch $branch) use ($category, $lang) {
            return $category->getNodeId() === $branch->getNodeId() && $lang === $branch->getLang();
        };

        return array_reduce($this->branches, function (Branch $result = null, Branch $branch) use ($matches) {
            return  $matches($branch) ? $branch : $result;
        });
    }

    private function initNodeVersions()
    {
        if (null !== $this->nodeVersions) {
            return;
        }

        $results = $this->em->getRepository('KunstmaanNodeBundle:NodeVersion')->createQueryBuilder('nv')
            ->select('nv.refEntityName', 'nv.refId', 'nv.id', 'n.internalName', 'nt.url', 'nt.lang', 'nt.title', 'n.id as nodeId')
            ->innerJoin('nv.nodeTranslation', 'nt', Join::WITH, 'nt.publicNodeVersion = nv.id')
            ->innerJoin('nt.node', 'n')
            ->where('nt.online = 1')
            ->andWhere('n.deleted = 0')
            ->orderBy('n.lvl, nt.weight')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);

        foreach ($results as $item) {

            $id = $item['id'];
            $branch = new Branch($item['title'], $item['nodeId'], $item['url'], $item['lang'], $item['refId'], $item['refEntityName'], $item['internalName']);

            $this->nodeVersions[$branch->getRefName()][$branch->getRefId()] = $id;

            $this->branches[] = $branch;

            $this->refs[$id] = $branch->getRefId();
            $this->nodeRefs[$item['lang']][$branch->getNodeId()] = $branch->getRefId();
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
