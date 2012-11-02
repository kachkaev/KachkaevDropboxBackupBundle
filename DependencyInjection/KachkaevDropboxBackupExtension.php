<?php

namespace Kachkaev\DropboxBackupBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KachkaevDropboxBackupExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('config.yml');

        $container->setParameter('kachkaev_dropbox_backup.database_driver', $config['database']['driver']);
        $container->setParameter('kachkaev_dropbox_backup.database_host', $config['database']['host']);
        $container->setParameter('kachkaev_dropbox_backup.database_port', $config['database']['port']);
        $container->setParameter('kachkaev_dropbox_backup.database_dbname', $config['database']['dbname']);
        $container->setParameter('kachkaev_dropbox_backup.database_user', $config['database']['user']);
        $container->setParameter('kachkaev_dropbox_backup.database_password', $config['database']['password']);
        $container->setParameter('kachkaev_dropbox_backup.database_output_directory', $config['database']['output_directory']);
        $container->setParameter('kachkaev_dropbox_backup.database_output_fileprefix', $config['database']['output_fileprefix']);
        $container->setParameter('kachkaev_dropbox_backup.database_output_compression', $config['database']['output_compression']);

        $container->setParameter('kachkaev_dropbox_backup.dropbox_user', $config['dropbox']['user']);
        $container->setParameter('kachkaev_dropbox_backup.dropbox_password', $config['dropbox']['password']);
    }
}
