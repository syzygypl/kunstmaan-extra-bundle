<?php

namespace ArsThanea\KunstmaanExtraBundle\Form;

use ArsThanea\KunstmaanExtraBundle\ContentType\PageContentTypeInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
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
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var PageContentTypeInterface
     */
    private $contentTypeService;

    public function __construct(ManagerRegistry $doctrine, PageContentTypeInterface $contentTypeService)
    {
        parent::__construct($doctrine);
        $this->doctrine = $doctrine;
        $this->contentTypeService = $contentTypeService;
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
            'class'           => function (Options $options) {
                return $this->contentTypeService->getContentTypeClass($options['page_name']);
            },
            'empty_value'     => ' ',
            'required'        => false,
        ]);

        $resolver->isRequired('page_name');
    }

}