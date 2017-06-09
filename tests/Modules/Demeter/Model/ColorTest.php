<?php

namespace Tests\Asylamba\Modules\Demeter\Model;

use Asylamba\Modules\Demeter\Model\Color;

class ColorTest extends \PHPUnit\Framework\TestCase
{
    public function testEntity()
    {


        $color =
            new Color();

    }

    public function testIncreaseCredit()
    {
      $color = new color();
      $color->credits=1000;
      $this->assertEquals(2234, $color->increaseCredit(1324));
    }

    public function testDecreaseCredit()
    {
      $color = new color();
      $color->credits=1000;
      $this->assertEquals(505, $color->decreaseCredit(495));
    }
}
