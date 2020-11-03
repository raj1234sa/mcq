@extends('layout.main')
@section('page-title', 'Examinations')
@section('page-header', 'Examinations')
@section('page-content')
    <div class="card">
        <div class="card-body">
            <div class="row no-gutters">
                <div class="col-12">
                    <form id="filterForm" class="form-inline">
                        {!! form_element('', 'keywords', 'text', '', array('frm_grp_class'=>'col-3 p-0 mr-3', 'hint'=>'Subject Name', 'form_group'=>false)) !!}
                        {!! form_select('', 'subject_id', '', array('list'=>$subjects, 'text_field'=>'subject_name', 'value_field'=>'id','form_group'=>false,'frm_grp_class'=>'col-2 p-0 mr-3', 'searchdropdown'=>false, 'list_before'=>"<option value=''>Select Subject</option>")) !!}
                        {!! form_select('', 'category_id', '', array('list'=>$categories, 'text_field'=>'category_name', 'value_field'=>'id','form_group'=>false,'frm_grp_class'=>'col-2 p-0 mr-3', 'searchdropdown'=>false, 'list_before'=>"<option value=''>Select Category</option>")) !!}
                        <button type="button" class="btn btn-secondary btn--icon-text" id="search"><i class="zmdi zmdi-search"></i> Search</button>
                        <button type="button" class="btn btn-link" id="reset">Reset</button>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered ajax w-100" data-load="/get-exams">
                    <thead class="thead-default">
                    <tr>
                        <th>Sr</th>
                        <th>Examination Name</th>
                        <th>Category</th>
                        <th>Subject</th>
                        <th>Deadline</th>
                        <th class="text-center">Status</th>
                        <th data-order="false" data-printhide='true'>Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        var tabletools = ['print', 'export'];
        var exportRoute = '/exam-export';
    </script>
@endsection