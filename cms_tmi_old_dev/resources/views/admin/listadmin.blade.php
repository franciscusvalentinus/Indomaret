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

                    <div class="panel-body progress-panel">
                        <div class="row">
                            <div class="col-lg-12 task-progress pull-left">
                                <h1>List Admin TMI Cabang</h1>
                            </div>
                        </div>
                    </div>

                    {{--<div class="table table-hover">--}}
                    <table class="datatable table table-striped table-bordered responsive" id="dtTable">
                        <thead>
                        <tr>
                            <th class="font-14" style="text-align: center;">Email</th>
                            <th class="font-14" style="text-align: center;">Nama</th>
                            <th class="font-14" style="text-align: center;">Cabang</th>
                            <th class="font-14" style="text-align: center;">Role</th>
                            <th class="font-14" style="text-align: center;">Aksi</th>
                        </tr>
                        </thead>
                    </table>
                </section>
            </div>


        </div>
    </div>

    <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="position: absolute; margin-left: -300px">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Konfirmasi Penghapusan</h4>
                </div>

                <div class="modal-body">
                    <p>Anda akan menghapus Admin CMS TMI, aksi ini tidak dapat dibatalkan.</p>
                    <p>Apakah anda yakin ingin melanjutkan?</p>
                </div>

                <div class="modal-footer">
                    <a class="btn btn-danger btn-ok"><i class="fa fa-times"></i> Delete</a>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        var table = $('#dtTable').dataTable( {
            ajax: '{{url('admin/admintmi/datatable')}}',
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
                { data: 'email', name: 'email'},
                { data: 'namaadmin', name: 'namaadmin'},
                { data: 'cabang', name: 'cabang'},
                { data: 'keterangan', name: 'keterangan'},
                { data: 'status', name: 'status'}
            ]
        });


        $('#confirm-delete').on('show.bs.modal', function(e) {
            $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
        });

    </script>
@endsection
