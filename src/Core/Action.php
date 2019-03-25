<?php

namespace FocalStrategy\Actions\Core;

use FocalStrategy\Actions\Core\ActionData;
use App\User;
use FocalStrategy\Core\Buttons\Button;
use FocalStrategy\Core\ValueInterface;
use FocalStrategy\Actions\Core\ActionException;
use FocalStrategy\Core\BaseBtnType;
use FocalStrategy\Actions\Core\Field;
use FocalStrategy\Core\ReceivesData;
use FocalStrategy\Core\Renderable;
use Input;
use Redirect;
use Validator;
use View;

abstract class Action implements Renderable
{
    const INLINE_MAX_FIELDS = 1;

    private $render_type;
    private $render_type_override;
    private $method = 'POST';
    private $additional_data_url;

    private $defaults = [];
    private $required_defaults = [];
    private $fields = [];
    private $buttons = [];

    private $hold_default_data = [];
    private $default_data = [];

    // While processing
    private $data = [];
    protected $errors;

    public function __construct()
    {
        $cl = static::class;
        $cl = explode('\\', $cl);
        $this->action = route('actions.save', ['action_name' => snake_case(last($cl))]);
    }

    public function getTitle() : string
    {
        $cl = static::class;
        $cl = explode('\\', $cl);
        $cl = ucwords(str_replace('_', ' ', snake_case(last($cl))));

        return str_replace(' Action', '', $cl);
    }

    public function setRenderType(ActionRenderType $render_type)
    {
        $this->render_type = $render_type;
    }

    public function setAdditionalDataUrl(string $additional_data_url)
    {
        $this->additional_data_url = $additional_data_url;
    }

    public function getAdditionalDataUrl()
    {
        $url = $this->additional_data_url;
        foreach ($this->default_data as $key => $value) {
            $url = str_replace('+'.$key.'+', $value, $url);
        }
        return $url;
    }

    public function setRenderTypeOverride(ActionRenderType $render_type)
    {
        $this->render_type_override = $render_type;
    }

    public function setBigBoxButtonClass(string $button_class) : Action
    {
        $this->button_class = $button_class;
        return $this;
    }

    public function setBigBoxButtonText(string $button_text) : Action
    {
        $this->button_text = $button_text;
        return $this;
    }

    public function addDefaultData(array $new_data) : Action
    {
        $this->hold_default_data = $new_data;
        return $this;
    }

    // only used in debug
    public function hasAllDefaults()
    {
        $has_all = true;
        foreach ($this->required_defaults as $d) {
            if (!isset($this->default_data[$d])) {
                $has_all = false;
                break;
            }
        }
        return $has_all;
    }

    public function getDefaultFields() : array
    {
        return $this->defaults;
    }

    public function getRequiredDefaultFields() : array
    {
        return $this->required_defaults;
    }

    public function getDefaultData()
    {
        return $this->default_data;
    }

    public function render()
    {
        $this->build();
        $this->setDefaultData($this->hold_default_data);
        $this->replaceTempVars();

        $action_data = new ActionData($this->getDefaultData());

        if ($this->render_type_override != null && !Input::ajax()) {
            $this->render_type = $this->render_type_override;
        } elseif ($this->render_type != ActionRenderType::FORM()
          && $this->getCountFields() > self::INLINE_MAX_FIELDS) {
            $this->render_type = ActionRenderType::BIGBOX();
        }

        if ($this->render_type == null) {
            $this->render_type = ActionRenderType::INLINE();
        }

        if ($this->render_type == ActionRenderType::INLINE()) {
            return view('actions::wrappers.inline')
            ->with('action', $this)
            ->with('action_data', $action_data);
        } elseif ($this->render_type == ActionRenderType::FORM()) {
            return view('actions::wrappers.form')
            ->with('action', $this)
            ->with('action_data', $action_data);
        } elseif ($this->render_type == ActionRenderType::BIGBOX()) {
            return view('actions::wrappers.bigbox')
            ->with('action', $this)
            ->with('action_data', $action_data);
        } else {
            throw new ActionException('ActionRenderType not recognised');
        }
    }

    public function process(User $user, array $input = []) : ActionResponse
    {
        $validator = Validator::make($input, $this->validate());

        // Validate
        if ($validator->fails()) {
            return ActionResponse::make()
            ->error('There were validation errors')
            ->withErrors($validator)
            ->withInput();
        }
        // Attach Data
        $this->data = $input;

        // Authorise
        if (!$this->authorise($user)) {
            App::abort(403);
        }

        // Handle
        return $this->handle();
    }

    public function isForm() : bool
    {
        if ($this->method == 'GET' && count($this->fields == 0)) {
            return false;
        }

        return true;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getButtons()
    {
        return $this->buttons;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getFormUrl($bigbox = false)
    {
        $cl = static::class;
        $cl = explode('\\', $cl);
        $name = snake_case(last($cl));

        if ($bigbox) {
            return route('actions.bigbox', ['action_name' => $name] + $this->default_data);
        } else {
            return route('actions.show', ['action_name' => $name] + $this->default_data);
        }
    }

    public function getBigBoxButtonText()
    {
        if ($this->button_text) {
            return $this->button_text;
        }

        return $this->getTitle();
    }

    public function getButtonClass()
    {
        if ($this->button_class) {
            return $this->button_class;
        }

        return 'btn-primary';
    }

    public function getFormAttributes()
    {
        $attr = [];
        foreach ($this->fields as $key => $value) {
            if ($value->getType() == 'file') {
                $attr['files'] = true;
            }
        }
        return $attr;
    }

    public function getBoxClasses() : string
    {
        $max_inputs = 2;
        if ($this->getCountFields() <= $max_inputs) {
            return 'col-sm-12 bigbox-sm big_box_custom_field_count_' . $this->getCountFields();
        }
        return 'col-sm-6';
    }

    public function __get($method)
    {
        if (!method_exists($this, $method)) {
            if (isset($this->data[$method])) {
                return $this->data[$method];
            } else {
                return null;
            }
        }
    }

    protected function method($method)
    {
        $this->method = $method;
    }

    protected function defaults(array $defaults)
    {
        $this->defaults = array_merge($this->defaults, $defaults);
    }

    protected function default(string $name, bool $required = true)
    {
        $this->defaults[] = $name;
        if ($required) {
            $this->required_defaults[] = $name;
        }
    }

    protected function addField(string $field_name)
    {
        $field = new Field($field_name);
        $this->fields[$field_name] = $field;

        return $field;
    }

    protected function addButton(string $text, BaseBtnType $btn_type, array $html_attributes = [])
    {
        $button = new Button($text, null, $btn_type);
        $button->mergeAttributes(array_merge(['class' => 'bigbox-hide-button'], $html_attributes));
        $this->buttons[] = $button;

        return $button;
    }

    abstract protected function build();
    abstract protected function validate() : array;
    abstract public function authorise(User $user) : bool;
    abstract protected function handle() : ActionResponse;

    protected function setDefaultData(array $data)
    {
        $this->default_data = $this->extractDefaults($data);
    }

    private function extractDefaults($data)
    {
        if (count($this->defaults) > 0) {
            $results = [];
            foreach ($data as $key => $value) {
                if (in_array($key, $this->defaults)) {
                    if ($value instanceof ValueInterface) {
                        $results[$key] = $value->value();
                    } else {
                        $results[$key] = $value;
                    }
                }
            }
            return $results;
        }

        //No defaults so why the data, pass it on anyway.
        return $data;
    }

    private function getCountFields()
    {
        $total = 0;
        foreach ($this->fields as $key => $value) {
            if ($value->getType() != 'hidden') {
                $total++;
            }
        }
        return $total;
    }

    private function replaceTempVars()
    {
        foreach ($this->fields as $field) {
            if ($field instanceof ReceivesData) {
                $field->addData($this->default_data);
            }
        }

        foreach ($this->buttons as $button) {
            if ($button instanceof ReceivesData) {
                $button->addData($this->default_data);
            }
        }
    }
}
