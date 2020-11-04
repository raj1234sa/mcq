@extends('layout.main')
@section('page-title', (isset($post_data) ? 'Edit Question' : 'Add Question'))
@section('page-header', (isset($post_data) ? 'Edit Question' : 'Add Question'))
@section('page-content')
    <div class="card">
        <form action="/question-save-form" method="post" class="row no-gutters card-body">
            @csrf
            {!! form_element('Exam Name', 'exam_name', 'text', (isset($post_data) ? $post_data['exam_name'] : ''),array('validation'=>array('required'=>'This field is required'))) !!}
            {!! form_element('Exam Duration (Minutes)', 'duration', 'number', (isset($post_data) ? $post_data['duration'] : ''),array('validation'=>array('required'=>'This field is required'))) !!}
            {!! form_element('Passmark (%)', 'passmark', 'number', (isset($post_data) ? $post_data['passmark'] : ''),array('validation'=>array('required'=>'This field is required'))) !!}
            {!! form_element('Deadline', 'deadline', 'datepicker', (isset($post_data) ? $post_data['deadline'] : ''),array('validation'=>array('required'=>'This field is required'), 'hint'=>'Pick a date')) !!}
            {!! form_select('Subject', 'subject_id', (isset($post_data) ? $post_data['subject_id'] : ''), array('list' => $subjects, 'value_field'=>'id', 'text_field'=>'subject_name','list_before'=>"<option value=''>Select Subject</option>", 'attributes'=>"onchange='getCategory(this)'",'validation'=>array('required'=>'This field is required'))) !!}
            {!! form_select('Category', 'category_id', (isset($post_data) ? $post_data['category_id'] : ''), array('list' => array(), 'value_field'=>'id', 'text_field'=>'category_name','list_before'=>"<option value=''>Select Category</option>",'validation'=>array('required'=>'This field is required'))) !!}
            {!! form_element('Terms & Conditions', 'terms_conditions', 'textarea', (isset($post_data) ? $post_data['terms_conditions'] : ''),array('validation'=>array('required'=>'This field is required'))) !!}
            {!! form_element('', 'this_action', 'text', (isset($post_data) ? $post_data['id'] : ''), array('frm_grp_class'=>'d-none')) !!}
        </form>
    </div>
@endsection
@section('js')
    <script !src="">
        var selected_category = "{{isset($post_data) ? $post_data['category_id'] : ''}}";
        $(document).ready(function () {
            $("#subject_id").trigger('change');
        });

        function getCategory(dropdown) {
            var subjId = $(dropdown).val();
            $.ajax({
                url: '/get-subject-category',
                type: 'POST',
                data: {subj_id: subjId, _token: $("#csrf").val(), selected: selected_category},
                success: function (response) {
                    $("#category_id").html(response);
                }
            });
        }
    </script>
@endsection
