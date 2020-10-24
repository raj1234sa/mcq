@extends('layout.main')
@section('page-title', 'Subjects')
@section('page-header', 'Subjects')
@section('page-content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered ajax" data-load="/get-subjects">
                    <thead class="thead-default">
                    <tr>
                        <th>Sr</th>
                        <th>Subject Name</th>
                        <th>Details</th>
                        <th class="text-center">Status</th>
                        <th data-order="false">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
