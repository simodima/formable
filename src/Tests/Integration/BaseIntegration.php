<?php
namespace Formable\Tests\Integration;

use Formable\Tests\Kernel\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseIntegration extends WebTestCase
{

    protected static function createKernel(array $options = array())
    {
        return new AppKernel(
            isset($options['environment']) ? $options['environment'] : 'test',
            isset($options['debug']) ? $options['debug'] : true
        );
    }

    public function setUp()
    {
        static::bootKernel();
        $container = static::$kernel->getContainer();
        $this->generator = $container->get('pugx.formable');
    }

    protected function tearDown()
    {
        static::$kernel->shutdown();
    }
}