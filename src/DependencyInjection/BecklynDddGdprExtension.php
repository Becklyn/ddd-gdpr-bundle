<?php declare(strict_types=1);

namespace Becklyn\Ddd\PersonalData\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2022-04-07
 */
class BecklynDddGdprExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container) : void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../resources/config')
        );
        $loader->load('services.yml');
    }

    public function prepend(ContainerBuilder $container) : void
    {
        $container->prependExtensionConfig('doctrine_migrations', [
            'migrations_paths' => [
                'Becklyn\\Ddd\\PersonalData\\Infrastructure\\DoctrineMigrations' => __DIR__ . '/../Infrastructure/DoctrineMigrations',
            ],
        ]);
    }
}
