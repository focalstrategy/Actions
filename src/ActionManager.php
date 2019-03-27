<?php

namespace FocalStrategy\Actions;

use App;
use App\User;
use Auth;
use FocalStrategy\Actions\Core\Action;
use FocalStrategy\Actions\Core\ActionRenderType;
use FocalStrategy\Actions\Core\ActionResponse;

class ActionManager
{
    protected $actions = [];

    public function add(string $action, string $path = null)
    {
        if (class_exists($action)) {
            if (!$path) {
                $path = $this->generatePath($action);
            }

            if (!isset($this->actions[$path])) {
                $this->actions[$path] = $action;
                return;
            } else {
                //throw dup action name
            }
        }

        //throw dup action
    }

    public function displayAction(string $name, array $defaults = []) : Action
    {
        $action = $this->getAction($name);
        $action->addDefaultData($defaults);
        $action->setRenderType(ActionRenderType::FORM());
        return $action;
    }

    public function handleAction(User $user, string $name, array $input, bool $ajax = false) : ActionResponse
    {
        $action = $this->getAction($name);

        $response = $action->process($user, $input);
        $response->ajax($ajax);

        return $response;
    }

    public function list()
    {
        if (count($this->actions) == 0) {
            return [];
        }

        return array_merge(
            array_combine(['action'], array_values($this->actions)),
            array_combine(['url'], array_keys($this->actions))
        );
    }

    public function getAction(string $name)
    {
        $cl = $this->actions[$name];

        if (class_exists($cl)) {
            $action = App::make($cl);
            return $action;
        }
    }

    private function generatePath(string $action)
    {
        $cl = explode('\\', $action);
        return snake_case(last($cl));
    }
}
