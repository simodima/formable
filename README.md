# Formable Bundle

# Why?

Because the cleanest way to transfer data from a web request to the domain is by using DTOs.
 
# How?

This Bundle allows you to describe DTOs and define its validation rules. 

## Example

Create your DTO


```

use AppBundle\Controller\FormFieldDefinition;
use Symfony\Component\Validator\Constraints as Assert;

class PublishPostCommand
{
    /**
     * @FormFieldDefinition(name="title", dataType="text")
     *
     * @Assert\Length(max=250)
     */
    public $title;

    /**
     * @FormFieldDefinition(name="content", dataType="text")
     */
    public $content;

    /**
     * @FormFieldDefinition(name="tags", dataType="collection", options={
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
     * @FormFieldDefinition(name="date", dataType="date", options={
     *   "widget"="single_text",
     *   "format"="yyyy-M-d"
     * })
     */
    public $date;
}

```

## Run tests

`vendor/bin/phpspec run`