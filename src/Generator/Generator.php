<?php

namespace Formable\Generator;

use Doctrine\Common\Annotations\Reader;
use Formable\Definition\Formable;
use Formable\Exception\FormGeneratorException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;

class Generator
{
    const ANNOTATION_CLASS = 'Formable\\Definition\\Formable';
    private $reader;
    private $factory;

    /**
     * @param Reader $reader
     * @param FormFactory $factory
     */
    public function __construct(Reader $reader, FormFactory $factory)
    {
        $this->reader = $reader;
        $this->factory = $factory;
    }

    /**
     * @param  mixed $originalObject
     * @param  array $options
     *
     * @return Form
     *
     * @throws FormGeneratorException
     */
    public function generate($originalObject, $options = [])
    {
        $reflectionObject = new \ReflectionClass($originalObject);
        $baseBuilder = $this->factory->createBuilder(
            'form',
            $originalObject,
            array_merge(['data_class' => $reflectionObject->name], $options)
        );

        $formBuilder = $this->createFormBuilderForObject($reflectionObject, $baseBuilder);

        return $formBuilder->getForm();
    }

    /**
     * @param \ReflectionClass     $reflectionObject
     * @param FormBuilderInterface $formBuilder
     *
     * @return FormBuilderInterface
     *
     * @throws FormGeneratorException
     */
    private function createFormBuilderForObject(\ReflectionClass $reflectionObject, FormBuilderInterface $formBuilder)
    {
        $recognizedFields = 0;

        foreach ($reflectionObject->getProperties() as $reflectionProperty) {
            /** @var Formable $annotation */
            $annotation = $this->reader->getPropertyAnnotation($reflectionProperty, static::ANNOTATION_CLASS);

            $name = $annotation->getName() ?: $reflectionProperty->getName();

            if (null !== $annotation) {
                ++$recognizedFields;

                if ($class = $annotation->getClass()) {

                    $formBuilder->add(
                        $this->createFormBuilderForObject(
                            new \ReflectionClass($class),
                            $formBuilder->create($name, null, ['compound' => true, 'data_class' => $class])
                        )
                    );

                } else {
                    $formBuilder->add(
                        $name,
                        $annotation->getDataType(),
                        array_merge($annotation->getOptions(), ['property_path' => $annotation->getName()])
                    );
                }
            }
        }

        if ($recognizedFields == 0) {
            throw new FormGeneratorException;
        }

        return $formBuilder;
    }
}