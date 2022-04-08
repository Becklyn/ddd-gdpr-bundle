<?php declare(strict_types=1);

namespace Becklyn\Ddd\PersonalData;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2022-04-07
 */
class BecklynDddGdprBundle extends Bundle
{
    public function build(ContainerBuilder $container) : void
    {
        parent::build($container);

        $mappings = [
            \realpath(__DIR__ . '/../resources/config/doctrine-mapping') => 'Becklyn\\Ddd\\PersonalData\\Infrastructure\\Domain\\Doctrine',
        ];

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings));
    }
}
