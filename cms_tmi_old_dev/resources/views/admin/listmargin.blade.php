@extends('dashboard')
@section('content')

    <style>
        .table-striped tr:nth-child(odd) td,
        .table-striped tr:nth-child(odd) th {
            background-color: #dfe6e9;
        }
    </style>
    <div class="container-fluid" style="margin-top: 100px;padding-left: 300px;padding-right: 300px;">

        <div class="row">
            <div class="col-md-12">
                <!--Project Activity start-->
                <section class="panel">

                    <div class="panel-body progress-panel">
                        <div class="row">
                            <div class="col-lg-8 task-progress pull-left">
                                <h1>Daftar Master Margin</h1>
                            </div>
                        </div>
                    </div>

                    <form class="form-horizontal" role="form" method="POST" id="search-form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

                        <div class="form-group" style="margin-top: 20px;">
                            <label class="col-md-2 control-label">Pilih Tipe TMI <b style="color:red">*</b></label>
                            <select name="tipemember" id="tipetmi" class="selectpicker" data-live-search="true" onchange="changeCabang()">
                                {!! $tipetmi !!}
                            </select>
                        </div>

                        <div class="form-group" style="margin-top: 20px;">
                            <label class="col-md-2 control-label">Pilih Cabang <b style="color:red">*</b></label>
                            <select name="tipecabang" id="tipecabang" class="selectpicker" data-live-search="true" onchange="changeCabang()">
                                {!! $branch !!}
                            </select>
                        </div>


                        <div style="padding-left: 15px;margin-bottom: 40px">
                            <button type=submit style="margin-top: 5px;" class="btn btn-primary igr-flat"><i class="fa fa-check-square-o">&nbsp</i>Submit</button>
                        </div>
                    </form>

                    <table class="datatable table table-striped table-bordered responsive" id="dtTable">
                        <thead>
                        <tr>
                            <th class="font-14" style="text-align: center;">Tipe TMI</th>
                            <th class="font-14" style="text-align: center;">Kode Margin</th>
                            <th class="font-14" style="text-align: center;">Cabang</th>
                            <th class="font-14" style="text-align: center;">Kategori</th>
                            <th class="font-14" style="text-align: center;">Min(%)</th>
                            <th class="font-14" style="text-align: center;">Saran(%)</th>
                            {{--<th class="font-14" style="text-align: center;">Max(%)</th>--}}
                            {{--<th class="font-14" style="text-align: center;">Aksi</th>--}}
                            {{--<th class="font-14" style="text-align: center;">Aksi</th>--}}
                        </tr>
                        </thead>
                    </table>


                    <div class="modal fade-scale" id="AddMarginTmi" role="dialog">
                        <div class="modal-dialog modal-dialog" style="position:absolute;margin-left: -500px;">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Tambah Master Margin </h4>
                                </div>

                                <div class="modal-body">
                                    <form class="form-horizontal" method="POST" action="">
                                        <input type="text" id="txt_id" hidden>


                                        {{--<div class="form-group">--}}
                                        {{--<label class="col-md-4 control-label">Pilih Cabang TMI <b style="color:red">*</b></label>--}}
                                        {{--<div class="col-md-6">--}}
                                        {{--<select class="js-example-basic-single" name="cabang" id="txt_cab">--}}
                                        {{--{!! $branch !!}--}}
                                        {{--</select>--}}
                                        {{--</div>--}}
                                        {{--</div>--}}

                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Pilih Cabang IGR <b style="color:red">*</b></label>
                                            <div class="col-md-6">
                                                <select class="demo" multiple="multiple" data-width="900px" data-live-search="true" name="multi">
                                                    {!! $branch !!}
                                                </select>
                                                <input type="hidden" id="splu" name="splu"/>
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
                                            <label class="col-md-4 control-label">Divisi  <b style="color:red">*</b></label>
                                            <div class="col-md-6">
                                                <select class="js-example-basic-single" data-width="270px" name="divisi" id="divSelect" onChange= "changeDiv()">
                                                    <option>--Pilih Divisi--</option>
                                                    {!! $divisiOpt !!}
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Departemen  <b style="color:red">*</b></label>
                                            <div class="col-md-6">
                                                <select class="js-example-basic-single" data-width="270px" name="departemen" id="depSelect" onChange= "changeDep()">
                                                    <option>--Pilih Departemen--</option>
                                                    {!! $departemenOpt !!}
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Kategori  <b style="color:red">*</b></label>
                                            <div class="col-md-6">
                                                <select class="js-example-basic-single" data-width="280px" name="kategori" id="katSelect">
                                                    <option>--Pilih Kategori--</option>
                                                    {!! $kategoriOpt !!}
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Harga Min  <b style="color:red">*</b></label>
                                            <div class="col-md-2">
                                                <input type="text" class="form-control flat" maxlength="50" id='txt_min' name="margin_min">
                                            </div>
                                            <div class="col-md-2">
                                                <span style="text-align: right;">%</span>
                                            </div>
                                        </div>

                                        <div class="form-group" style="display: none;">
                                            <label class="col-md-4 control-label">Harga Max <b style="color:red">*</b></label>
                                            <div class="col-md-2">
                                                <input type="text" class="form-control flat" maxlength="50" id='txt_max' name="margin_max">
                                            </div>
                                            <div class="col-md-2">
                                                <span style="text-align: right;">%</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Harga Saran <b style="color:red">*</b></label>
                                            <div class="col-md-2">
                                                <input type="text" class="form-control flat" maxlength="50" id='txt_saran' name="margin_saran">
                                            </div>
                                            <div class="col-md-2">
                                                <span style="text-align: right;">%</span>
                                            </div>
                                        </div>


                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary" id="btn_savemargin">Tambah</button>
                                        </div>
                                    </form>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="modal fade-scale" id="ModalViewCabang" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                                    <h4 class="modal-title">Cabang Berlaku</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="myModal" class="modal fade" role="dialog">
                        <div class="modal-dialog" style="position: absolute; margin-left: -300px;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 style="margin-bottom:0px" id="myModalLabel" class="font-14"></h4>
                                </div>
                                <div id="modal-core" class="modal-body" style='text-align: center'></div>
                                <div id="modal-error" class="modal-footer"><br/></div>
                            </div>
                        </div>
                    </div>


                    <div class="modal fade-scale" id="EditModalMargin" role="dialog">
                        <div class="modal-dialog modal-lg" style="position:absolute;margin-left: -500px;">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Edit Master Margin </h4>
                                </div>
                                <div class="modal-body">
                                    <form class="form-horizontal" method="POST" action="">
                                        <input type="text" id="txt_id" hidden>


                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Pilih Cabang IGR <b style="color:red">*</b></label>
                                            <div class="col-md-6">
                                                <select class="demon" multiple="multiple" data-width="900px" data-live-search="true" name="multi">
                                                    {!! $branch !!}
                                                </select>
                                                <input type="hidden" id="splu" name="splu"/>
                                            </div>
                                        </div>


                                        {{--<div class="form-group">--}}
                                        {{--<label class="col-md-4 control-label">Cabang <b style="color:red">*</b></label>--}}
                                        {{--<div class="col-md-6">--}}
                                        {{--<input type="text" class="form-control flat" id='txtcab' name="txtcab" hidden>--}}
                                        {{--</div>--}}
                                        {{--</div>--}}

                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Pilih Tipe TMI <b style="color:red">*</b></label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control flat" id='txt_tipex' name="tipemember" hidden>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Kategori  <b style="color:red">*</b></label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control flat" id='cat'>
                                            </div>
                                        </div>

                                        <div hidden class="alert alert-info" id="alert_info_min">
                                            <p>Procentase Harga Min tidak boleh lebih besar dari Harga Max !</p>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Harga Min  <b style="color:red">*</b></label>
                                            <div class="col-md-2">
                                                <input type="text" class="form-control flat" maxlength="50" id='txt_minx' name="margin_min">
                                            </div>
                                            <div class="col-md-2">
                                                <span style="text-align: right;">%</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Harga Saran <b style="color:red">*</b></label>
                                            <div class="col-md-2">
                                                <input type="text" class="form-control flat" maxlength="50" id='txt_saranx' name="margin_saran">
                                            </div>
                                            <div class="col-md-2">
                                                <span style="text-align: right;">%</span>
                                            </div>
                                        </div>

                                        <div hidden class="alert alert-info" id="alert_info_max">
                                            <p>Procentase Harga Max tidak boleh lebih kecil dari Harga Min !</p>
                                        </div>

                                        <div class="form-group" style="display: none">
                                            <label class="col-md-4 control-label">Harga Max <b style="color:red">*</b></label>
                                            <div class="col-md-2">
                                                <input type="text" class="form-control flat" maxlength="50" id='txt_maxx' name="margin_max">
                                            </div>
                                            <div class="col-md-2">
                                                <span style="text-align: right;">%</span>
                                            </div>
                                        </div>



                                        <div class="form-group" style="display: none">
                                            <label class="col-md-4 control-label">Berlaku di  <b style="color:red">*</b></label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control flat" id='flagcab'>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary" id="btn_editmrg">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

        </div>
    </div>

    <script>

        var projTable = $('#dtTable').DataTable( {
            dom: "<'row'<'col-xs-12'<'col-xs-6'l><'col-xs-6'p>>r>"+
            "<'row'<'col-xs-12't>>"+
            "<'row'<'col-xs-12'<'col-xs-6'i><'col-xs-6'p>>>",
            processing: true,
            serverSide: true,
            ordering: true,
            searching : true,
            dom: 'Blfrtip',
            autoWidth: false,
            iDisplayLength: 10,
            ajax: {
                url: '{{url('admin/mastermargin/datatable')}}',
                data: function (d) {
                    d.tipemember = $('[name=tipemember]').val();
                    d.tipecabang = $('[name=tipecabang]').val();
                }
            },

            columns: [
                { data: 'tipetmi', name: 'tipetmi'},
                { data: 'kode_mrg', name: 'kode_mrg'},
                { data: 'kode_igr', name: 'kode_igr'},
                { data: 'kat_namakategori', name: 'kat_namakategori'},
                { data: 'margin_min', name: 'margin_min'},
                { data: 'margin_saran', name: 'margin_saran'},
            ],

            scrollX:        true,
            scrollCollapse: true,
            bResetDisplay: false,
            "bStateSave": true,
            fixedColumns : {
                leftColumns: 1
            }
        } );

        $('#search-form').on('submit', function(e) {
            projTable.draw();
            e.preventDefault();
        });


        {{--var table = $('#dtTable').dataTable( {--}}
            {{--ajax: '{{url('admin/mastermargin/datatable')}}',--}}
            {{--"paging": true,--}}
            {{--"lengthChange": true,--}}
            {{--"searching": true,--}}
            {{--"ordering": true,--}}
            {{--"info": true,--}}
            {{--"autoWidth": true,--}}
            {{--"columnDefs": [--}}
                {{--{"className": "dt-center", "targets": "_all"}--}}
            {{--],--}}

            {{--columns: [--}}
                {{--{ data: 'tipetmi', name: 'tipetmi'},--}}
{{--//                { data: 'hrg_jualigr', name: 'hrg_jualigr'},--}}
{{--//                { data: "hrg_jualigr", render: $.fn.dataTable.render.number( ',', '.', 0, 'Rp ' ) },--}}
                {{--{ data: 'kode_igr', name: 'kode_igr'}, --}}
                {{--{ data: 'kat_namakategori', name: 'kat_namakategori'},--}}
                {{--{ data: 'margin_min', name: 'margin_min'},--}}
                {{--{ data: 'margin_saran', name: 'margin_saran'},--}}
{{--//                { data: 'margin_max', name: 'margin_max'},--}}
{{--//                { data: 'aksi', name: 'aksi'},--}}
            {{--]--}}
        {{--} );--}}


        $(document).on('click', '#btn_add_margin', function(){
            var id = $(this).val();

            $("#AddMarginTmi").modal();

        });

        $(document).on('click', '#btn_edit_margin', function(){
            var id = $(this).val();

            $("#txt_id").val(id);
            $("#EditModalMargin").modal();

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
                url:'MarginAjax',
                type : 'POST',
                data : {id : id},
                success:function(msg){

                    msg1 = msg.split("!@#$")[0];

                    msg2 = msg.split("!@#$")[1];

                    msg1 = eval(msg1);
                    msg2 = eval(msg2);

                    var branch = [];

                    $.each(msg2, function() {
                        branch.push(this.kode_igr);
                    });

                    $('.demon').multiselect('deselectAll', false);
                    $('.demon').multiselect('updateButtonText');
                    $('.demon').multiselect('select', branch);

//                    document.getElementById("txt_cabx").disabled = true;
                    document.getElementById("txt_tipex").disabled = true;
                    document.getElementById("cat").disabled = true;
                    document.getElementById("txt_minx").disabled = false;
                    document.getElementById("txt_maxx").disabled = false;
                    document.getElementById("txt_saranx").disabled = false;
                    document.getElementById("flagcab").disabled = false;


//
//                    $("#splu").html("<option style='font-size:12px;' value='"+msg2[0].kode_igr+"'></option>");

                    $("#txt_tipex").val(msg1[0].tipetmi);
                    $("#cat").val(msg1[0].kat_namakategori);
                    $("#txt_minx").val(msg1[0].margin_min);
                    $("#txt_maxx").val(msg1[0].margin_max);
                    $("#txt_saranx").val(msg1[0].margin_saran);
                    $("#flagcab").val(msg1[0].flag_cab);

                }

            });

        });


        $("#btn_editmrg").click(function(){
            var id = $("#txt_id").val();
            var minx = $("#txt_minx").val();
            var maxx = $("#txt_maxx").val();
            var saranx = $("#txt_saranx").val();
            var flagcab = $("#flagcab").val();

            var splu = $("#splu").val();

            if(minx >= maxx){
                $("#alert_info_min").show();
            }else{
//            if(maxx > minx){
//                $("#alert_info_max").show();
//            }else
//            {
                $.ajax({
                    url: 'editmargin',
                    type : 'POST',
                    data : {
                        id : id,
                        splu : splu,
                        minx : minx,
                        maxx : maxx,
                        saranx : saranx,
                        flagcab : flagcab
                    },
                    success:function(msg){
//                    alert("Data berhasil diubah.");
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

                        $('#EditModalMargin').modal('hide');

//                    location.reload();
                    }
                });
//            }

            }
        });

        $(document).on('click', '#btn_cabang_ongkir', function(){
            var id = $(this).val();

//            $("#txt_id").val(id);
            $("#myModal").modal();

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
                type: "GET",
                url: getCabangOngkirURL,
                data : {id : id},
                success:function(data){
                    if(data == undefined || data == ""){
                        $('#modal-core').html('Gagal Memuat Konfirmasi');
                    }else{
                        $('#modal-core').html($(data));
                    }
                }

            });

        });

        function changeDiv(){
            var divSelect = $("#divSelect").val();
            $.ajax({
                url : '{{ url('changedep') }}',
                data: { div: divSelect },
                success : function(data) {
                    $("#depSelect").html(data);
                    $("#depSelect").js-example-basic-single('refresh');
                }
            });
        }

        function changeDep(){
            var depSelect = $("#depSelect").val();
            $.ajax({
                url : '{{ url('changekat')}}',
                data: {dep: depSelect},
                success : function(data){
                    $("#katSelect").html(data);
                    $("#katSelect").js-example-basic-single('refresh');
                }
            });
        }

        $("#btn_savemargin").click(function(){
            var id = $("#txt_id").val();
            var splu = $("#splu").val();
            var tipe = $("#txt_tipe").val();
            var div = $("#divSelect").val();
            var dep = $("#depSelect").val();
            var kat = $("#katSelect").val();
            var min = $("#txt_min").val();
            var max = $("#txt_max").val();
            var saran = $("#txt_saran").val();
//            var alamat = $("#alamat").val();

            if(splu == "" || tipe == "" || div == "" || dep == "" || kat == "" || min == "" || max == "" || saran == ""){
                alert("Silahkan lengkapi data!");
            }else{
                $.ajax({
                    url: 'addmastermargin',
                    type : 'POST',
                    data : {
                        id : id,
                        splu : splu,
                        tipe : tipe,
                        div : div,
                        dep : dep,
                        kat :kat,
                        min :min,
                        max :max,
                        saran :saran
                    },
                    success:function(msg){
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
                        $('#AddMarginTmi').modal('hide');

                    }
                });
            }
//            }
        });



    </script>
@endsection
