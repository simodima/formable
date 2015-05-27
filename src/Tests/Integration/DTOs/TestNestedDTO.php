<?php

namespace Formable\Tests\Integration\DTOs;

use Formable\Definition\Formable;

class TestNestedDTO
{
    /**
     * @var
     *
     * @Formable(name="moneyDTO", class="Formable\Tests\Integration\DTOs\TestMoneyDTO")
     */
    public $moneyDTO;
}
