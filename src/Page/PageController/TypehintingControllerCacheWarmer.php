<?php

namespace ArsThanea\KunstmaanExtraBundle\Page\PageController;

use ArsThanea\KunstmaanExtraBundle\ContentType\ContentTypeService;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;
use Symfony\Component\HttpKernel\KernelInterface;

class TypehintingControllerCacheWarmer extends CacheWarmer
{
    private $kernel;
    private $path;
    private $contentType;

    public function __construct(
        KernelInterface    $kernel,
        ContentTypeService $contentType,
                           $path
    )
    {
        $this->kernel = $kernel;
        $this->contentType = $contentType;
        $this->path = $path;
    }


    /**
     * Checks whether this warmer is optional or not.
     *
     * Optional warmers can be ignored on certain conditions.
     *
     * A warmer should return true if the cache can be
     * generated incrementally and on-demand.
     *
     * @return bool true if the warmer is optional, false otherwise
     */
    public function isOptional(): bool
    {
        return true;
    }

    /**
     * Warms up the cache.
     *
     * @param string $cacheDir The cache directory
     */
    public function warmUp($cacheDir)
    {
        $pages = $this->contentType->getAllContentTypeClasses();
        $pageparts = $this->kernel->getContainer()->getParameter('kunstmaan_page_part.page_parts_presets');

        $classes = array_unique(array_filter(iterator_to_array($this->getClasses($pages, $pageparts))));

        $controllerName = basename($this->path, '.php');

        $code = $this->generateCode($classes, $controllerName);

        file_put_contents($this->path, $code);
    }

    /**
     * @throws \ReflectionException
     */
    private function generateCode($classes, $controllerName)
    {
        $methods = [];
        $comments = [];

        foreach($classes as $item) {
            if (false === class_exists($item)) {
                $comments[] = sprintf('Class %s does not exist', $item);
                continue;
            }

            $reflection = new \ReflectionClass($item);
            if ($reflection->getConstructor() && 0 !== $reflection->getConstructor()->getNumberOfRequiredParameters()) {
                $comments[] = sprintf('Class %s has a required constructor argument', $item);
                continue;
            }

            if (false === $reflection->hasMethod('getDefaultView')) {
                $comments[] = sprintf('Class %s has no default view', $item);
                continue;
            }

            list ($name) = array_reverse(explode("\\", $item));
            $template = $reflection->newInstance()->getDefaultView();

            $methods[] = [
                'name' => lcfirst($name),
                'class' => $item,
                'template' => $template,
            ];
        }

        $code = '<?php' . "\n\n"
            . '// THIS FILE IS GENERATED DURING THE CACHE WARMUP, DO NOT MODIFY' . "\n"
            . 'class %s extends \Symfony\Bundle\FrameworkBundle\Controller\Controller'. "\n" . '{%s}';

        $code = sprintf($code, $controllerName, "\n\n\t" .
            ($comments ? implode("\n\t", $comments) . "\n\n\t" : '') .
            implode("\n\t", array_map(function ($item) {

                $key = false === strpos($item['class'], "PagePart") ? 'page' : 'resource';

                return sprintf('public function %sAction()' . "\n\t" . '{' . "\n\t\t"
                    . 'return $this->render("%s", [' . "\n\t\t\t"
                        . '"%s" => new \%s,' . "\n\t\t"
                    . ']);' . "\n\t" . "}" . "\n",

                    $item['name'], $item['template'], $key, $item['class']
                );

            }, $methods)) . "\n"
        );

        $code = str_replace("\t", "    ", $code);

        return $code;

    }

    private function getClasses($pages, $pageparts): \Generator
    {
        foreach ($pages as $item) {
            yield $item;
        }

        foreach ($pageparts as $item) {
            foreach ($item['types'] as $type) {
                if ($type['class']) {
                    yield $type['class'];
                }
            }
        }
    }
}
