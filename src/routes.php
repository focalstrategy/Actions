<?php

Route::group(['middleware' => 'web'], function () {
    Route::get('actionables/{action_name}/show', [
        'as' => 'actions.show',
        'uses' => 'FocalStrategy\Actions\Core\ActionController@show',
    ]);

    Route::get('actionables/{action_name}/bigbox', [
        'as' => 'actions.bigbox',
        'uses' => 'FocalStrategy\Actions\Core\ActionController@showBigBox',
    ]);

    Route::post('actionables/{action_name}/save', [
        'as' => 'actions.save',
        'uses' => 'FocalStrategy\Actions\Core\ActionController@save',
        'before' => 'csrf'
    ]);
});
