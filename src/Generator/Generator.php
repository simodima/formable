<?php

namespace Formable\Generator;

use Doctrine\Common\Annotations\Reader;
use Formable\Exception\FormGeneratorException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;

class Generator
{
    private $reader;
    private $factory;
    private $annotationClass = 'Formable\\Generator\\Annotation\\Formable';

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
        $recognizedFields = 0;
        $reflectionObject = new \ReflectionObject($originalObject);
        $formBuilder = $this->factory->createBuilder(
            'form',
            null,
            array_merge(['data_class' => $reflectionObject->name], $options)
        );

        foreach ($reflectionObject->getProperties() as $reflectionProperty) {
            $annotation = $this->reader->getPropertyAnnotation($reflectionProperty, $this->annotationClass);

            if (null !== $annotation) {
                ++$recognizedFields;
                $field = $annotation->getName();
                $formBuilder->add(
                    $field,
                    $annotation->getDataType(),
                    array_merge($annotation->getOptions(), ['property_path' => $field])
                );
            }
        }

        if ($recognizedFields == 0) {
            throw new FormGeneratorException;
        }

        return $formBuilder->getForm();
    }
}