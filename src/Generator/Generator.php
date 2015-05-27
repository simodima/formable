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
    private $reader;
    private $factory;
    private $annotationClass = 'Formable\\Definition\\Formable';

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
        $formBuilder = $this->createFormBuilderForObject($reflectionObject, $options);

        return $formBuilder->getForm();
    }

    /**
     * @param \ReflectionClass     $reflectionObject
     * @param array                $options
     * @param String               $name
     * @param mixed                $data
     * @param FormBuilderInterface $previousFormBuilder
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     *
     * @throws FormGeneratorException
     */
    private function createFormBuilderForObject(
        \ReflectionClass $reflectionObject,
        $options = [],
        $name = 'form',
        $data = null,
        FormBuilderInterface $previousFormBuilder = null
    )
    {
        $recognizedFields = 0;
        $data = is_null($data) ? $reflectionObject->newInstanceWithoutConstructor() : $data;
        $opt = array_merge(['data_class' => $reflectionObject->name], $options);

        if ($previousFormBuilder == null) {
            $formBuilder = $this->factory->createBuilder(
                $name,
                $data,
                $opt
            );
        } else {
            $formBuilder = $previousFormBuilder->create($name, null, $opt);
        }

        foreach ($reflectionObject->getProperties() as $reflectionProperty) {
            /** @var Formable $annotation */
            $annotation = $this->reader->getPropertyAnnotation($reflectionProperty, $this->annotationClass);

            if (null !== $annotation) {
                ++$recognizedFields;

                if ($class = $annotation->getClass()) {
                    $mappedObject = new \ReflectionClass($class);
                    $formBuilder->add(
                        $this->createFormBuilderForObject(
                            $mappedObject,
                            array_merge($annotation->getOptions(), ['compound' => true]),
                            $annotation->getName(),
                            $mappedObject->newInstanceWithoutConstructor(),
                            $formBuilder
                        )
                    );
                } else {
                    $formBuilder->add(
                        $annotation->getName(),
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