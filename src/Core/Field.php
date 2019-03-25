<?php

namespace FocalStrategy\Actions\Core;

use Config;
use Core\ApplyScopes;
use FocalStrategy\Actions\Core\DebugException;
use FocalStrategy\Core\Renderable;
use FocalStrategy\Actions\Core\StaticCache;
use View;

class Field implements Renderable
{
    protected $render_type;

    protected $attributes = [];
    protected $field_name;
    protected $label;
    protected $type = 'text';
    protected $options = [];
    protected $required = false;

    protected static $cache;

    public function __construct(string $field_name)
    {
        $this->field_name = $field_name;
        $this->label = ucwords(str_replace(['_id','_'], ['',' '], $field_name));

        if (!self::$cache) {
            self::$cache = new StaticCache();
        }
    }

    public function renderType(ActionRenderType $render_type)
    {
        $this->render_type = $render_type;

        return $this;
    }

    public function label(string $label)
    {
        $this->label = $label;

        return $this;
    }

    public function type(string $type)
    {
        if (Config::get('app.debug') && !View::exists('actions::fields.'.$type)) {
            throw new DebugException('Field Type "'.$type.'" does not exist');
        }
        $this->type = $type;

        return $this;
    }


    /* For Dropdowns e.g. Branch::class, ['retail'] */
    public function source(string $cl, array $scopes = [])
    {
        // $cache_key = self::$cache->gen($cl, $scopes);

        // $as = new ApplyScopes();
        // $as->setModel($cl);
        // $query = $as->addScopesToBuilder($cl::query(), $scopes);

        // if (self::$cache->has($cache_key)) {
        //     return self::$cache->get($cache_key);
        // }

        $this->options = [];//$query->get()->lists('standardised_name', 'id');
        // self::$cache->put($cache_key, $this->options);

        return $this;
    }

    public function required()
    {
        $this->required = true;

        return $this;
    }

    public function options(array $options)
    {
        $this->options = $options;

        return $this;
    }

    public function attr(string $key, string $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function render()
    {
        return view('actions::fields.'.$this->type)
            ->with('field', $this);
    }

    public function getFieldName()
    {
        return $this->field_name;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getOptions()
    {
        if (!$this->required && $this->type != 'multi_select') {
            return [null => 'Please Select…']+$this->options;
        }

        return $this->options;
    }

    public function getAttributes()
    {
        $attributes = $this->attributes;
        if (isset($attributes['class'])) {
            $attributes['class'] = 'form-control '.$attributes['class'];
        }
        if (isset($attributes['label-class'])) {
            $attributes['class'] = $attributes['label-class'];
        } else {
            $attributes['class'] = 'form-control';
        }

        if ($this->required) {
            $attributes['required'] = 'required';
        }

        return $attributes;
    }
}
