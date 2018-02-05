<?php

namespace ArsThanea\KunstmaanExtraBundle\Form;

use ArsThanea\KunstmaanExtraBundle\ContentType\PageContentTypeInterface;
use ArsThanea\KunstmaanExtraBundle\SiteTree\CurrentLocaleInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Bridge\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;
use Symfony\Bridge\Doctrine\Form\Type\DoctrineType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NodeEntityType extends DoctrineType
{

    /**
     * @var PageContentTypeInterface
     */
    private $contentTypeService;

    /**
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;
    /**
     * @var CurrentLocaleInterface
     */
    private $currentLocale;

    public function __construct(
        ManagerRegistry $doctrine,
        PageContentTypeInterface $contentTypeService,
        DomainConfigurationInterface $domainConfiguration,
        CurrentLocaleInterface $currentLocale
    ) {
        parent::__construct($doctrine);
        $this->contentTypeService = $contentTypeService;
        $this->domainConfiguration = $domainConfiguration;
        $this->currentLocale = $currentLocale;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['advanced_select']) {
            $view->vars['attr'] += ['class' => 'js-advanced-select form-control advanced-select'];
        }
    }

    /**
     * Return the default loader object.
     *
     * @param ObjectManager $manager
     * @param QueryBuilder $queryBuilder
     * @param string $class
     *
     * @return EntityLoaderInterface
     */
    public function getLoader(ObjectManager $manager, $queryBuilder, $class)
    {
        $queryBuilder->innerJoin(NodeVersion::class, 'nv', Join::WITH, 'nv.refId = e.id and nv.refEntityName = :ref_name');

        $queryBuilder->innerJoin(NodeTranslation::class, 'nt', Join::WITH, 'nt.id = nv.nodeTranslation and nt.publicNodeVersion = nv.id');
        $queryBuilder->innerJoin(Node::class, 'n', Join::WITH, 'n.id = nt.node');

        $queryBuilder->andWhere($queryBuilder->expr()->eq("n.deleted", 0));
        $queryBuilder->andWhere($queryBuilder->expr()->eq('nt.online', 1));
        $queryBuilder->setParameter("ref_name", $class);

        if (null !== $queryBuilder->getParameter('left') && null !== $queryBuilder->getParameter('right')) {
            $queryBuilder->andWhere('n.lft >= :left')->andWhere('n.rgt <= :right');
        }

        if (null !== $queryBuilder->getParameter('lang')) {
            $queryBuilder->andWhere('nt.lang = :lang');
        }

        return new ORMQueryBuilderLoader(
            $queryBuilder,
            $manager,
            $class
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'advanced_select' => true,
            'root_node'       => function (Options $options) {
                return $this->domainConfiguration->getRootNode();
            },
            'lang'            => function (Options $options) {
                return $this->currentLocale->getCurrentLocale();
            },
            'class'           => function (Options $options) {
                return $this->contentTypeService->getContentTypeClass($options['page_name']);
            },
            'query_builder'   => function (Options $options) {
                /** @var QueryBuilder $qb */
                $qb = $options['em']->getRepository($options['class'])->createQueryBuilder('e');

                $node = $options['root_node'];
                if ($node instanceof Node) {
                    $qb->setParameters([
                        'left' => $node->getLeft(),
                        'right' => $node->getRight(),
                    ]);
                }

                $qb->setParameter('lang', $options['lang']);

                return $qb;
            },
            'placeholder'     => ' ',
            'required'        => false,
        ]);

        $resolver->setAllowedTypes('root_node', ['null', Node::class]);
        $resolver->setRequired('page_name');

    }

}
