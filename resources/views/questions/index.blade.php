@extends('layout.main')
@section('page-title', 'Questions')
@section('page-header', 'Questions')
@section('page-content')
    <div class="card">
        <div class="card-body">
            <div class="row no-gutters">
                <div class="col-12">
                    <form id="filterForm" class="form-inline">
                        {!! form_element('', 'keywords', 'text', '', array('frm_grp_class'=>'col-3 p-0 mr-3', 'hint'=>'Search Question', 'form_group'=>false)) !!}
                        {!! form_select('', 'exam_id', '', array('list'=>$examinations, 'text_field'=>'exam_name', 'value_field'=>'id','form_group'=>false,'frm_grp_class'=>'col-2 p-0 mr-3', 'searchdropdown'=>false, 'list_before'=>"<option value=''>Select Exam</option>")) !!}
                        <button type="button" class="btn btn-secondary btn--icon-text" id="search"><i class="zmdi zmdi-search"></i> Search</button>
                        <button type="button" class="btn btn-link" id="reset">Reset</button>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered ajax w-100" data-load="/get-questions">
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