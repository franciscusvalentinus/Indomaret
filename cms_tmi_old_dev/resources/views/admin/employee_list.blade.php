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
        .modal {
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
    </style>
    <div class="container-fluid" style="margin-top: 100px;padding-left: 300px;padding-right: 200px;">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <div class="panel-body progress-panel">
                        <div class="row">
                            <div class="col-lg-8 task-progress pull-left">
                                <h1>DAFTAR KARYAWAN BOSQUE</h1>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <input id="recomendation_letter" type="text" placeholder="Masukkan nomor surat rekomendasi">
                                </div>
                                <div class="col-md-3">
                                    <button id="btn_get_employee" type="button" class="btn btn-primary btn-lg btn-block flat">Submit</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="datatable table table-striped table-bordered" id="dtTable" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th class="font-14" style="text-align: center;">Nama Member</th>
                                                <th class="font-14" style="text-align: center;">NIK</th>
                                                <th class="font-14" style="text-align: center;">Nomor Surat Rekomendasi</th>
                                                <th class="font-14" style="text-align: center;">Nomor Ticket</th>
                                                <th class="font-14" style="text-align: center;">Job Posisi</th>
                                                <th class="font-14" style="text-align: center;">Unit ID</th>
                                                <th class="font-14" style="text-align: center;">Kode Member</th>
                                                <th class="font-14" style="text-align: center;">Status DPC</th>
                                                <th class="font-14" style="text-align: center;">Submit SPPH</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($employee_list as $employee)
                                            <tr>
                                                <td>{{$employee->name}}</td>
                                                <td>{{$employee->nik}}</td>
                                                <td>{{$employee->rec_letter_number}}</td>
                                                <td>{{$employee->ticket_number}}</td>
                                                <td>{{$employee->job_position}}</td>
                                                <td>{{$employee->unit_id}}</td>
                                                <td>{{$employee->member_code}}</td>
                                                <td>{{$employee->dpc_status}}</td>
                                                <td>{{$employee->is_spph_submit}}</td>
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

        <div id="detail_user" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close">&times;</span>
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
            
            var approve_table = $('#dtTable').DataTable({
                "scrollX": true
            } );

            $('#btn_get_employee').on('click', function(){
                var rec_letter = $('#recomendation_letter').val();
                if(rec_letter == ''){
                    $.alert('Nomor surat rekomendasi harus di isi!'); 
                    return;
                }

                $.ajax({
                    url: 'get_employee_data',
                    type: 'POST',
                    data: {
                        'rec_letter' : rec_letter
                    },
                    success:function(msg){
                        $status = msg['status'];
                        if($status == 1){
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
                })
            })
        });


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