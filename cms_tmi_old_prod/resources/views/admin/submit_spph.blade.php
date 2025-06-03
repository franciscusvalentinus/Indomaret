@extends('dashboard')
@section('content')

    <style>
        .table-striped tr:nth-child(odd) td,
        .table-striped tr:nth-child(odd) th {
            background-color: #dfe6e9;
        }
        /*#footer_table{*/
        /*    margin-top: 20px;*/
        /*}*/
        /*#footer_table tr th{*/
        /*    text-align: right;*/
        /*}*/
        /*#footer_table tr td{*/
        /*    text-align: right;*/
        /*}*/
        /*#footer_table tr:nth-child(odd){*/
        /*    background-color: #dddddd;*/
        /*}*/
        .modal_detail_user {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            padding-top: 100px; /* Location of the box */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }
        .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        }
        .loader{
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite; /* Safari */
            animation: spin 2s linear infinite;
        }
        /* Safari */
        @-webkit-keyframes spin {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
          }
    </style>
    <div class="container-fluid" style="margin-top: 100px;padding-left: 300px;padding-right: 200px;">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <div class="panel-body progress-panel">
                        <div class="row">
                            <div class="col-lg-8 task-progress pull-left">
                                <h1>SUBMIT SPPH</h1>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            
                            <div class="row">
                                
                                <div class="col-md-3">
                                </div>
                                <div class="col-md-3">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="datatable table table-striped table-bordered responsive" id="dtTable">
                                        <thead>
                                            <tr>
                                                <th class="font-14" style="text-align: center;">Nama Member</th>
                                                <th class="font-14" style="text-align: center;">Nomor Pengajuan</th>
                                                <th class="font-14" style="text-align: center;">Kredit Limit</th>
                                                <th class="font-14" style="text-align: center;">Sudah Submit SPPH</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pending_approval_list as $pending)
                                            <tr>
                                                <td>{{$pending->name}}</td>
                                                <td>{{$pending->credit_number}}</td>
                                                <td>{{$pending->credit_limit}}</td>
                                                @if($pending->real_order == null)
                                                    <td>Belum</td>
                                                @else
                                                    <td>Sudah</td>
                                                @endif
                                            </tr>
                                            @endforeach
                                            {{-- <tr>
                                                <th class="font-14" style="text-align: center;">hello world</th>
                                                <th class="font-14" style="text-align: center;">test</th>
                                            </tr>
                                            <tr>
                                                <th class="font-14" style="text-align: center;">hello 2</th>
                                                <th class="font-14" style="text-align: center;">test 2</th>
                                            </tr> --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form id="exportform" action="exportformpb" method="post" target="_blank">
                        <input type="text" name="_token" value="{{ csrf_token() }}" hidden>
                        <input type="text" id="efcabang" name="efcabang" hidden>
                        <input type="text" id="eftoko" name="eftoko" hidden>
                        <input type="text" id="efhari" name="efhari" hidden>
                        <input type="text" id="efstart" name="efstart" hidden>
                        <input type="text" id="efend" name="efend" hidden>
                    </form>
                </section>
            </div>
        </div>

        <div class="modal" id="submitSPPH_loader" role="dialog">
            <div class="modal-dialog modal-dialog" style="max-width: 100%; max-height: 100%; position: absolute">
                <p style="color: white;">Mohon Tunggu</p>
                <div class="loader"></div>
            </div>
        </div>
        <div id="detail_user" class="modal_detail_user" role="dialog">
            <!-- Modal content -->
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close" data-dismiss="modal_detail_user">&times;</span>
                    {{-- <h2>Modal Header</h2> --}}
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <p class="col-md-6">
                            Nama Member
                        </p>
                        <p class="col-md-6" id="member_name">
                        </p>

                        <p class="col-md-6">
                            Kode Member
                        </p>
                        <p class="col-md-6" id="member_code_credit">
                        </p>

                        <p class="col-md-6">
                            Nomor Kredit
                        </p>
                        <p class="col-md-6" id="credit_number">
                        </p>

                        <p class="col-md-6">
                            Nama Toko
                        </p>
                        <p class="col-md-6" id="store_name">
                        </p>

                        <p class="col-md-6">
                            Total Pinjaman
                        </p>
                        <p class="col-md-6" id="total_credit">
                        </p>

                        <p class="col-md-6">
                            Tenor (bulan)
                        </p>
                        <p class="col-md-6" id="tenor">
                        </p>

                        <p class="col-md-6">
                            Start Awal Cicilan
                        </p>
                        <p class="col-md-6" id="start_period">
                            Data tidak ada
                        </p>

                        <p class="col-md-6">
                            Periode Akhir Cicilan
                        </p>
                        <p class="col-md-6" id="end_period">
                            Data tidak ada
                        </p>

                        <div class="col-md-12">
                            <label for="fix_order" style="width: 45%">Nominal Fix Order</label>
                            <input type="number" style="width: 50%" id="fix_order">
                            <button id="btn_get_sales_data" style="visibility: hidden;">Get Data</button>
                        </div>

                        <div class="col-md-12">
                            <label for="va_number" style="width: 45%">Nomor Virtual Account</label>
                            <input type="number" style="width: 50%" id="va_number">
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <input id="user_credit_approve_btn" type="button" value="Submit">
                    <input id="user_credit_print_spph_btn" type="button" value="Cetak SPPH">
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    var approve_table;
        $(document).ready(function(){
            //get_credit_approval_waiting_list
            var approve_table = $('#dtTable').DataTable();
            document.getElementById("fix_order").disabled = true;

            // approve_table = $('#dtTable').DataTable({
                
            //     processing: true,
            //     serverSide: true,
            //     ordering: true,
            //     searching : true,
            //     autoWidth: false,
            //     ajax: {
            //         url: 'get_credit_approval_waiting_list',
            //         // data: function(d){

            //         // }
            //     },
            //     columns: [
            //         {data: 'data1', name: 'data1'},
            //         {data: 'data2', name: 'data2'},
            //     ]
            // });
            // approve_table.draw();
            // approve_table.rows().every(function(){
            //     console.log('test');
            // });
            /*
            $('#example tbody').on('click', 'tr', function () {
            var data = table.row( this ).data();
            alert( 'You clicked on '+data[0]+'\'s row' );
            } );
            */
            $('#dtTable tbody').on('click', 'tr', function(){//todo buat modal untuk approve
            console.log('testsetsthskaeset');
                //hasil meeting kamis 11-02-2020 :
                /*
                    status_final_igr -> 1 setuju, 2 tidak setuju
                    tambah object "NILAI_TOTAL_BELANJA"
                    */
                var data = approve_table.row(this).data();
                // console.log( 'You clicked on '+data[1]+'\'s row' );
                // alert( 'TODO : Buat modal untuk approve member : '+data[0]);
                checkApprovalNumber(data[1]);
            });
        });

        function checkApprovalNumber(credit_number){
            $.ajax({
                    url: 'get_detail_credit',
                    type: 'POST',
                    data: {
                        credit_number:credit_number
                    },
                    success:function(msg){
                        console.log('testttttttt');
                        if(msg['status'] == 1){
                            showModal(msg);
                        } else{
                            $.alert('Terjadi kesalahan : ' + msg['message']);
                        }
                    }
                });            
        };
        var modal;
        function showModal(response){

            var data_approval = response['message'];
            var data_request = response['request_message'];

            if(data_request['status'] == 0){
                $.alert('Terjadi kesalahan : ' + data_request['message']);
            }

            console.log(data_approval);
            modal = document.getElementById("detail_user");
            /*modal.addEventListener('shown.bs.modal', function () {
                console.log(' HIDDEN BOSQUE');
              })*/
            modal.style.display = "block";
            var span = document.getElementsByClassName("close")[0];
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
            /*
            member_name
            member_code_credit
            credit_number
            store_name
            total_credit
            tenor
            start_period
            end_period
            */
            var credit_limit = data_approval['total_credit'];
            var credit_number = data_approval['credit_number'];
            console.log(data_approval['member_code']);
            document.getElementById("member_name").innerHTML = data_approval['name'];
            document.getElementById("member_code_credit").innerHTML = data_approval['member_code'];
            document.getElementById("credit_number").innerHTML = credit_number;
            document.getElementById("store_name").innerHTML = data_approval['store_name'];
            document.getElementById("total_credit").innerHTML = credit_limit;
            document.getElementById("tenor").innerHTML = data_approval['tenor'];
            document.getElementById("start_period").innerHTML = data_approval['start_period'];
            document.getElementById("end_period").innerHTML = data_approval['end_period'];
            document.getElementById("fix_order").value = data_approval['igr_total_sales'];
            document.getElementById("va_number").value = data_approval['va_number'];

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
            }

            console.log(data_approval['real_order']);
            if(data_approval['real_order'] == null){
                document.getElementById("user_credit_print_spph_btn").style.display = "none";
                document.getElementById("user_credit_approve_btn").style.display = "block";
                document.getElementById("va_number").disabled = false;
            } else{
                //kolom real_order tidak null, artinya sudah pernah submit
                document.getElementById("user_credit_print_spph_btn").style.display = "block";
                document.getElementById("user_credit_approve_btn").style.display = "none";
                fix_order = parseInt(data_approval['real_order']);

                document.getElementById("fix_order").value = fix_order;
                document.getElementById("va_number").value = data_approval['va_number'];
                document.getElementById("fix_order").disabled = true;
                document.getElementById("va_number").disabled = true;
            }
            
            $("#btn_get_sales_data").click(function(){
                //$("#submitSPPH_loader").modal();
                //tombol ini akan tidak di pakai!
                $.ajax({
                    url:'get_sales_data',
                    type:'POST',
                    data: {
                        'branch_code' : data_approval['branch_code'],
                        'member_code' : data_approval['member_code'],
                        'dpc' : credit_number
                    }, success:function(message){
                        status = message['status'];
                        console.log(message);
                        console.log(status);
                        
                        if(status == 1){
                            document.getElementById("fix_order").value = message['message'];
                        } else{
                            $.alert(message['message']);
                        }
                    }
                })
                //$("#submitSPPH_loader").modal('hide');
                
            });

            $("#user_credit_print_spph_btn").click(function(){
                $.ajax({
                    url:'encrypt_data',
                    type: 'POST',
                    data : {
                        'content':credit_number
                    }, success:function(message){
                        var status = message['status'];
                        if(status == 1){
//                            window.open('get_spph_file/'+message['message']);
                            window.open("http://18.138.71.214:81/view_spph/"+message['message']);
                        } else{
                            $.alert('Terjadi kesalahan : ' + msg['message']);
                        }
                    }
                })
            });
            $("#user_credit_approve_btn").click(function(){
                var fix_order = $("#fix_order").val();
                var va_number = $("#va_number").val();

                if(fix_order == ''){
                    $.alert('Nominal Fix Order harus di isi!');
                    return;
                } 
                if(va_number == ''){
                    $.alert('Nomor Virtual Account harus di isi!');
                    return;
                }
                fix_order = parseInt(fix_order);
                if(fix_order > credit_limit){
                    $.alert('Nominal Fix Order tidak boleh lebih dari total pinjaman maksimal!');
                    return;
                }
                sendApproval(modal, 1,credit_number, fix_order, va_number);
            });
        };

        function sendApproval(modal, is_approve, credit_number, fix_order, va_number){
            $("#submitSPPH_loader").modal('show');
            modal.style.display = "none";
            $.ajax({
                url: 'approve_credit',
                type: 'POST',
                data: {
                    is_approve:is_approve,
                    credit_number:credit_number,
                    fix_order:fix_order,
                    va_number:va_number
                },
                success:function(msg){
                    $("#submitSPPH_loader").modal('hide');
                    modal.style.display = "block";

                    $status = msg['status'];
                    if($status == 1){
                        modal.style.display = "none";
                        $.alert(msg['message'], {
                            autoClose: true,
                            closeTime: 5000,
    //                        position: ['top-center', [-0.70, 0]],
                            position: ['top-right', [-0.42, 0]],
                            title: false,
                            type: 'info',
                            speed: 'normal'
                        });
                        location.reload();
                    } else{
                        $.alert(msg['message']);
                    }
                }
            });
        };

        $(function() {

            $('input[name="daterange"]').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                requestPb();
            });

            $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

        });

        function numberWithCommas(x) {
            var parts = x.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return parts.join(".");
        }

        function exportIt()
        {
            var startdate;
            var enddate;
            var date = $('#scopehari').val();
            if( date === "daterange")
            {
                var temp = $('#daterange').val();
                temp = temp.split(" - ");
                startdate = temp[0];
                enddate = temp[1];
            }
            else if ( date === "yearmonth" )
            {
                date = $('#scopetahun').val() + $('#scopebulan').val();
                startdate = '%';
                enddate = '%';
            }

            var form = $('#exportform');
            $('#efcabang').val($('#scopecabang').val());
            $('#eftoko').val($('#scopetoko').val());
            $('#efhari').val(date);
            $('#efstart').val(startdate);
            $('#efend').val(enddate);

            form.submit();
        }
    </script>
    <style>
        .newexportbutton{
            margin-top: 20px;
        }
    </style>
@endsection