<?php

namespace ArsThanea\KunstmaanExtraBundle\Request\ParamConverter;

use ArsThanea\KunstmaanExtraBundle\ContentType\PageContentTypeInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageParamConverter extends DoctrineParamConverter
{
    /**
     * @var PageContentTypeInterface
     */
    private $contentType;

    public function __construct(ManagerRegistry $registry, PageContentTypeInterface $contentType)
    {
        parent::__construct($registry);
        $this->contentType = $contentType;
    }

    /**
     * @param Request        $request
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $fieldName = $this->getOptions($configuration)['type_field'];

        if ($fieldName) {
            $value = $request->attributes->get($fieldName);
            if (!$value) {
                throw new NotFoundHttpException(sprintf('Expected to have `%s` attribute present', $fieldName));
            }

            $className = $this->contentType->getContentTypeClass($value);

            if (null === $className) {
                throw new NotFoundHttpException(sprintf('Cannot guess page type from request: %s', $value));
            }

            $configuration->setClass($className);
        }

        return parent::apply($request, $configuration);
    }

    protected function getOptions(ParamConverter $configuration)
    {
        return array_replace([
            'type_field' => null,
        ], parent::getOptions($configuration));
    }

    /**
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function supports(ParamConverter $configuration)
    {
        if ("kunstmaan_extra.page" !== $configuration->getConverter()) {
            return false;
        }

        $options = $this->getOptions($configuration);

        if ($options['type_field']) {
            // we will guess the class name later in the `apply` method
            // for now assume it is supported

            return true;
        }

        $configuration->setClass(
            $this->contentType->getContentTypeClass($configuration->getName())
        );

        return parent::supports($configuration);
    }


}
