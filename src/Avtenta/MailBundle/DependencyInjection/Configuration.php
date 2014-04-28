<?php

namespace Avtenta\MailBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('avtenta_mail');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
	
	$rootNode
	    ->children()
		->enumNode('service')
		    ->defaultValue('imap')
		    ->treatNullLike('imap')
		    ->values(array('imap', 'pop', 'nntp'))
		    ->info('protocol used for the connection, either imap, pop or nntp')
		->end()
		->scalarNode('host')
		    ->isRequired()
		    ->cannotBeEmpty()
		->end()
		->integerNode('port')
		    ->defaultValue(143)
		    ->treatNullLike(143)
		    ->min(1)->max(65535)
		->end()
		->scalarNode('username')
		    ->cannotBeEmpty()
		->end()
		->scalarNode('password')
		    ->cannotBeEmpty()
		->end()
	    ->end();

        return $treeBuilder;
    }
}
