@extends('layout.main')
@section('page-title', 'Departments')
@section('page-header', 'Departments')
@section('page-content')
    <div class="card">
        <div class="card-body">
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
