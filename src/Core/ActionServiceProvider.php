<?php

namespace FocalStrategy\Actions\Core;

use App;
use Auth;
use Core\Actions\Action;
use Core\Actions\ActionException;
use Core\Actions\ActionResponse;

class ActionSServiceProvider
{
    public function displayAction(string $name, array $defaults = []) : Action
    {
        $action = $this->getAction($name);
        $action->addDefaultData($defaults);
        $action->setRenderType(ActionRenderType::FORM());
        return $action;
    }

    public function handleAction(string $name, array $input, bool $ajax = false) : ActionResponse
    {
        $action = $this->getAction($name);

        $response = $action->process(Auth::user(), $input);
        $response->ajax($ajax);

        return $response;
    }

    public function make(string $action_class, array $data = []) : Action
    {
        $inst = App::make($action_class);
        $inst->addDefaultData($data);
        return $inst;
    }

    private function getAction(string $name) : Action
    {
        $cl = '\Core\Actions\Actionables\\'.ucwords(camel_case($name));
        if (class_exists($cl)) {
            $action = App::make($cl);
            return $action;
        }

        throw new ActionException('Action Not Found: '.$cl);
    }
}
