<?php


namespace ArsThanea\KunstmaanExtraBundle\Generator;


use Kunstmaan\GeneratorBundle\Generator\AdminListGenerator as BaseAdminListGenerator;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AdminListGenerator extends BaseAdminListGenerator
{
    private $skeletonDir;

    public function __construct($skeletonDir)
    {
        $this->skeletonDir = $skeletonDir;
        parent::__construct($skeletonDir);
    }

    public function generateController(Bundle $bundle, $entityName, $sortField)
    {
        $className = sprintf("%sAdminListController", $entityName);
        $dirPath = sprintf("%s/Controller/AdminList", $bundle->getPath());
        $classPath = sprintf("%s/%s.php", $dirPath, str_replace('\\', '/', $className));
        $extensions = 'csv';
        if (class_exists("\\Kunstmaan\\AdminListBundle\\Service\\ExportService")) {
            $extensions = implode('|', \Kunstmaan\AdminListBundle\Service\ExportService::getSupportedExtensions());
        }

        if (file_exists($classPath)) {
            throw new \RuntimeException(
                sprintf(
                    'Unable to generate the %s class as it already exists under the %s file',
                    $className,
                    $classPath
                )
            );
        }

        $this->setSkeletonDirs([$this->skeletonDir]);
        $this->renderFile(
            '/Controller/EntityAdminListController.php.twig',
            $classPath,
            array(
                'namespace' => $bundle->getNamespace(),
                'bundle' => $bundle,
                'entity_class' => $entityName,
                'export_extensions' => $extensions,
                'sortField' => $sortField
            )
        );
    }

    public function generateRouting(Bundle $bundle, $entityName)
    {
        $filename = sprintf('%s_%s_admin_list.yml', strtolower($bundle->getName()), strtolower($entityName));
        $filePath = sprintf('%s/Resources/config/routing/admin/%s', $bundle->getPath(), $filename);

        $this->setSkeletonDirs([$this->skeletonDir]);
        $this->renderFile(
            '/routing/routes.yml',
            $filePath,
            [
                'bundle' => $bundle,
                'entity_class' => $entityName,
            ]
        );
    }

}
