<?php

namespace Kachkaev\DropboxBackupBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kachkaev_dropbox_backup');
        
        $rootNode
        ->children()
	        ->arrayNode('database')
    		    ->children()
        			->scalarNode('driver')->defaultValue('pdo_mysql')->info('Only pdo_mysql is supported at the moment')->end()
        			->scalarNode('host')->defaultValue('localhost')->end()
        			->scalarNode('port')->defaultValue(null)->end()
        			->scalarNode('dbname')->isRequired()->end()
        			->scalarNode('user')->isRequired()->end()
        			->scalarNode('password')->isRequired()->end()
        			->scalarNode('output_directory')->defaultValue('')->info('Directory relative to Dropbox root')->end()
        			->scalarNode('output_fileprefix')->defaultValue('')->info('Output filename consists of prefix and timestamp')->end()
        			->booleanNode('output_compression')->defaultFalse()->info('Set to true to compress .sql to .tgz')->end()
        			->end()
        	->end()
	        ->arrayNode('dropbox')
	            ->info('Dropbox account credentials (use parameters in config.yml and store real values in prameters.yml)')
    		    ->children()
        			->scalarNode('user')->isRequired()->end()
        			->scalarNode('password')->isRequired()->end()
		        ->end()
	        ->end()
        ->end();

        return $treeBuilder;
    }
}
