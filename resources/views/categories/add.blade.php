@extends('layout.main')
@section('page-title', (isset($post_data) ? 'Edit Category' : 'Add Category'))
@section('page-header', (isset($post_data) ? 'Edit Category' : 'Add Category'))
@section('page-content')
    <div class="card">
        <form action="/category-save-form" method="post" class="row no-gutters card-body">
            @csrf
            {!! form_element('Category Name', 'category_name', 'text', (isset($post_data) ? $post_data['category_name'] : '')) !!}
            {!! form_select('Department', 'department_id', (isset($post_data) ? $post_data['department_id'] : ''), array('list' => $departments, 'value_field'=>'id', 'text_field'=>'department_name')) !!}
            {!! form_element('', 'this_action', 'text', (isset($post_data) ? $post_data['id'] : ''), array('frm_grp_class'=>'d-none')) !!}
        </form>
    </div>
@endsection
