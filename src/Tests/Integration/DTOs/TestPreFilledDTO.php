<?php

namespace Formable\Tests\Integration\DTOs;

use Formable\Definition\Formable;

class TestPreFilledDTO
{
    /**
     * @var TestMoneyDTO
     *
     * @Formable(name="money", class="Formable\Tests\Integration\DTOs\TestMoneyDTO")
     */
    public $money;

    /**
     * @var \DateTime
     *
     * @Formable(name="date", dataType="datetime")
     */
    public $date;
}
