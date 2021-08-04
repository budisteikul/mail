<?php

if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

Route::resource('/mails/webhook','budisteikul\mail\Controllers\WebhookController',[ 'names' => 'mail_webhooks' ])->only(['store','index']);
Route::get('/mails/check','budisteikul\mail\Controllers\MailController@check')->middleware(['web','auth','verified','CoreMiddleware']);
Route::resource('/mails/settings','budisteikul\mail\Controllers\SettingController',[ 'names' => 'mail_settings' ])->middleware(['web','auth','verified','CoreMiddleware']);
Route::resource('/mails/attachments','budisteikul\mail\Controllers\AttachmentController',[ 'names' => 'mail_attachments' ])->only(['show'])->middleware(['web','auth','verified','CoreMiddleware']);
Route::get('/mails/folder/{id}/', 'budisteikul\mail\Controllers\MailController@index')->middleware(['web','auth','verified','CoreMiddleware']);
Route::resource('/mails','budisteikul\mail\Controllers\MailController',[ 'names' => 'mails' ])->middleware(['web','auth','verified','CoreMiddleware']);
Route::get('/mails/{id}/{view}', 'budisteikul\mail\Controllers\MailController@show')->name('mails.show')->middleware(['web','auth','verified','CoreMiddleware']);
