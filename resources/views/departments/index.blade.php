@extends('layout.main')
@section('page-title', 'Departments')
@section('page-header', 'Departments')
@section('page-content')
    <div class="card">
        <div class="card-body">
            <div class="row no-gutters">
                <div class="col-12">
                    <form id="filterForm" class="form-inline">
                        {!! form_element('', 'keywords', 'text', '', array('frm_grp_class'=>'col-3 p-0 mr-3', 'hint'=>'Department Name', 'form_group'=>false)) !!}
                        <button type="button" class="btn btn-secondary btn--icon-text" id="search"><i class="zmdi zmdi-search"></i> Search</button>
                        <button type="button" class="btn btn-link" id="reset">Reset</button>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered ajax" data-load="/get-departments">
                    <thead class="thead-default">
                    <tr>
                        <th>Sr</th>
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
