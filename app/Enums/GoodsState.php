<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class GoodsState extends Enum
{
    const 上架 =   1001;
    const 下架 =   1002;
}
