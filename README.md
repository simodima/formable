# Formable Symfony Bundle

[![Latest Stable Version](https://poser.pugx.org/trt/formable/v/stable)](https://packagist.org/packages/trt/formable) [![Total Downloads](https://poser.pugx.org/trt/formable/downloads)](https://packagist.org/packages/trt/formable) [![Latest Unstable Version](https://poser.pugx.org/trt/formable/v/unstable)](https://packagist.org/packages/trt/formable) [![License](https://poser.pugx.org/trt/formable/license)](https://packagist.org/packages/trt/formable)

# Why?

Because the cleanest way to transfer data from a web request to the domain is by using DTOs. 
For simple DTOs Symfony forces you to create 2 classes, the `FormType` class and the `SomethingDTO` class.

# How?

This Bundle allows you to describe DTOs by the annotation `@Formable()`. Let's see an example. 

## Example

### The Data Transfer Object

```php

use Formable\Definition\Formable;
use Symfony\Component\Validator\Constraints as Assert;

class PublishPostCommand
{
    /**
     * @Formable(name="title", dataType="text")
     *
     * @Assert\Length(max=250)
     */
    public $title;

    /**
     * @Formable(name="content", dataType="text")
     */
    public $content;

    /**
     * @Formable(name="tags", dataType="collection", options={
     *   "type"="text",
     *   "allow_add"=true
     * })
     *
     * @Assert\Count(
     *   min = "2"
     * )
     *
     */
    public $tags;

    /**
     * @Formable(name="date", dataType="date", options={
     *   "widget"="single_text",
     *   "format"="yyyy-M-d"
     * })
     */
    public $date;
}

```

### The Controller

```php

public function publishAction(Request $request)
{
    $form = $this->get('trt.formable')->generate(new PublishPostCommand);
    $form->submit($request->request->all());
    
    if ($form->isValid()) {
        ...
    }
}
```
## The annotation in depth

The `@Formable()` annotation follows the `Symfony\Component\Form\FormBuilderInterface` interface.


**ARGUMENTS**: 

- **name**: [_string_] the field name
- **dataType**: [_string_] the [FormType](http://symfony.com/doc/current/reference/forms/types.html)
- **options**: [_array_] the FormType options

```php
    /**
     * @Formable(name="date", dataType="date", options={
     *   "format"= IntlDateFormatter::MEDIUM,
     *   "days" = {1,2,3,4}
     * })
     */
    public $date;
```

## Installation
 
`composer require trt/formable`

```php
// Register the Bundle

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            ...
            new \Formable\Bundle\FormableBundle(),
        );

        return $bundles;
    }

}
```

## Run tests

`vendor/bin/phpspec run`
