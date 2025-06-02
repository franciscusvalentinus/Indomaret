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
        <div class="modal-body">
            
            <div class="alert alert-danger alert-dismissible">
                
                <ul id="error_message_list">
                </ul>
            </div>
            {{-- @if(Session::has('success'))
                <div class="alert alert-success alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Success</strong> hei
                </div>
            @endif

            @if(Session::has('error'))
                <div class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {{Session::get('error')}}
                </div>
            @endif --}}

            {{-- <form class="form-horizontal" method="POST" action="store_credit_request">
                {{ csrf_field() }}
                <input type="text" id="txt_id" hidden>

{{-- #NOMOR PENGAJUAN CICILAN
KODE MEMBER
BATAS NOMINAL PINJAMAN
TENOR CICILAN (DALAM BULAN)
PERIODE AWAL CICILAN
PERIODE AKHIR CICILAN
NILAI CICILAN PER BULAN
--}}

                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                    <span class="col-md-6 input-group-addon" style="min-width: 200px;text-align: left">Kode Member</span>
                    <select class="col-md-6" id="member_data">
                        <option value="%">-- Pilih Member --</option>
                        {!!$data_user!!}
                    </select>
                </div>

                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                    <span class="col-md-6 input-group-addon" style="min-width: 200px;text-align: left">No.KTP</span>
                    <input class="col-md-6" type="number" name="noktp" min="1" class="input-sm form-control" id="noktp">
                </div>

                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                    <input type="checkbox" id="is_idm_employee" onchange="handleCheckbox(this)"><label for="is_idm_employee">Apakah ini karyawan?</label>
                </div>

                <div id="input_nonik" class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px; display: none;">
                    <span class="col-md-6 input-group-addon" style="min-width: 200px;text-align: left">No Karyawan</span>
                    <input class="col-md-6" type="number" name="nonik" min="1" class="input-sm form-control" id="nonik">
                </div>
                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                    <span class="col-md-6 input-group-addon" style="min-width: 200px;text-align: left">Nominal Batas Pinjaman</span>
                    <input class="col-md-6" type="number" name="creditlimitmax" min="1" class="input-sm form-control" id="creditlimitmax">
                </div>

                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                    <span class="input-group-addon" style="min-width: 200px;text-align: left">Tenor Cicilan (Bulan)</span>
                    <input type="text" name="creditperiod" min="1" class="input-sm form-control" id="creditperiod">
                </div>

                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                    <span class="input-group-addon" style="min-width: 200px;text-align: left">Periode Akhir PB</span>
                    {{-- todo buat otomatis generate dari periode awal --}}
                    <input type="date" min={!!$current_date!!} name="last_order_period" min="1" class="input-sm form-control" id="last_order_period">
                </div>

                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                    <span class="input-group-addon" style="min-width: 200px;text-align: left">Periode Awal Pinjaman</span>
                    {{-- todo ini di buat select date yak --}}
                    <input type="date" min={!!$current_date!!} name="startperiod" class="input-sm form-control" id="startperiod">
                </div>

                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                    <span class="input-group-addon" style="min-width: 200px;text-align: left">Periode Akhir Pinjaman</span>
                    {{-- todo buat otomatis generate dari periode awal --}}
                    <input type="date" min={!!$current_date!!} name="endperiod" min="1" class="input-sm form-control" id="endperiod">
                </div>

                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                    <span class="input-group-addon" style="min-width: 200px;text-align: left">*) Nilai Cicilan Per Bulan</span>
                    <input type="number" name="nominalinstallment" min="1" class="input-sm form-control" id="nominalinstallment">
                </div>

                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                    <span class="input-group-addon" style="min-width: 200px;text-align: left">Toleransi keterlambatan (Optional) dalam bulan</span>
                    <input type="number" name="grace_period" min="1" class="input-sm form-control" id="grace_period">
                </div>

                <div class="bs-example" style="visibility: hidden">
                    <div class="panel-group" id="accordion">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree"><i class="glyphicon glyphicon-hand-down"></i><span style="color:red; font-weight: bold"> Mau Ubah Tipe Tmi ?</span></a>
                                </h4>
                            </div>
                            <div id="collapseThree" class="panel-collapse collapse">
                                <div class="panel-body" style="padding-bottom: 0px;">
                                    <p>Periksa kembali pilihan Anda sebelum tekan Tombol Simpan</p>
                                </div>

                                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                    <span class="input-group-addon" style="min-width: 142px;text-align: left">Pilih Tipe TMI</span>
                                    <select class="js-example-basic-single" name="tipe" id="tipex">
                                        {!! $tipetmi !!}
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-submit" id="btn_savemember">Submit</button>
                    <button type="button" class="btn btn-danger" id="btn_reset">Reset</button>
                </div>
            </form>
    </div>
    <script>

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
        });

        $("#btn_savemember").click(function(){

            $("#error_message_list").empty();
            // $('#scopecabang').val(),
            let error_list = document.getElementById("error_message_list");
            var is_employee = document.getElementById("is_idm_employee").checked;
            var no_nik = $("#nonik").val();
            var member_code = document.getElementById("member_data").value;
            var noktp = document.getElementById("noktp").value;
            var credit_limit_max = $("#creditlimitmax").val();
            var credit_period = $("#creditperiod").val();
            var last_order_period = $("#last_order_period").val();
            var start_period = $("#startperiod").val();
            var end_period = $("#endperiod").val();
            var grace_period = $("#grace_period").val();
            var nominalinstallment = $("#nominalinstallment").val();

            console.log('is_employe ? '+is_employee);

            console.log(member_code);
            console.log(credit_limit_max);
            console.log(credit_period);
            console.log(start_period);
            console.log(end_period);
            console.log(nominalinstallment);

//https://www.javascripttutorial.net/javascript-dom/javascript-innerhtml/
            if(member_code == '%'){
                let li = document.createElement('li');
                li.textContent = 'Kode Member harus di pilih!';
                error_list.appendChild(li);
            }
            if(noktp == ''){
                let li = document.createElement('li');
                li.textContent = 'Nomor KTP harus di isi!';
                error_list.appendChild(li);
            }

            if(is_employee && no_nik == ''){
                let li = document.createElement('li');
                li.textContent = 'Nomor Induk Karyawan harus di isi!';
                error_list.appendChild(li);
            }

            if(credit_limit_max == '' || credit_limit_max == null){
                let li = document.createElement('li');
                li.textContent = 'Nominal batas pinjaman harus di isi!!';
                error_list.appendChild(li);
            } else if(credit_limit_max <= 0){
                let li = document.createElement('li');
                li.textContent = 'Nominal batas pinjaman harus lebih dari nol!';
                error_list.appendChild(li);
            }

            if(credit_period < 1){
                let li = document.createElement('li');
                li.textContent = 'Periode cicilan minimal 1 bulan!';
                error_list.appendChild(li);
            } else if(credit_period > 20){
                let li = document.createElement('li');
                li.textContent = 'Periode cicilan maksimal 20 bulan!';
                error_list.appendChild(li);
            }

            console.log(nominalinstallment);
            if(start_period == '' || start_period == null){// format yyyy-MM
                let li = document.createElement('li');
                li.textContent = 'Periode awal pinjaman harus di isi!';
                error_list.appendChild(li);
            }
            if(end_period == '' || end_period == null){
                let li = document.createElement('li');
                li.textContent = 'Periode akhir pinjaman harus di isi!';
                error_list.appendChild(li);
            }

            var selisih = (new Date(end_period) - new Date(start_period))/(1000*60*60*24);
            console.log('hello : ' + (selisih/(1000*60*60*24)));
            if(selisih <= 0){
                let li = document.createElement('li');
                li.textContent = 'Tanggal periode akhir harus lebih besar dari periode awal!';
                error_list.appendChild(li);
            }

            if(nominalinstallment == ''){
                let li = document.createElement('li');
                li.textContent = 'Nominal cicilan harus di isi!!';
                error_list.appendChild(li);
            } else if(nominalinstallment <= 0){
                let li = document.createElement('li');
                li.textContent = 'Nominal cicilan harus lebih dari nol!!';
                error_list.appendChild(li);
            }

            // $.alert(member_code + ' | ' + credit_limit_max + ' | ' + credit_period + ' | ' + start_period + ' | '+ nominalinstallment, {
            //                 autoClose: true,
            //                 closeTime: 5000,
            //                 position: ['top-center', [-0.70, 0]],
            //                 // position: ['top-right', [-0.42, 0]],
            //                 title: false,
            //                 type: 'info',
            //                 speed: 'normal'
            //             });

            $.ajax({
                url : 'store_credit_request',
                type : 'POST', 
                data : {
                    'member_code' : member_code,
                    'credit_limit_max' : credit_limit_max,
                    'last_order_period' : last_order_period,
                    'start_period' : start_period,
                    'end_period' : end_period,
                    'grace_period' : grace_period,
                    'nominalinstallment' : nominalinstallment,
                    'credit_period' : credit_period,
                    'identity_card' : noktp,
                    'nik' : no_nik,
                    'is_employee' : is_employee?1:0,
                }
            })
        });
        $("#btn_reset").click(function(){
            document.getElementById("member_data").value = '%';
            $("#creditlimitmax").val("");
            $("#creditperiod").val("");
            $("#startperiod").val("");
            $("#endperiod").val("");
            $("#graceperiod").val("");
            $("#nominalinstallment").val("");
            $("#noktp").val("");
            $("#nonik").val("");

        })

        function handleCheckbox(checkbox){
            if(checkbox.checked == true){
                document.getElementById("input_nonik").style.display = "block";
            }else{
                document.getElementById("input_nonik").style.display = "none";
            }
        }
    </script>
@endsection
