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
Route::get('/department-delete/{id}', 'App\Http\Controllers\DepartmentController@deleteDepartment');
Route::post('/department-status', 'App\Http\Controllers\DepartmentController@changeStatus');

Route::get('/category-list', 'App\Http\Controllers\CategoryController@index');
Route::get('/category-add', 'App\Http\Controllers\CategoryController@create');
Route::get('/category-add/{id}', 'App\Http\Controllers\CategoryController@edit');
Route::post('/category-save-form', 'App\Http\Controllers\CategoryController@save');
Route::post('/get-categories', 'App\Http\Controllers\CategoryController@getCategories');
Route::get('/category-delete/{id}', 'App\Http\Controllers\CategoryController@deleteCategory');
Route::post('/category-status', 'App\Http\Controllers\CategoryController@changeStatus');

Route::get('/subject-list', 'App\Http\Controllers\SubjectController@index');
Route::get('/subject-add', 'App\Http\Controllers\SubjectController@create');
Route::get('/subject-add/{id}', 'App\Http\Controllers\SubjectController@edit');
Route::post('/subject-save-form', 'App\Http\Controllers\SubjectController@save');
Route::post('/get-subjects', 'App\Http\Controllers\SubjectController@getSubjects');
Route::get('/subject-delete/{id}', 'App\Http\Controllers\SubjectController@deleteSubject');
Route::post('/subject-status', 'App\Http\Controllers\SubjectController@changeStatus');
Route::post('/get-department-category', 'App\Http\Controllers\SubjectController@getDepartmentCategory');

