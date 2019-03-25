<?php

namespace FocalStrategy\Actions\Core;

use FocalStrategy\Core\BaseBgType;
use Redirect;
use Response;

class ActionResponse
{
    protected $response;
    protected $ajax;

    protected $status;
    protected $message;
    protected $highlight;
    protected $remove_on_response;
    protected $remove_parent;
    protected $redirect_to;

    protected $with_errors;
    protected $with_input;
    protected $js_callback;
    protected $replacement;
    protected $reload_datatable_page;

    public static function make()
    {
        return new ActionResponse();
    }

    public function setResponse(callable $response) : ActionResponse
    {
        $this->response = $response;

        return $this;
    }

    public function ajax(bool $ajax) : ActionResponse
    {
        $this->ajax = $ajax;

        return $this;
    }

    public function highlight(BaseBgType $type) : ActionResponse
    {
        $this->highlight = (string) $type;

        return $this;
    }

    public function remove() : ActionResponse
    {
        $this->remove_on_response = true;

        return $this;
    }

    public function removeParent() : ActionResponse
    {
        $this->remove_parent = true;

        return $this;
    }

    public function replaceWith(string $replacement)
    {
        $this->replacement = $replacement;

        return $this;
    }

    public function success(string $message) : ActionResponse
    {
        $this->status = 'success';
        $this->message = $message;

        return $this;
    }

    public function error(string $message) : ActionResponse
    {
        $this->status = 'error';
        $this->message = $message;

        return $this;
    }

    public function redirectTo(string $redirect_to) : ActionResponse
    {
        $this->redirect_to = $redirect_to;

        return $this;
    }

    public function jsCallback(string $js_callback) : ActionResponse
    {
        $this->js_callback = $js_callback;

        return $this;
    }

    public function withErrors($validator) : ActionResponse
    {
        $this->with_errors = $validator;

        return $this;
    }

    public function withInput() : ActionResponse
    {
        $this->with_input = true;

        return $this;
    }

    public function reloadDatatablePage() : ActionResponse
    {
        $this->reload_datatable_page = true;

        return $this;
    }

    public function get()
    {
        if ($this->response) {
            $response = $this->response;
            return $response();
        }

        if ($this->ajax) {
            $data = [];
            $data[$this->status] = true;
            $data['message'] = $this->message;
            $data['notify'] = $this->message;

            if ($this->highlight) {
                $data['highlight'] = $this->highlight;
            }

            if ($this->remove_on_response) {
                $data['remove_on_response'] = true;
            }

            if ($this->remove_parent) {
                $data['remove_parent'] = true;
            }

            if ($this->redirect_to !== null) {
                $data['redirect_to'] = $this->redirect_to;
            }

            if ($this->js_callback) {
                $data['js_callback'] = $this->js_callback;
            }

            if ($this->with_errors != null) {
                $data['errors'] = $this->with_errors->errors();
            }

            if ($this->replacement != null) {
                $data['replace_with'] = $this->replacement;
            }

            if ($this->reload_datatable_page) {
                $data['reload_datatable_page'] = true;
            }

            return Response::json($data);
        } else {
            $response = null;
            if ($this->redirect_to  !== null) {
                $response = Redirect::to($this->redirect_to);
            } else {
                $response = Redirect::back();
            }

            if ($this->with_errors != null) {
                $response = $response->withErrors($this->with_errors);
            }

            if ($this->with_input != null) {
                $response = $response->withInput();
            }

            return $response->with('flash_'.$this->status, $this->message);
        }
    }
}
