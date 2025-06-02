@extends('dashboard')
@section('content')
    <style>
        .table { width: 100%; }
        .table-striped tr:nth-child(odd) td,
        .table-striped tr:nth-child(odd) th {
            background-color: #dfe6e9;
        }
    </style>
    <div class="container-fluid" style="margin-top: 100px;padding-left: 200px;padding-right: 30px;">
        <div class="row">
            <div class="col-md-12">
                <!--Project Activity start-->
                <section class="panel">
                    

                    <div class="col-md-6  panel-body progress-panel">
                        <div class="row">
                            <div class="col-lg-12 task-progress pull-left" style="margin-bottom: 50px">
                                <h1>Tambah Tipe TMI</h1>
                            </div>
                        </div>

                        @if(Session::has('error_message'))
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <strong>Warning!</strong> {!!  Session::get('error_message')  !!}
                            </div>
                        @elseif (Session::has('success_message'))
                            <div class="alert alert-info alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <strong>Success!</strong> {!!  Session::get('success_message')  !!}
                            </div>
                        @endif

                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/posttipetmi') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group">
                                <label class="col-md-4 control-label">Kode Toko TMI <b style="color:red">*</b></label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control flat" name="kodetmi">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Name Toko TMI <b style="color:red">*</b></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control flat" name="namatoko">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </div>

                        </form>
                        {{--<div class="table table-hover">--}}
                        {{--</div>--}}
                    </div>

                    <div class="col-md-6  panel-body progress-panel">
                    <div class="row">
                        <div class="col-lg-12 task-progress pull-left">
                            <h1>List Tipe TMI</h1>
                        </div>
                    </div>

                    {{--<div class="table table-hover">--}}
                        <table class="datatable table table-striped table-bordered responsive" id="dtTable">
                            <thead>
                            <tr>
                                <th class="font-14" style="text-align: center;">Kode TMI</th>
                                <th class="font-14" style="text-align: center;">Nama TMI</th>
                            </tr>
                            </thead>
                        </table>
                    {{--</div>--}}
                    </div>
                </section>
            </div>


    </div>
    </div>
    <script>
        var table = $('#dtTable').dataTable( {
            ajax: '{{url('admin/tipetmi/datatable')}}',
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "columnDefs": [
                {"className": "dt-center", "targets": "_all"}
            ],

            columns: [
                { data: 'kode_tmi', name: 'kode_tmi'},
                { data: 'nama', name: 'nama'}
            ]
        });


    </script>
@endsection
