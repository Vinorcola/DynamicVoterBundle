<?php

namespace Vinorcola\DynamicVoterBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Vinorcola\DynamicVoterBundle\Security\DynamicVoter;

class VinorcolaDynamicVoterExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $processedConfig = $this->processConfiguration($configuration, $configs);

        // Create the dynamic voter only if we have at least one rule.
        if (count($processedConfig['rules']) > 0) {
            $dynamicVoterDefinition = new Definition(DynamicVoter::class, [
                $processedConfig['rules'],
                $processedConfig['cache']
            ]);
            $dynamicVoterDefinition->setPublic(false);
            $dynamicVoterDefinition->addTag('security.voter');

            $container->setDefinition('vinorcola_dynamic_voter.dynamic_voter', $dynamicVoterDefinition);
        }
    }
}
