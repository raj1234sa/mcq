@extends('layout.main')
@section('page-title', (isset($post_data) ? 'Edit Category' : 'Add Category'))
@section('page-header', (isset($post_data) ? 'Edit Category' : 'Add Category'))
@section('page-content')
    <div class="card">
        <form action="/subject-save-form" method="post" class="row no-gutters card-body">
            @csrf
            {!! form_element('Subject Name', 'subject_name', 'text', (isset($post_data) ? $post_data['subject_name'] : ''),array('validation'=>array('required'=>'This field is required'))) !!}
            {!! form_select('Department', 'department_id', (isset($post_data) ? $post_data['department_id'] : ''), array('list' => $departments, 'value_field'=>'id', 'text_field'=>'department_name','list_before'=>"<option value=''>Select Department</option>", 'attributes'=>"onchange='getCategory(this)'",'validation'=>array('required'=>'This field is required'))) !!}
            {!! form_select('Category', 'category_id', (isset($post_data) ? $post_data['category_id'] : ''), array('list' => $categories, 'value_field'=>'id', 'text_field'=>'category_name','list_before'=>"<option value=''>Select Category</option>",'validation'=>array('required'=>'This field is required'))) !!}
            {!! form_element('', 'this_action', 'text', (isset($post_data) ? $post_data['id'] : ''), array('frm_grp_class'=>'d-none')) !!}
        </form>
    </div>
@endsection
@section('js')
    <script !src="">
        var selected_category = "{{isset($post_data) ? $post_data['category_id'] : ''}}";
        $(document).ready(function () {
            $("#department_id").trigger('change');
        });

        function getCategory(dropdown) {
            var deptId = $(dropdown).val();
            $.ajax({
                url: '/get-department-category',
                type: 'POST',
                data: {dept_id: deptId, _token: $("#csrf").val(), selected: selected_category},
                success: function (response) {
                    $("#category_id").html(response);
                }
            });
        }
    </script>
@endsection
