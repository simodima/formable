<?php
/**
 * Created by PhpStorm.
 * User: toretto
 * Date: 25/05/15
 * Time: 16:44
 */

namespace Formable\Exception;


use Exception;

class FormGeneratorException extends \Exception
{
    public function __construct()
    {
        parent::__construct(
            "A form must have at least one field. 0 fields given."
        );
    }

}