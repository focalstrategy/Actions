<?php

namespace FocalStrategy\Actions;

use FocalStrategy\Actions\Core\Action;
use Illuminate\Support\ServiceProvider;

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

    private function generatePath(string $action)
    {
        $cl = explode('\\', $action);
        return snake_case(last($cl));
    }
}
