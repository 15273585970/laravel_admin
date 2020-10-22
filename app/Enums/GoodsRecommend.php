<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class GoodsRecommend extends Enum
{
    const 不推荐 =   1000;
    const 推荐   =   1002;
}
