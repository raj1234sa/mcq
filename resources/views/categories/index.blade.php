@extends('layout.main')
@section('page-title', 'Categories')
@section('page-header', 'Categories')
@section('page-content')
    <div class="card">
        <div class="card-body">
            <div class="row no-gutters">
                <div class="col-12">
                    <form id="filterForm" class="form-inline">
                        {!! form_element('', 'keywords', 'text', '', array('frm_grp_class'=>'col-3 p-0 mr-3', 'hint'=>'Category Name', 'form_group'=>false)) !!}
                        {!! form_select('', 'department_id', '', array('list'=>$departments, 'text_field'=>'department_name', 'value_field'=>'id','form_group'=>false,'frm_grp_class'=>'col-2 p-0 mr-3', 'searchdropdown'=>false, 'list_before'=>"<option value=''>Select Department</option>")) !!}
                        <button type="button" class="btn btn-secondary btn--icon-text" id="search"><i class="zmdi zmdi-search"></i> Search</button>
                        <button type="button" class="btn btn-link" id="reset">Reset</button>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered ajax" data-load="/get-categories">
                    <thead class="thead-default">
                    <tr>
                        <th>Sr</th>
                        <th>Category Name</th>
                        <th>Department Name</th>
                        <th class="text-center">Status</th>
                        <th data-order="false">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
