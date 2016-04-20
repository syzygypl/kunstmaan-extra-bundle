<?php

namespace ArsThanea\KunstmaanExtraBundle\Page\PageController;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Use this controller for your page controllers to fool PHPStorm into thinking that it’s actually
 * handling the logic for your page template and then autocomplete available context variables
 */
abstract class AbstractNonRenderingController extends Controller implements PageControllerInterface
{

    /**
     * @param string $view #Template
     * @param array $parameters
     * @param Response $response
     * @return array
     */
    public function render($view, array $parameters = [], Response $response = null)
    {
        return $parameters;
    }
}
