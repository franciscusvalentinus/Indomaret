@extends('dashboard')
@section('content')

    <style>
        .table-striped tr:nth-child(odd) td,
        .table-striped tr:nth-child(odd) th {
            background-color: #dfe6e9;
        }
    </style>
    <div class="container-fluid" style="margin-top: 100px;padding-left: 250px;padding-right: 5%;">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <div class="panel-body progress-panel">
                        <div class="row">
                            <div class="col-lg-8 task-progress pull-left">
                                <h1>Upload PLU Tolakan</h1>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                            <label>Upload file excel</label>
                                <input type="file" id="file" name="file" required="required">
                                <button id="submit_import">SUBMIT</button>
                    </div>

                </section>
                <table class="datatable table table-striped table-bordered responsive" id="plu_rejection_table">
                    <thead>
                    <tr>
                        <th class="font-14" style="text-align: center;">KODE CABANG</th>
                        <th class="font-14" style="text-align: center;">CABANG</th>
                        <th class="font-14" style="text-align: center;">PLU</th>
                        <th class="font-14" style="text-align: center;">Aksi</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="optionModal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="option_modal_title">TITLE</h4>
                </div>
                <div class="modal-body">
                    <p>Apa yang akan anda lakukan?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="optionrestoreplu">Pulihkan</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <script type="text/javascript">
        let excelFile;

        $("#file").on('change', function (e) {
            console.log('test');
            excelFile = e.target.files[0];
            console.log(excelFile);
        })
        $(function() {
            //todo function here

        });

        $( document ).ready(function() {
            setDatatable();
        });

        function setDatatable(){
            var table = $('#plu_rejection_table').dataTable({
                dom: 'Bfrtip',
                ajax: '{{url('get_plu_rejection_list')}}',
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                columns: [
                    { data: 'branch_code', name: 'branch_code'},
                    { data: 'branch_name', name: 'branch_name'},
                    { data: 'plu', name: 'plu'},
                    { data: 'aksi', name: 'aksi'},
                ]
            });
        }

        $("#submit_import").click(function(){
            let formData = new FormData();
            formData.append('file',excelFile);
            $.ajax({
                url: 'submitplutolakan',
                type : 'POST',
                contentType: false,
                processData: false,
                data: formData,
                success:function (message) {
                    var status = message['status'];
                    var message = message['message'];
                    if(status == 1){
                        $.alert(message, {
                            autoClose: true,
                            closeTime: 5000,
                            //                        position: ['top-center', [-0.70, 0]],
                            position: ['top-right', [-0.42, 0]],
                            title: false,
                            type: 'info',
                            speed: 'normal'
                        });
                        window.location.reload(true);
                    } else{
                        $.alert(message)
                    }
                }
            });
        });

        function optionModal(rejected_id) {
            $.ajax({
                url: 'get_detail_plu_rejection',
                type: 'POST',
                data: {
                    id : rejected_id
                }, success:function(msg){
                    var status = msg['status'];
                    if(status == 1){
                        var plu = msg['message'];
                        $("#optionModal").modal();
                        document.getElementById("option_modal_title").innerHTML = 'Pengaturan PLU ' + plu['plu'];
                        var btn_restore = document.getElementById('optionrestoreplu');
                        btn_restore.dataset.target = rejected_id;
                    } else{
                        $.alert(msg['message']);
                    }
                }, error:function (errmsg) {
                    console.log(errmsg)
                    $.alert(errmsg)
                }
            })
        }

        $("#optionrestoreplu").click(function(){
            var btn_restore = document.getElementById('optionrestoreplu');
            var pluRejectId = btn_restore.dataset.target;

            $.ajax({
                url: 'restore_plu_rejection',
                type: 'POST',
                data: {
                    id : pluRejectId
                }, success:function(msg){
                    var status = msg['status'];
                    if(status == 1){
                        $.alert(msg['message'], {
                            autoClose: true,
                            closeTime: 5000,
                            //                        position: ['top-center', [-0.70, 0]],
                            position: ['top-right', [-0.42, 0]],
                            title: false,
                            type: 'info',
                            speed: 'normal'
                        });
                        window.location.reload(true);
                    } else{
                        $.alert(msg['message']);
                    }
                    console.log(msg);
                }, error:function (errmsg) {
                    console.log(errmsg)
                }
            })
        });

    </script>
    <style>
        .newexportbutton{
            margin-top: 20px;
        }
        #table-wrapper {
            position:relative;
        }
        #table-scroll {
            height:100%;
            overflow:auto;
            margin-top:20px;
        }
        #table-wrapper table {
            width:100%;

        }
        #table-wrapper table * {
            /*background:yellow;*/
            color:black;
        }
        #table-wrapper table thead th .text {
            position:absolute;
            top:-20px;
            z-index:2;
            height:20px;
            width:35%;
            border:1px solid red;
        }
        th {
            background: white;
            position: sticky;
            top: 0;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        }
    </style>
@endsection