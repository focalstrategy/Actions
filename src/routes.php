<?php

Route::get('actionables/{action_name}/show', [
    'as' => 'actions.show',
    'uses' => 'ActionController@show',
]);

Route::get('actionables/{action_name}/bigbox', [
    'as' => 'actions.bigbox',
    'uses' => 'ActionController@showBigBox',
]);

Route::post('actionables/{action_name}/save', [
    'as' => 'actions.save',
    'uses' => 'ActionController@save',
    'before' => 'csrf'
]);
