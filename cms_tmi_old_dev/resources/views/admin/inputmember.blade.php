@extends('dashboard')
@section('content')
    <style>
        .table { width: 100%; }
        .table-striped tr:nth-child(odd) td,
        .table-striped tr:nth-child(odd) th {
            background-color: #dfe6e9;
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
    <div class="container-fluid" style="margin-top: 100px;padding-left: 200px;padding-right: 30px;">
        <div class="row">


            <div class="col-md-12">
                <!--Project Activity start-->
                <section class="panel">
                    <div class="panel-body progress-panel">
                        <div class="row">
                            <div class="col-lg-8 task-progress pull-left">
                                <h1>Register Member TMI</h1>
                            </div>
                        </div>
                    </div>

                    <div class="panel-body">
                        <button type="button" id="btn_add_member" class="btn btn-primary btn-lg btn-block flat">Tambah Member Baru</button>
                    </div>

                    <div class="panel-body progress-panel">
                        <div class="row">
                            <div class="col-lg-12 task-progress pull-left">
                                <h1>List Member TMI</h1>
                            </div>
                        </div>
                    </div>

                    {{--<div class="table table-hover">--}}
                    <table class="datatable table table-striped table-bordered responsive" id="dtTable">
                        <thead>
                        <tr>
                            <th class="font-14" style="text-align: center;">Email</th>
                            <th class="font-14" style="text-align: center;">Nama Toko</th>
                            <th class="font-14" style="text-align: center;">Tipe TMI</th>
                            <th class="font-14" style="text-align: center;">Cabang</th>
                            <th class="font-14" style="text-align: center;">Kode Member</th>
                            <th class="font-14" style="text-align: center;">No.HP</th>
                            <th class="font-14" style="text-align: center;">Alamat</th>
                            <th class="font-14" style="text-align: center;">Total Produk</th>
                            <th class="font-14" style="text-align: center;">Aksi</th>
                            {{--<th class="font-14" style="text-align: center;">Status</th>--}}
                        </tr>
                        </thead>
                    </table>
                    {{--</div>--}}

                </section>
            </div>

            <div class="modal fade-scale" id="ModalAktivasiMember" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                            <h4 class="modal-title">Form Aktivasi</h4>
                        </div>
                        <form class="form-horizontal" method="POST" action="">
                            <input type="text" id="txt_tipeid" hidden>
                            <div class="modal-body">
                                <div class="modal-header">
                                    <h4 class="modal-title">Tipe TMI </h4>
                                    <div class="col-md-12 input-group inputs" style="margin-top: 30px; margin-bottom: 30px;padding-left: 0px;padding-right: 0px;">
                                        <span class="input-group-addon flat" style="min-width: 142px;text-align: left">Pilih Tipe TMI</span>
                                        <select class="js-example-basic-single" name="tipemember" id="tipetmi">
                                            <option value="">-- Pilih Tipe Tmi --</option>
                                            {!! $tipetmi !!}
                                        </select>
                                    </div>
                                    <div class="alert alert-danger flat" role="alert">
                                        <strong>Warning!</strong> Mohon Pastikan telah memilih Tipe Member TMI !
                                    </div>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger flat" id="btn_aktivasi"><i class="icon_key"></i>Aktivasi Member</button>
                                <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade-scale" id="EditModalMember" role="dialog">
                <div class="modal-dialog modal-dialog" style="position:absolute;margin-left: -200px;">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Edit Member Tmi </h4>
                        </div>
                        <div class="modal-body">

                            <form class="form-horizontal" method="POST" action="">
                                <input type="text" id="txt_id" hidden>

                                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                    <span class="input-group-addon" style="min-width: 142px;text-align: left">Email </span>
                                    <input type="text" class="input-sm form-control" id="emailx" type="text" name="email">
                                </div>

                                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                    <span class="input-group-addon" style="min-width: 142px;text-align: left">Nama Toko</span>
                                    <input type="text" class="input-sm form-control" id="namex" type="text" name="name">
                                </div>

                                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                    <span class="input-group-addon" style="min-width: 142px;text-align: left">Tipe Tmi</span>
                                    <input type="text" name="tipenametmi" min="1" class="input-sm form-control" id="tipenametmi">
                                </div>

                                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                    <span class="input-group-addon" style="min-width: 142px;text-align: left">Cabang</span>
                                    <input type="text" name="cabang" min="1" class="input-sm form-control" id="cabangx">
                                </div>
                                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                    <span class="input-group-addon" style="min-width: 142px;text-align: left">Kode member</span>
                                    <input type="text" name="kodemember" min="1" class="input-sm form-control" id="kodememberx">
                                </div>

                                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                    <span class="input-group-addon" style="min-width: 142px;text-align: left">No Hp</span>
                                    <input type="text" name="nohp" min="1" class="input-sm form-control" id="nohpx">
                                </div>

                                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                    <span class="input-group-addon" style="min-width: 142px;text-align: left">Limit Free PLU </span>
                                    <input type="number"  name="limit_free_plu" class="form-control" id="limit_free_plux">
                                    {{--<input type="text" name="address" min="1" class="input-sm form-control" id="addressx">--}}
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Tanggal Akhir Free Ongkir  <b style="color:red">*</b></label>
                                    <div class="col-md-6">
                                        <input type="date" class="form-control flat" maxlength="50" id='free_ongkir_exp_date' name="NoHp">
                                    </div>
                                </div>

                                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                    <span class="input-group-addon" style="min-width: 142px;text-align: left">Alamat </span>
                                    <textarea class="form-control" rows="5" id='addressx' name="address" type="text"></textarea>
                                    {{--<input type="text" name="address" min="1" class="input-sm form-control" id="addressx">--}}
                                </div>

                                <div class="bs-example" style="visibility: visible">
                                    <div class="panel-group" id="accordion">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree"><i class="glyphicon glyphicon-hand-down"></i><span style="color:red; font-weight: bold"> Mau Ubah Tipe Tmi ?</span></a>
                                                </h4>
                                            </div>
                                            <div id="collapseThree" class="panel-collapse collapse in">
                                                <div class="panel-body" style="padding-bottom: 0px;">
                                                    <p>Periksa kembali pilihan Anda sebelum tekan Tombol Simpan (APABILA TIDAK INGIN UBAH TIPE, SILAHKAN PILIH TIPE TMI : <b>PILIH TIPE</b>)</p>
                                                </div>

                                                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                                    <span class="input-group-addon" style="min-width: 142px;text-align: left">Pilih Tipe TMI</span>
                                                    <select class="js-example-basic-single" name="tipe" id="tipex">
                                                        {!! $tipetmi !!}
                                                    </select>
                                                </div>

                                                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                                    <span class="input-group-addon" style="min-width: 142px;text-align: left">Silahkan masukkan alasan</span>
                                                    <input type="text" class="js-example-basic-single" name="tipe" id="reason_change">
                                                    </input>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="btn_savemember">Simpan</button>
                                    <button type="button" class="btn btn-danger" id="btn_deletemember">Hapus</button>
                                    <button type="button" class="btn btn-danger" id="btn_resetpasswordmember">Reset Password</button>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
            </div>

            <div class="modal fade-scale" id="AddMemberTmi_loader" role="dialog">
                <div class="modal-dialog modal-dialog" style="max-width: 100%; max-height: 100%; position: absolute">
                    <p style="color: white;">Mohon Tunggu</p>
                    <div class="loader"></div>
                </div>
            </div>
            <div class="modal fade-scale" id="AddMemberTmi" role="dialog">
                <div class="modal-dialog modal-dialog" style="position:absolute;margin-left: -200px;">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Register Member Tmi </h4>
                        </div>
                        {{--<div class="modal-header">--}}
                        {{--<h4 class="modal-title">Register Member TMI </h4>--}}

                        <div class="modal-body">
                            <form class="form-horizontal" method="POST" action="">
                                <input type="text" id="txt_id" hidden>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Nama Toko TMI <b style="color:red">*</b></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control flat" id='namatoko' name="namatoko">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Alamat Email <b style="color:red">*</b></label>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control" maxlength="50" id='email' name="email">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Password <b style="color:red">*</b></label>
                                    <div class="col-md-6">
                                        <input type="password" class="form-control" maxlength="50" id='password' name="Password">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Confirm Password <b style="color:red">*</b></label>
                                    <div class="col-md-6">
                                        <input type="password" class="form-control" maxlength="50" id="passwordconfimation" name="Password_confirmation">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Limit Free Plu <b style="color:red">*</b></label>
                                    <div class="col-md-6">
                                        <input type="number" class="form-control" maxlength="7" min="0" value="0" id="limitfreeplu" name="limit_free_plu">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Pilih Cabang TMI <b style="color:red">*</b></label>
                                    <div class="col-md-6">
                                        <select class="js-example-basic-single" name="cabang" id="txt_cab" onChange= "changeMember()">
                                            {!! $branch !!}
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Pilih Member IGR <b style="color:red">*</b></label>
                                    <div class="col-md-6">
                                        <select id='member' class="js-example-basic-single" name="member">
                                            {!! $member !!}
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Pilih Tipe TMI <b style="color:red">*</b></label>
                                    <div class="col-md-6">
                                        <select class="js-example-basic-single" id='txt_tipe' name="tipemember">
                                            {!! $tipetmi !!}
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">No. Handphone  <b style="color:red">*</b></label>
                                    <div class="col-md-6">
                                        <input type="number" class="form-control flat" maxlength="50" id='nohape' name="NoHp">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Tanggal GO  <b style="color:red">*</b></label>
                                    <div class="col-md-6">
                                        <input type="date" class="form-control flat" maxlength="50" id='go_date' name="NoHp">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Tanggal Akhir Free Ongkir  <b style="color:red">*</b></label>
                                    <div class="col-md-6">
                                        <input type="date" class="form-control flat" maxlength="50" id='ongkir_exp_date' name="NoHp">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Alamat Penagihan  <b style="color:red">*</b></label>
                                    <div class="col-md-6">
                                        <textarea class="form-control" rows="5" id='alamat' name="Alamat" type="text"></textarea>
                                        {{--<input type="text" class="form-control flat" maxlength="150" id='alamat' name="Alamat">--}}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                        <input type="checkbox" id="is_need_credit" onchange="handleCheckboxIsCredit(this)"><label for="is_need_credit">Ajukan Kredit?</label>

                                        <div id="credit_request" style="display: none;">
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Batas waktu penggunaan cicilan  <b style="color:red">*</b></label>
                                                {{-- batas waktu penggunaan cicilan (berupa tanggal dan tidak boleh lebih dari tanggal GO) --}}
                                                <div class="col-md-6">
                                                    <input type="date" class="form-control flat" maxlength="50" id='credit_expired_date' name="CreditExpDate">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Nominal batas pinjaman <b style="color:red">*</b></label>
                                                {{-- nominal batas pinjaman (baca tipe TMI) --}}
                                                <div class="col-md-6">
                                                    <input type="number" class="form-control flat" maxlength="50" id='credit_limit' name="CreditLimit" oninput="updateFlagTakeOver()">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <input type="checkbox" id="is_idm_employee" onchange="handleCheckboxIsEmployee(this)"><label for="is_idm_employee">Karyawan Indomaret group?</label>
                                                {{-- Karyawan / non karyawan (checkbox) --}}
                                            </div>
                                            <div id="input_no_nik" class="form-group" style="display: none;">
                                                <label class="col-md-4 control-label">Input NIK<b style="color:red">*</b></label>
                                                <div class="col-md-6">
                                                    <input type="number" class="form-control flat" maxlength="50" id='nik' name="NIK">
                                                </div>
                                            </div>
                                            <div id="input_non_employee_data" class="form-group" style="display: block;">
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">Nilai Cicilan Bulanan<b style="color:red">*</b></label>
                                                    <div class="col-md-6">
                                                        <input type="number" value="0" class="form-control flat" min="0" maxlength="50" id='credit_per_month' name="Credit Per Month">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">Take Over Bank<b style="color:red">*</b></label>
                                                    <div class="col-md-6">
                                                        <input type="text" maxlength="1" value="N" class="form-control flat" maxlength="50" id='flag_take_over_bank' name="Take Over Bank" oninput="updateTenorAndGracePeriod()" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">Periode Pantau<b style="color:red">*</b></label>
                                                    <div class="col-md-6">
                                                        <input type="number" class="form-control flat" maxlength="50" max="20" id='monitoring_period' name="Monitoring Period" oninput="validationMonitoringPeriod()">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">No KTP <b style="color:red">*</b></label>
                                                <div class="col-md-6">
                                                    <input type="number" class="form-control flat" id='noktp' name="noktp">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">NPWP </label>
                                                {{-- NPWP (optional) --}}
                                                <div class="col-md-6">
                                                    <input type="number" placeholder="0000-0000" class="form-control flat input_npwp" maxlength="50" id='npwp' name="NPWP">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">PKP </label>
                                                {{-- PKP (optional) --}}
                                                <div class="col-md-6">
                                                    <input type="number" class="form-control flat" maxlength="50" id='pkp' name="PKP">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Tenor (dalam bulan) <b style="color:red">*</b></label>
                                                {{-- Tenor (dalam bulan) --}}
                                                <div class="col-md-6">
                                                    <input type="number" value="0" class="form-control flat" max="20" id='tenor' name="Tenor">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label" style="display: none">Masa tenggang pembayaran cicilan <b style="color:red">*</b></label>
                                                <div class="col-md-6" style="display: none">
                                                    <label class="radio-inline">
                                                        <input id="optradiotrue" type="radio" onclick="setGracePeriod(true)" name="optradio" disabled>Ya
                                                      </label>
                                                      <label class="radio-inline">
                                                        <input id="optradiofalse" type="radio" onclick="setGracePeriod(false)" name="optradio" checked disabled>Tidak
                                                      </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Lama masa tenggang<b style="color:red">*</b></label>
                                                {{-- Lama Masa tenggang (hardcode 3 bulan jika masa tenggang = Y) --}}
                                                <div class="col-md-6">
                                                    <input type="number" class="form-control flat" maxlength="50" id='grace_period' name="GracePeriod" value="0" disabled>
                                                </div>
                                            </div>
                                            
                                        </div>

                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-target="AddMemberTmi_loader" id="btn_cancelregister">Close</button>
                                    <button type="button" class="btn btn-primary" data-target="AddMemberTmi_loader" id="btn_regnewmember">Tambah</button>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
    <script>
        let tmi_credit_type_id = 0;
        var table = $('#dtTable').dataTable( {
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            ajax: '{{url('admin/member/datatable')}}',
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
                { data: 'store_name', name: 'store_name'},
                { data: 'tipetmi', name: 'tipetmi'},
                { data: 'cabang', name: 'cabang'},
                { data: 'member_code', name: 'member_code'},
                { data: 'phone_number', name: 'phone_number'},
                { data: 'addressmember', name: 'addressmember'},
                { data: 'total_products', name: 'total_products'},
//                { data: 'aksi', name: 'aksi'},
                { data: 'status', name: 'status'}
            ]
        });

        function updateFlagTakeOver(){
            var is_idm_employee = document.getElementById("is_idm_employee").checked;
            if(is_idm_employee){
                tmi_credit_type_id = 1;
                document.getElementById("flag_take_over_bank").value = 'N';
                document.getElementById("monitoring_period").value = '0';
                document.getElementById("monitoring_period").disabled = true;
            } else{
                var credit_limit = $("#credit_limit").val();
                if(credit_limit == '' || credit_limit <= 10000000){
                    tmi_credit_type_id = 2;
                    document.getElementById("flag_take_over_bank").value = 'N';
                    document.getElementById("monitoring_period").value = '0';
                    document.getElementById("monitoring_period").disabled = true;
                } else if(credit_limit>10000000 && credit_limit<=20000000){
                    tmi_credit_type_id = 3;
                    document.getElementById("flag_take_over_bank").value = 'Y';
                    document.getElementById("monitoring_period").value = '1';
                    document.getElementById("monitoring_period").min = '1';
                
                    document.getElementById("monitoring_period").disabled = false;
                } else if(credit_limit > 20000000){
                    document.getElementById('credit_limit').value = '20000000';
                    $.alert('Cicilan tidak boleh lebih dari 20 JUTA')
                }
            }
            //updateTenorAndGracePeriod();
            //var x = document.getElementById("myInput").value;
            //document.getElementById("demo").innerHTML = "You wrote: " + x;
            /*
                Kriteria untuk Non-Karyawan: 
                KUR Super Mikro : 
                    Nilai Kredit <= 10 jt
                    Maka : 
                        Periode Pantau = 0
                        Flag Take Over = N
                KUR Mikro : 
                    Nilai Kredit 10jt>x>20jt
                    Maka : 
                        Periode Pantau > 0
                        Flag Take Over = Y
            */
        }

        function validationMonitoringPeriod(){
            var monitoring_period = document.getElementById("monitoring_period").value;
            if(!monitoring_period.disabled){
                if(monitoring_period <= 0){
                    document.getElementById("monitoring_period").value = 1;
                }
            }
        }

        function updateTenorAndGracePeriod(){
            //todo logic nya belum masuk nih!!!
            /*
            Jika take_over_bank = "Y", maka : 
                inputan tenor cicilan tidak dapat di isi
                inputan masa tenggang tidak dapat di isi
            klo non-karyawan berarti default 0 dan gabisa di isi ya
            */
            /*
            optradiotrue
            optradiofalse
            */
            var flag_take_over = $('#flag_take_over_bank').val();
            
            if(flag_take_over == 'Y'){
                
                document.getElementById('grace_period').value = '0';
                document.getElementById('grace_period').disabled = true;
                
                document.getElementById("tenor").value = '0';
                document.getElementById("tenor").disabled = true;
                toggleOpt(true);
            } else{
                document.getElementById('grace_period').value = temp_grace_period;
                document.getElementById('grace_period').disabled = false;

                document.getElementById("tenor").disabled = false;
                toggleOpt(false);
            }
        }

        function toggleOpt(bools){
            document.getElementById('optradiotrue').disabled = bools;
            document.getElementById('optradiofalse').disabled = bools;
        }

        let temp_grace_period = 3;
        function setGracePeriod(is_grace){
            var current_grace_period = $("#grace_period").val();
            if(is_grace){
                grace_period = temp_grace_period;
            } else{
                temp_grace_period = current_grace_period;
                grace_period = 0;
            }
            document.getElementById("grace_period").value = grace_period;
        }

        function handleCheckboxIsCredit(checkbox){
            if(checkbox.checked == true){
                document.getElementById("credit_request").style.display = "block";
            }else{
                document.getElementById("credit_request").style.display = "none";
            }
        }

        function handleCheckboxIsEmployee(checkbox){
            if(checkbox.checked == true){
                document.getElementById("input_no_nik").style.display = "block";
                document.getElementById("input_non_employee_data").style.display = "none";
            }else{
                document.getElementById("input_no_nik").style.display = "none";
                document.getElementById("input_non_employee_data").style.display = "block";
            }
            memocps_9_2_4_1(checkbox.checked);
        }

        function memocps_9_2_4_1(is_idm_employee){
            /*
            Nomor memo : 967/CPS/20
            Point : 9.2.4.1 
            bunyi memo cps : 
            Tidak ada tenor cicilan dan pemberian masa tenggang
            pembayaran. Sehubungan dengan hal tsb., program
            komputer mem-protect agar kolom tenor cicilan dan masa
            tenggang pembayaran tidak dapat di-isi jika ada
            flag “take over Bank”.
            */
            if(is_idm_employee){
                document.getElementById("tenor").disabled = false;
                document.getElementById("grace_period").disabled = false;
                $("#flag_take_over_bank").val('N');
                $("#monitoring_period").val('0');

                toggleOpt(false);
                return;
            }
            var flag_take_over_bank = $("#flag_take_over_bank").val();
            if(flag_take_over_bank == 'Y'){
                document.getElementById("tenor").disabled = true;
                document.getElementById("grace_period").disabled = true;
                toggleOpt(true);
            } else{
                document.getElementById("tenor").disabled = false;
                document.getElementById("grace_period").disabled = false;
                toggleOpt(false);
            }
        }


        $(document).on('click', '#btn_add_member', function(){
            var id = $(this).val();

//            $("#txt_id").val(id);
            $("#AddMemberTmi").modal();

        });

        $(document).ready(function(){
            // Add minus icon for collapse element which is open by default
            $(".collapse.in").each(function(){
                $(this).siblings(".panel-heading").find(".glyphicon").addClass("glyphicon-minus").removeClass("glyphicon-plus");
            });
            $(".collapse").on('show.bs.collapse', function(){
                $(this).parent().find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
            }).on('hide.bs.collapse', function(){
                $(this).parent().find(".glyphicon").removeClass("glyphicon-minus").addClass("glyphicon-plus");
            });

            //set min max date
            //todo perbaikin ini ya
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth()+1; //January is 0!
            var yyyy = today.getFullYear();
            if(dd<10){
                    dd='0'+dd
                } 
                if(mm<10){
                    mm='0'+mm
                } 

            today = yyyy+'-'+mm+'-'+dd;
            document.getElementById("go_date").min=today;
            document.getElementById("ongkir_exp_date").min=today;
            document.getElementById("credit_expired_date").min=today;

            
        });

        $("#btn_regnewmember").click(function(){
            
            var id = $("#txt_id").val();
            var namatoko = $("#namatoko").val();
            var email = $("#email").val();
            var password = $("#password").val();
            var passwordconfimation = $("#passwordconfimation").val();
            var limitfreeplu = $("#limitfreeplu").val();
            var txt_cab = $("#txt_cab").val();
            var member = $("#member").val();
            var txt_tipe = $("#txt_tipe").val();
            var nohape = $("#nohape").val();
            var alamat = $("#alamat").val();
            var ongkir_exp_date = $("#ongkir_exp_date").val();
            var go_date = $("#go_date").val();

            //bagian credit
            var is_need_credit = document.getElementById("is_need_credit").checked;
            var credit_expired_date = $("#credit_expired_date").val();
            var credit_limit = $("#credit_limit").val();
            var noktp = $("#noktp").val()

            var is_idm_employee = document.getElementById("is_idm_employee").checked;

            var flag_take_over_bank = $("#flag_take_over_bank").val();
            var credit_per_month = $("#credit_per_month").val();
            var monitoring_period = $("#monitoring_period").val();
            
            var nik = $("#nik").val();

            var npwp = $("#npwp").val();
            var pkp = $("#pkp").val();
            var tenor = $("#tenor").val();
            var grace_period = $("#grace_period").val();

            if(is_idm_employee){
                tmi_credit_type_id = 1;
                flag_take_over_bank = 'N';
                monitoring_period = 0;
            }
            var credit_data = '{'+
                '"is_need_credit":"'+(is_need_credit == true ? 1 : 0)+'",'+
                '"credit_expired_date":"'+credit_expired_date+'",'+
                '"credit_limit":"'+credit_limit+'",'+
                '"tmi_credit_type_id":"'+tmi_credit_type_id+'",'+
                '"credit_per_month":"'+credit_per_month+'",'+
                '"flag_take_over_bank":"'+flag_take_over_bank+'",'+
                '"monitoring_period":"'+monitoring_period+'",'+
                '"is_idm_employee":"'+(is_idm_employee == true ? 1 : 0)+'",'+
                '"nik":"'+nik+'",'+
                '"identity_number":"'+noktp+'",'+
                '"npwp":"'+npwp+'",'+
                '"pkp":"'+pkp+'",'+
                '"tenor":"'+tenor+'",'+
                '"grace_period":"'+grace_period+ '"'+
            '}';
            /*
                credit_data["is_need_credit"] = is_need_credit == true ? 1 : 0;
                credit_data["credit_expired_date"] = credit_expired_date;
                credit_data["credit_limit"] = credit_limit;
                //todo perhatikan 4 object dibawah ini!
                credit_data["tmi_credit_type_id"] = tmi_credit_type_id;
                credit_data["credit_per_month"] = credit_per_month;
                credit_data["flag_take_over_bank"] = flag_take_over_bank;
                credit_data["monitoring_period"] = monitoring_period;
                credit_data["is_idm_employee"] = is_idm_employee == true ? 1 : 0;
                credit_data["nik"] = nik;
                credit_data["identity_number"] = noktp;
                credit_data["npwp"] = npwp;
                credit_data["pkp"] = pkp;
                credit_data["tenor"] = tenor;
                credit_data["grace_period"] = grace_period;
*/
            let any_error = 0;

            if(credit_per_month == ''){
                any_error = 1;
                alert('Silahkan masukkan cicilan per bulan!');
            }

            if(is_need_credit){
                if(credit_expired_date == '' || credit_limit == '' || tenor == '' || grace_period == ''){
                    any_error = 1;
                    alert('Silahkan lengkapi data pengajuan kredit!');
                }
                if(is_idm_employee && nik == ''){
                    any_error = 1;
                    alert('Silahkan isi NIK!');
                }
                if(!is_idm_employee){
                    if(flag_take_over_bank == ''){
                        any_error = 1;
                        alert('Silahkan pilih take over bank!');
                    }
                    if(monitoring_period == ''){
                        any_error = 1;
                        alert('Silahkan masukkan periode pantau!');
                    }
                    if(credit_per_month <= 0){
                        any_error = 1;
                        alert('Cicilan per bulan tidak boleh kurang dari nol!');
                    }
                    if(credit_limit < 10000000){
                        any_error = 1;
                        alert('Member non-karyawan dengan cicilan <10 juta tidak di ijinkan mengajukan cicilan dari CMS!');
                        return;
                    }
                } else {
                    //besar 20jt
                    //kecil 10jt
                    var str_tipe = $("#txt_tipe option:selected").text().toUpperCase();
                    var limit_max = 0;
                    if(str_tipe.includes("BESAR")){
                        limit_max = 20000000;
                    } else{
                        if(!is_idm_employee){
                            //harusnya ga mungkin masuk sini!
                            any_error = 1;
                            alert('Member non-karyawan dengan cicilan <10 juta tidak di ijinkan mengajukan cicilan dari CMS!');
                            return;
                        }
                        limit_max = 10000000;
                    }
                    limit_max = 20000000; //29-04-2021 permintaan kak vernandus

                    if(credit_limit > limit_max){
                        any_error = 1;
                        alert('Nominal Batas Pinjaman tidak boleh lebih dari '+limit_max+'!');
                    }
                }
            }
            // alert(
            //     is_need_credit + ' | ' + credit_expired_date + ' | ' + credit_limit + ' | ' +
            //     is_idm_employee + ' | ' + nik + ' | ' + npwp + ' | ' + pkp + ' | ' + tenor + ' | ' + 
            //     grace_period
            // );

            if(namatoko == "" || email == "" || password == "" || passwordconfimation == "" ||
                txt_cab == "" || member == "" || txt_tipe == "" || nohape == "" || alamat == "" ||
                passwordconfimation == "" || parseInt(limitfreeplu) <=0 || limitfreeplu == "" ||
                ongkir_exp_date == "" || go_date == ""
            ){
                any_error = 1;
                alert("Silahkan lengkapi data!");
            }          
            if(limitfreeplu.startsWith("-")){
                any_error = 1;
                alert("Limit Free Plu harus lebih dari nol!");
            } else if(limitfreeplu.includes("e")){
                any_error = 1;
                alert("Limit Free Plu harus berupa angka!");
            } else if(email.includes(' ')){
                any_error = 1;
                alert('email tidak boleh mengandung spasi!');
            }
            var count = 0;
           if(any_error == 0){
                toggleAttribute(true);
            
                try{
               count++;

                    $("#AddMemberTmi_loader").modal();
                    $("#AddMemberTmi").modal('hide');
                    $.ajax({
                        url: 'registermember',
                        type : 'POST',
                        dataType : 'json',
                        data : {
                            id : id,
                            namatoko : namatoko,
                            email : email,
                            password : password,
                            passwordconfimation : passwordconfimation,
                            limitfreeplu: limitfreeplu,
                            txt_cab : txt_cab,
                            member : member,
                            txt_tipe :txt_tipe,
                            nohape : nohape,
                            alamat : alamat,
                            ongkir_exp_date : ongkir_exp_date,
                            go_date : go_date,
                            credit_request : JSON.stringify(credit_data)
                        },
                        success:function(msg){
                            toggleAttribute(false);
                            $("#AddMemberTmi").modal('show');
                            $("#AddMemberTmi_loader").modal("hide");
                            console.log(msg+"");
                            console.log(msg["status"]);
                            var status = msg['status'];
                            var message = msg['message'];
                            if(status == 1){
                                $.alert('Data berhasil di tambah', {
                                    autoClose: true,
                                    closeTime: 5000,
        //                        position: ['top-center', [-0.70, 0]],
                                    position: ['top-right', [-0.42, 0]],
                                    title: false,
                                    type: 'info',
                                    speed: 'normal'
                                });
                                table.api().ajax.reload();
                                $('#AddMemberTmi').modal('hide');
                                location.reload();
                            } else{
                                $.alert(message)
                            }

                        },fail:function (msg) {
                            $("#AddMemberTmi").modal('show');
                            toggleAttribute(false);
                            $("#AddMemberTmi_loader").modal("hide");
                            $.alert('err : '.msg);
                        }
                    });
                } catch(e){
                    console.log(count + '|' + e);
                }
                
            
           }
           
        });


        $(document).on('click', '#btn_edit_member', function(){
            var id = $(this).val();

            $("#txt_id").val(id);

            $("#EditModalMember").modal();
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
                url:'memberAjax',
                type : 'POST',
                data : {id : id},
                success:function(msg){
                    console.log(msg);
                    var user_id = msg[0]['iduser'];
                    console.log(user_id);
                    if(user_id == 1){
                        document.getElementById("emailx").disabled = false;
                        document.getElementById("free_ongkir_exp_date").disabled = false;
                    } else {
                    document.getElementById("emailx").disabled = true;
                    document.getElementById("free_ongkir_exp_date").disabled = true;
                    }
                    document.getElementById("namex").disabled = false;
                    document.getElementById("tipex").disabled = false;
                    document.getElementById("tipenametmi").disabled = true;
                    document.getElementById("cabangx").disabled = true;
                    document.getElementById("kodememberx").disabled = true;
                    document.getElementById("nohpx").disabled = false;
                    document.getElementById("addressx").disabled = false;
                    // $.alert(msg[0]['tipetmi']);

                    $("#emailx").val(msg[0]['email']);
                    $("#namex").val(msg[0]['store_name']);
                    $("#tipex").val(msg[0]['tipetmi']);
                    $("#tipenametmi").val(msg[0]['tipetmi']);
                    $("#cabangx").val(msg[0]['cabang']);
                    $("#kodememberx").val(msg[0]['member_code']);
                    $("#nohpx").val(msg[0]['phone_number']);
                    $("#addressx").val(msg[0]['addressmember']);
                    $("#limit_free_plux").val(msg[0]['limitfreeplu']);
                    $("#free_ongkir_exp_date").val(msg[0]['free_shipping']);
                    // $.alert($("#tipex").val());
                    
                }

            });

        });

        $(document).on('click', '#btn_aktivasi_mm', function(){
            $("#txt_tipeid").val($(this).val());
            $("#ModalAktivasiMember").modal();
        });


        $("#btn_aktivasi").click(function(){
            var tipeid = $("#txt_tipeid").val();
            var tipetmi = $("#tipetmi").val();
            $.ajax({
                url: 'aktivasimember',
                type : 'POST',
                data : {
                    tipeid : tipeid,
                    tipetmi : tipetmi
                },
                success:function(msg){
//                    if(data == '1'){
//                        cartReload();
//                        getCartDetail();
//                    }else{
//                        $('#modal-error').html('<div class="alert alert-danger">' + data + '</div>');
//                    }
                    $.alert('Member Berhasil Di Aktivasi', {
                        autoClose: true,
                        closeTime: 5000,
                        position: ['top-right', [-0.42, 0]],
                        title: false,
                        type: 'danger',
                        speed: 'normal'
                    });
                    table.api().ajax.reload();
                    $('#ModalAktivasiMember').modal('hide');

                }
            });
//            }
        });

        $("#btn_resetpasswordmember").click(function () {
            var membercode = $("#kodememberx").val();

            $.ajax({
                url: 'resetpasswordmember',
                type: 'POST',
                data: {
                    member_code : membercode
                }, success:function (msg) {
                    var status = msg['status'];
                    var message = msg['message'];
                    if(status == 1) {
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
                        $.alert(msg['message']);
                    }
                }, error:function (errmsg) {
                    console.log(errmsg);
                    $.alert(errmsg);
                }
            })
        });

        $("#btn_deletemember").click(function () {
            var membercode = $("#kodememberx").val();

            $.ajax({
                url: 'deactivatemember',
                type: 'POST',
                data: {
                    member_code : membercode
                }, success:function (msg) {
                    var status = msg['status'];
                    var message = msg['message'];
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
                        $.alert(msg['message']);
                    }
                }, error:function(errmsg){
                    console.log(errmsg);
                    $.alert(errmsg);
                }
            });
        });

        $("#btn_savemember").click(function(){
            $("#AddMemberTmi_loader").modal();
            $("#EditModalMember").modal('hide');

            var id = $("#txt_id").val();
            var namatoko = $("#namex").val();
            var tipetmi = $("#tipex").val();//isi nya kode tmi!
            var reason = $("#reason_change").val();
            var nohp = $("#nohpx").val();
            var address = $("#addressx").val();
            var email = $("#emailx").val();
            var limit_free_plu = $("#limit_free_plux").val();
            var free_ongkir_exp_date = $("#free_ongkir_exp_date").val();
            
            $.ajax({
                url: 'editmember',
                type : 'POST',
                data : {
                    id : id,
                    namatoko : namatoko,
                    tipetmi : tipetmi,
                    nohp : nohp,
                    address : address,
                    email : email,
                    limit_free_plu : limit_free_plu,
                    free_ongkir_exp_date : free_ongkir_exp_date,
                    reason_change : reason
                },
                success:function(msg){
                    $("#AddMemberTmi_loader").modal('hide');
                    $("#EditModalMember").modal('show');
                    var status = msg['status'];
                    var message = msg['message'];
                    if(status == 1) {
                        $.alert('Data berhasil di update', {
                            autoClose: true,
                            closeTime: 5000,
    //                        position: ['top-center', [-0.70, 0]],
                            position: ['top-right', [-0.42, 0]],
                            title: false,
                            type: 'info',
                            speed: 'normal'
                        });
                        table.api().ajax.reload();
                        $('#EditModalMember').modal('hide');
                        location.reload();
                    } else {
                        
                            alert(msg['message']);
                        
                    }
                }, error:function(msg){
                    $("#AddMemberTmi_loader").modal('hide');
                    $("#EditModalMember").modal('show');
                    $.alert(msg);
                }
            });
//            }
        });

        function changeMember(){
            var cab = $("#txt_cab").val();
            $.ajax({
                url : 'get_member',
                data : {id_cab : cab},
                success : function(msg){
                    document.getElementById("member").disabled = false;
                    $("#member").html(msg);
                }
            });
        }

        function toggleAttribute(is_disabled){
            document.getElementById("txt_id").disabled = is_disabled;
            document.getElementById("namatoko").disabled = is_disabled;
            document.getElementById("email").disabled = is_disabled;
            document.getElementById("password").disabled = is_disabled;
            document.getElementById("passwordconfimation").disabled = is_disabled;
            document.getElementById("limitfreeplu").disabled = is_disabled;
            document.getElementById("txt_cab").disabled = is_disabled;
            document.getElementById("member").disabled = is_disabled;
            document.getElementById("txt_tipe").disabled = is_disabled;
            document.getElementById("nohape").disabled = is_disabled;
            document.getElementById("alamat").disabled = is_disabled;
            document.getElementById("ongkir_exp_date").disabled = is_disabled;
            document.getElementById("go_date").disabled = is_disabled;
            document.getElementById("is_need_credit").disabled = is_disabled;
            document.getElementById("credit_expired_date").disabled = is_disabled;
            document.getElementById("credit_limit").disabled = is_disabled;
            document.getElementById("noktp").disabled = is_disabled;
            document.getElementById("is_idm_employee").disabled = is_disabled;
            document.getElementById("nik").disabled = is_disabled;
            document.getElementById("npwp").disabled = is_disabled;
            document.getElementById("pkp").disabled = is_disabled;
            document.getElementById("tenor").disabled = is_disabled;
            document.getElementById("grace_period").disabled = is_disabled;
        }

    </script>
@endsection
