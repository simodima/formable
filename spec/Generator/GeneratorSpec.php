<?php

namespace spec\Formable\Generator;

use Doctrine\Common\Annotations\Reader;
use Formable\Definition\Formable;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;

class GeneratorSpec extends ObjectBehavior
{
    public function it_is_initializable(Reader $reader, FormFactory $factory)
    {
        $this->beConstructedWith($reader, $factory);
        $this->shouldHaveType('Formable\Generator\Generator');
    }


    public function it_should_not_create_a_form_with_zero_fields(
        Reader $reader,
        FormFactory $factory,
        FormBuilderInterface $formBuilder
    )
    {
        $this->beConstructedWith($reader, $factory);
        $factory->createBuilder(
            'form',
            null,
            [
                'data_class'      => 'stdClass'
            ]
        )->willReturn($formBuilder);

        $testDTO = new \stdClass();
        $testDTO->name = 'name';
        $testDTO->age  = 18;

        $this->shouldThrow('Formable\Exception\FormGeneratorException')->duringGenerate($testDTO);
    }


    public function it_should_create_a_form(
        Reader $reader,
        FormFactory $factory,
        FormBuilderInterface $formBuilder,
        Formable $formable,
        Form $form
    )
    {
        $this->beConstructedWith($reader, $factory);
        $factory->createBuilder(
            'form',
            null,
            [
                'csrf_protection' => false,
                'data_class'      => 'spec\Formable\Generator\TestDTO'
            ]
        )->willReturn($formBuilder);

        $formBuilder->add('name', 'text', ['property_path' => 'name'])->willReturn($formBuilder);
        $formBuilder->getForm()->willReturn($form);

        $formable->getName()->willReturn('name');
        $formable->getDataType()->willReturn('text');
        $formable->getOptions()->willReturn([]);

        $property = new \ReflectionProperty('spec\Formable\Generator\TestDTO', 'name');
        $reader->getPropertyAnnotation($property, 'Formable\\Definition\\Formable')->willReturn(
            $formable
        );

        $this->generate(new TestDTO(), ['csrf_protection' => false])
            ->shouldReturnAnInstanceOf('\Symfony\Component\Form\Form')
        ;
    }
}
