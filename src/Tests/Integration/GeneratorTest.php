<?php

namespace Formable\Tests\Integration;

use Formable\Generator\Generator;
use Formable\Tests\Integration\DTOs\TestDoubleNestedDTO;
use Formable\Tests\Integration\DTOs\TestMoneyDTO;
use Formable\Tests\Integration\DTOs\TestNestedDTO;
use Formable\Tests\Integration\DTOs\TestNoPropertyNameDTO;
use Formable\Tests\Integration\DTOs\TestPreFilledDTO;
use Formable\Tests\Kernel\AppKernel;
use spec\Formable\Generator\TestDTO;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GeneratorTest extends WebTestCase
{
    /**
     * @var Generator
     */
    protected $generator;

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

    /**
     * @test
     */
    public function it_should_generate_a_form()
    {
        $form = $this->generator->generate(new TestDTO());
        $this->assertInstanceOf('\Symfony\Component\Form\Form', $form);
    }

    /**
     * @test
     */
    public function it_should_generate_a_form_with_no_property_name()
    {
        $form = $this->generator->generate(new TestNoPropertyNameDTO());
        $this->assertInstanceOf('\Symfony\Component\Form\Form', $form);
    }

    /**
     * @test
     */
    public function it_should_generate_a_nested_form()
    {
        $form = $this->generator->generate(new TestNestedDTO());
        $this->assertInstanceOf('\Symfony\Component\Form\Form', $form);

        $form->submit([
            'moneyDTO' => [
                'currency' => 'EUR',
                'value'    => 100
            ]
        ]);

        $this->assertInstanceOf('\Formable\Tests\Integration\DTOs\TestNestedDTO', $form->getData());
        $this->assertInstanceOf('\Formable\Tests\Integration\DTOs\TestMoneyDTO', $form->getData()->moneyDTO);
        $this->assertEquals(100, $form->getData()->moneyDTO->value);
    }

    /**
     * @test
     */
    public function it_should_generate_a_double_nested_form()
    {
        $form = $this->generator->generate(new TestDoubleNestedDTO());
        $this->assertInstanceOf('\Symfony\Component\Form\Form', $form);

        $form->submit([
            'nestedDTO' => [
                'moneyDTO' => [
                    'currency' => 'EUR',
                    'value'    => 100
                ]
            ]
        ]);

        $this->assertInstanceOf('\Formable\Tests\Integration\DTOs\TestDoubleNestedDTO', $form->getData());
        $this->assertInstanceOf('\Formable\Tests\Integration\DTOs\TestNestedDTO', $form->getData()->nestedDTO);
        $this->assertInstanceOf('\Formable\Tests\Integration\DTOs\TestMoneyDTO', $form->getData()->nestedDTO->moneyDTO);
        $this->assertEquals(100, $form->getData()->nestedDTO->moneyDTO->value);
    }


    /**
     * @test
     */
    public function it_should_not_reset_the_provided_data()
    {
        $preFilledDto = new TestPreFilledDTO();
        $preFilledDto->date = new \DateTime('2015-01-01');
        $money = new TestMoneyDTO();
        $money->currency = 'EUR';
        $preFilledDto->money = $money;

        $form = $this->generator->generate($preFilledDto);

        $form->submit([
            'money' => [
                'value'    => 100
            ]
        ], false);

        $this->assertInstanceOf('\DateTime', $form->getData()->date);
        $this->assertEquals(new \DateTime('2015-01-01'), $form->getData()->date);
        $this->assertInstanceOf('\Formable\Tests\Integration\DTOs\TestMoneyDTO', $form->getData()->money);
        $this->assertEquals(100, $form->getData()->money->value);
        $this->assertEquals('EUR', $form->getData()->money->currency);
    }
}