<?php

namespace FocalStrategy\Actions\Core;

use MyCLabs\Enum\Enum;

/**
 * @method static ActionRenderType INLINE()
 * @method static ActionRenderType FORM()
 * @method static ActionRenderType BIGBOX()
 */
class ActionRenderType extends Enum
{
    const INLINE = 'inline';
    const FORM = 'form';
    const BIGBOX = 'bigbox';
}
