# Formable Bundle

# Why?

Because the cleanest way to transfer data from a web request to the domain is by using DTOs.
 
# How?

This Bundle allows you to describe DTOs and define its validation rules. 

## Installation
 
 ...
 
## Example

### The Data Transfer Object

```php

use Formable\Annotation\Formable;
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

## Run tests

`vendor/bin/phpspec run`