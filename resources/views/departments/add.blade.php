@extends('layout.main')
@section('page-title', (isset($post_data) ? 'Edit Department' : 'Add Department'))
@section('page-header', (isset($post_data) ? 'Edit Department' : 'Add Department'))
@section('page-content')
    <div class="card">
        <form action="/department-save-form" method="post" class="row no-gutters card-body">
            @csrf
            {!! form_element('Department Name', 'department_name', 'text', (isset($post_data) ? $post_data['department_name'] : ''), array('validation'=>array('required'=>'This Field is required.'))) !!}
            {!! form_element('', 'this_action', 'text', (isset($post_data) ? $post_data['id'] : ''), array('frm_grp_class'=>'d-none')) !!}
        </form>
    </div>
@endsection
