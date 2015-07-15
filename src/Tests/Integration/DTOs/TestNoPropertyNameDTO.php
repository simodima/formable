<?php
/**
 * Created by PhpStorm.
 * User: lucagiacalone
 * Date: 14/07/15
 * Time: 17:53
 */

namespace Formable\Tests\Integration\DTOs;

use Formable\Definition\Formable;

class TestNoPropertyNameDTO
{
    /**
     * @var
     *
     * @Formable(dataType="text", options={})
     */
    public $name;
}