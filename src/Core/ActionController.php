<?php

namespace FocalStrategy\Actions\Core;

use Action;
use App\User;
use Auth;
use FocalStrategy\Actions\ActionManager;
use Illuminate\Http\Request;

class ActionController
{
    public function show(string $action_name)
    {
        $action = Action::displayAction($action_name, Input::all());

        $page = new Page();
        $page->setTitle($action->getTitle());
        $page->setPageTitle($action->getTitle());
        $page->breadcrumb($action->getTitle());

        return $page
            ->view('actions::show')
            ->with('action', $action);
    }

    public function showBigBox(Request $req, string $action_name)
    {
        $action = Action::displayAction($action_name, $req->all());

        return view('actions::show_bigbox')
            ->with('action', $action);
    }

    public function save(Request $req, string $action_name)
    {
        $user = null;

        foreach (config('auth.guards') as $guard => $details) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                break;
            }
        }

        if (!$user && config('app.debug')) {
            $user = new User();
        }

        $response = Action::handleAction(
            $user,
            $action_name,
            $req->all(),
            $req->ajax()
        );

        return $response->get();
    }
}
