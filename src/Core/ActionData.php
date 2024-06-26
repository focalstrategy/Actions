<?php

namespace FocalStrategy\Actions\Core;

use FocalStrategy\Core\ValueInterface;
use FocalStrategy\ViewObjects\ViewObject;

class ActionData extends ViewObject
{
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if ($value instanceof ValueInterface) {
                $this->$key = $value->value();
            } else {
                $this->$key = $value;
            }
        }
    }
}
