<?php

namespace Formable\Tests\Integration\DTOs;

use Formable\Definition\Formable;

class TestMoneyDTO
{
    /**
     * @var String
     *
     * @Formable(name="currency", dataType="text")
     */
    public $currency;

    /**
     * @var Integer
     *
     * @Formable(name="value", dataType="integer")
     */
    public $value;
}
