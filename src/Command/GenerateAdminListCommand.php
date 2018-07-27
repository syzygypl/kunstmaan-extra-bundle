<?php

namespace ArsThanea\KunstmaanExtraBundle\Command;

use ArsThanea\KunstmaanExtraBundle\Generator\AdminListGenerator;
use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCommand;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Generates a KunstmaanAdminList
 */
class GenerateAdminListCommand extends GenerateDoctrineCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputOption(
                        'entity',
                        '',
                        InputOption::VALUE_REQUIRED,
                        'The entity class name to create an admin list for (shortcut notation)'
                    ),
                    new InputOption(
                        'sortfield',
                        '',
                        InputOption::VALUE_OPTIONAL,
                        'The name of the sort field if entity needs to be sortable'
                    ),
                )
            )
            ->setDescription('Generates a KunstmaanAdminList')
            ->setHelp(
                <<<EOT
                The <info>szg:generate:adminlist</info> command generates an AdminList for a Doctrine ORM entity.

<info>php bin/console szg:generate:adminlist Bundle:Entity</info>
EOT
            )
            ->setName('szg:generate:adminlist');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @throws \RuntimeException
     * @throws \Doctrine\ORM\ORMException
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();

        GeneratorUtils::ensureOptionsProvided($input, array('entity'));

        $entity = Validators::validateEntityName($input->getOption('entity'));
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        $entityClass = $this->getContainer()->get('doctrine')->getAliasNamespace($bundle) . '\\' . $entity;
        $metadata = $this->getEntityMetadata($entityClass);
        /** @var Bundle $bundle */
        $bundle = $this->getContainer()->get('kernel')->getBundle($bundle);

        $questionHelper->writeSection($output, 'AdminList Generation');

        /** @var AdminListGenerator $generator */
        $generator = $this->getGenerator($this->getApplication()->getKernel()->getBundle("KunstmaanGeneratorBundle"));
        $generator->setQuestion($questionHelper);
        $generator->generate($bundle, $entityClass, $metadata[0], $output, $input->getOption('sortfield'));

        $parts = explode('\\', $entity);
        $entityClass = array_pop($parts);

        $generator->generateRouting($bundle, $entityClass);
    }

    /**
     * Interacts with the user.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Welcome to the Kunstmaan admin list generator');

        // entity
        $entity = null;
        try {
            $entity = $input->getOption('entity') ? Validators::validateEntityName($input->getOption('entity')) : null;
        } catch (\Exception $error) {
            $output->writeln(
                $questionHelper->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error')
            );
        }

        if (is_null($entity)) {
            $output->writeln(
                array(
                    '',
                    'This command helps you to generate an admin list for your entity.',
                    '',
                    'You must use the shortcut notation like <comment>AcmeBlogBundle:Post</comment>.',
                    '',
                )
            );

            $question = new Question($questionHelper->getQuestion('The entity shortcut name', $entity), $entity);
            $question->setValidator(array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateEntityName'));
            $entity = $questionHelper->ask($input, $output, $question);
            $input->setOption('entity', $entity);

            $question = new Question($questionHelper->getQuestion('The name of the sort field if entity needs to be sortable',
                false, '?'), false);
            $sortfield = $questionHelper->ask($input, $output, $question);
            $input->setOption('sortfield', $sortfield);
        }
    }

    /**
     * @return AdminListGenerator
     */
    protected function createGenerator()
    {
        return new AdminListGenerator(__DIR__ . '/../Resources/SensioGeneratorBundle/skeleton/adminlist');
    }
}
