<?php

namespace Formable\Tests\Kernel;

use Formable\Bundle\FormableBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // Dependencies
            new FrameworkBundle(),
            new FormableBundle()
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        // We don't need that Environment stuff, just one config

        $loader->load(__DIR__.'/config_'. $this->getEnvironment() .'.yml');
    }
} 