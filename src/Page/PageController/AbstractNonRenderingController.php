<?php

namespace ArsThanea\KunstmaanExtraBundle\Page\PageController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Use this controller for your page controllers to fool PHPStorm into thinking that it’s actually
 * handling the logic for your page template and then autocomplete available context variables
 */
abstract class AbstractNonRenderingController extends AbstractController implements PageControllerInterface
{

    /**
     * @param string $view #Template
     * @param array $parameters
     * @param Response|null $response
     * @return array
     */
    public function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        return $parameters;
    }
}
