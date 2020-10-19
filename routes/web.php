<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'App\Http\Controllers\AdminController@loginView');
Route::post('/login-auth', 'App\Http\Controllers\AdminController@loginAuth');
Route::get('/logout', 'App\Http\Controllers\AdminController@logout');

Route::get('/dashboard', 'App\Http\Controllers\DashboardController@index');

Route::get('/department-list', 'App\Http\Controllers\DepartmentController@index');
Route::get('/department-add', 'App\Http\Controllers\DepartmentController@create');
Route::get('/department-add/{id}', 'App\Http\Controllers\DepartmentController@edit');
Route::post('/department-save-form', 'App\Http\Controllers\DepartmentController@save');
Route::post('/get-departments', 'App\Http\Controllers\DepartmentController@getDepartments');

Route::get('/subject-list', 'App\Http\Controllers\SubjectController@index');


