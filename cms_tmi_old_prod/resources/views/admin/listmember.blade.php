@extends('dashboard')
@section('content')

    <style>
        .table-striped tr:nth-child(odd) td,
        .table-striped tr:nth-child(odd) th {
            background-color: #dfe6e9;
        }
    </style>
    <div class="container-fluid" style="margin-top: 100px;padding-left: 300px;padding-right: 200px;">
        <div class="row">
            <div class="col-md-12">
                <!--Project Activity start-->
                <section class="panel">
                    <div class="panel-body progress-panel">
                        <div class="row">
                            <div class="col-lg-8 task-progress pull-left">
                                <h1>Daftar PLU TMI</h1>
                            </div>
                        </div>
                    </div>

                    {{--<div class="panel-body">--}}
                    {{--<button type="button" id="btn_add_plu" class="btn btn-primary btn-lg btn-block flat">Tambah Plu Baru</button>--}}
                    {{--</div>--}}

                    {{--<div class="divfilter">--}}

                    {{--</div>--}}
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

                    <div class="table table-hover">
                        <table class="datatable table table-striped table-bordered" id="dtTable">
                            <thead>
                            <tr>
                                <th class="font-14" style="text-align: center;">Tipe TMI</th>
                                <th class="font-14" style="text-align: center;">Kategori</th>
                                <th class="font-14" style="text-align: center;">Display</th>
                                <th class="font-14" style="text-align: center;">Kode PLU</th>
                                <th class="font-14" style="text-align: center;">Deskripsi</th>
                                <th class="font-14" style="text-align: center;">Frac</th>
                                <th class="font-14" style="text-align: center;">Unit</th>
                                <th class="font-14" style="text-align: center;">Min(%)</th>
                                <th class="font-14" style="text-align: center;">Saran(%)</th>

                                {{--<th class="font-14" style="text-align: center;">Min Display</th>--}}
                                {{--<th class="font-14" style="text-align: center;">Aksi</th>--}}
                                {{--<th class="font-14" style="text-align: center;">Aksi</th>--}}
                            </tr>
                            </thead>
                        </table>
                    </div>

                </section>
            </div>

        </div>

        <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" style="position: absolute">
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Konfirmasi Penghapusan</h4>
                    </div>

                    <div class="modal-body">
                        <p>Anda akan menghapus PLU TMI, aksi ini tidak dapat dibatalkan.</p>
                        <p>Apakah anda yakin ingin melanjutkan?</p>
                    </div>

                    <div class="modal-footer">
                        <a class="btn btn-danger btn-ok"><i class="fa fa-times"></i> Delete</a>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade-scale" id="AddModalPlu" role="dialog">
            <div class="modal-dialog modal-lg" style="position:absolute;margin-left: -500px;">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Tambah Plu Tmi </h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" method="POST" action="">
                            <input type="text" id="txt_id" hidden>

                            <div class="modal-header">
                                <h4 class="modal-title">Informasi Produk </h4>

                                <div class="col-md-12 input-group inputs">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" style="min-width: 142px;text-align: left" type="button">Cari Plu IGR</button>
                                </span>
                                    <input id="plu" type="text" style="color: red" class="form-control" name="plu" placeholder="Masukkan Plu Lalu Enter...">
                                </div>

                                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                    <span class="input-group-addon" style="min-width: 142px;text-align: left">Desc</span>
                                    <input type="text" name="desc" min="1" class="input-sm form-control" id="desc">
                                </div>
                                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                    <span class="input-group-addon" style="min-width: 142px;text-align: left">Kategori</span>
                                    <input type="text" name="cat" min="1" class="input-sm form-control" id="cat">
                                </div>
                                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                    <span class="input-group-addon" style="min-width: 142px;text-align: left">Unit</span>
                                    <input type="text" name="unit" min="1" class="input-sm form-control" id="unit">
                                </div>
                                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                    <span class="input-group-addon" style="min-width: 142px;text-align: left">Frac</span>
                                    <input type="text" name="frac" min="1" class="input-sm form-control" id="frac">
                                </div>
                                {{--<div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">--}}
                                {{--<span class="input-group-addon" style="min-width: 142px;text-align: left">Display</span>--}}
                                {{--<input type="text" name="display" min="1" class="input-sm form-control" id="display">--}}
                                {{--</div>--}}
                                <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                    <span class="input-group-addon" style="min-width: 142px;text-align: left">Tag</span>
                                    <input type="text" name="tag" min="1" class="input-sm form-control" id="tag">
                                </div>
                            </div>

                            <div class="modal-header">
                                <h4 class="modal-title">Setting TMI </h4>

                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th scope="col">Default/Saran</th>
                                        <th scope="col">RPH</th>
                                        <th scope="col">MGN/MUP</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th scope="row">Harga Jual</th>
                                        <td><input type="text" name="frac" min="1" class="input-sm form-control" id="hrgsaran"></td>
                                        <td><input type="text" name="frac" min="1" class="input-sm form-control" id="saran"></td>

                                    </tr>
                                    <tr>
                                        <th scope="row">Harga Min</th>
                                        <td><input type="text" name="frac" min="1" class="input-sm form-control" id="hrgmin"></td>
                                        <td><input type="text" name="frac" min="1" class="input-sm form-control" id="min"></td>

                                    </tr>
                                    <tr>
                                        <th scope="row">Harga Max</th>
                                        <td><input type="text" name="frac" min="1" class="input-sm form-control" id="hrgmax"></td>
                                        <td><input type="text" name="frac" min="1" class="input-sm form-control" id="max"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="btn_addplu">Tambah</button>
                            </div>

                        </form>
                    </div>

                </div>

            </div>
        </div>


        <div class="modal fade-scale" id="EditModalPlu" role="dialog">
            <div class="modal-dialog modal-lg" style="position:absolute;margin-left: -200px;">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit Plu Tmi </h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" method="POST" action="">
                            <input type="text" id="txt_id" hidden>

                            <div class="input-group input-group-lg" style="padding-bottom: 20px;">
                                <span class="input-group-addon" id="sizing-addon1">Toko</span>
                                <input type="text" class="form-control" id="toko" type="text" name="toko">
                            </div>

                            <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                <span class="input-group-addon" style="min-width: 142px;text-align: left">Kode plu</span>
                                <input type="text" name="plu" min="1" class="input-sm form-control" id="plux">
                            </div>

                            <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                <span class="input-group-addon" style="min-width: 142px;text-align: left">Harga</span>
                                <input type="number" name="harga" min="1" class="input-sm form-control" id="hargax">
                            </div>
                            <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                <span class="input-group-addon" style="min-width: 142px;text-align: left">Desc</span>
                                <input type="text" name="desc" min="1" class="input-sm form-control" id="descx">
                            </div>
                            <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                <span class="input-group-addon" style="min-width: 142px;text-align: left">Unit</span>
                                <input type="text" name="unit" min="1" class="input-sm form-control" id="unitx">
                            </div>
                            <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                <span class="input-group-addon" style="min-width: 142px;text-align: left">Frac</span>
                                <input type="text" name="frac" min="1" class="input-sm form-control" id="fracx">
                            </div>
                            <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                <span class="input-group-addon" style="min-width: 142px;text-align: left">Display</span>
                                <input type="text" name="display" min="1" class="input-sm form-control" id="displayx">
                            </div>
                            <div class="col-md-12 input-group inputs" style="margin-top: 10px; margin-bottom: 10px;">
                                <span class="input-group-addon" style="min-width: 142px;text-align: left">Tag</span>
                                <input type="text" name="tag" min="1" class="input-sm form-control" id="tagx">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="btn_saveplu">Simpan</button>
                            </div>

                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <script>
        var projTable = $('#dtTable').DataTable( {
            dom: "<'row'<'col-xs-12'<'col-xs-6'l><'col-xs-6'p>>r>"+
            "<'row'<'col-xs-12't>>"+
            "<'row'<'col-xs-12'<'col-xs-6'i><'col-xs-6'p>>>",
            processing: true,
            serverSide: false,
            ordering: true,
            searching : true,
            dom: 'Blfrtip',
            autoWidth: false,
            iDisplayLength: 10,
            ajax: {
                url: '{{url('admin/plu/datatable')}}',
                data: function (d) {
                    d.tipemember = $('[name=tipemember]').val();
                    d.tipecabang = $('[name=tipecabang]').val();
                }
            },

            columns: [
                { data: 'tipetmi', name: 'tipetmi'},
                { data: 'kat_namakategori', name: 'kat_namakategori'},
                { data: 'display', name: 'display'},
                { data: 'kodeplu', name: 'kodeplu'},
                { data: 'long_description', name: 'long_description'},
                { data: 'frac_tmi', name: 'frac_tmi'},
                { data: 'unit_tmi', name: 'unit_tmi'},
                { data: 'margin_min', name: 'margin_min'},
                { data: 'margin_saran', name: 'margin_saran'}
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



        $(document).on('click', '#btn_edit_plu', function(){
            var id = $(this).val();

            $("#txt_id").val(id);
            $("#EditModalPlu").modal();

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
                url:'pluAjax',
                type : 'POST',
                data : {id : id},
                success:function(msg){

                    document.getElementById("toko").disabled = true;
                    document.getElementById("plux").disabled = true;
                    document.getElementById("hargax").disabled = false;
                    document.getElementById("descx").disabled = true;
                    document.getElementById("unitx").disabled = true;
                    document.getElementById("fracx").disabled = true;
                    document.getElementById("displayx").disabled = true;
                    document.getElementById("tagx").disabled = true;
                    document.getElementById("hrgsaran").disabled = true;
                    document.getElementById("persensaran").disabled = true;
                    document.getElementById("hrgmin").disabled = true;
                    document.getElementById("persenmin").disabled = true;
                    document.getElementById("hrgmax").disabled = true;
                    document.getElementById("persenmax").disabled = true;


                    $("#plux").val(msg[0]['kodeplu']);
                    $("#hrgsaran").val(msg[0]['hrg_jualigr']);
                    $("#descx").val(msg[0]['description']);
                    $("#unitx").val(msg[0]['unit_igr']);
                    $("#fracx").val(msg[0]['frac_igr']);
                    $("#displayx").val(msg[0]['display']);
                    $("#tagx").val(msg[0]['tag']);
                    $("#toko").val(msg[0]['nama']);

                }

            });

        });


        $(document).on('click', '#btn_add_plu', function(){
            var id = $(this).val();

//            $("#txt_id").val(id);
            $("#AddModalPlu").modal();

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
                url:'pluAjax',
                type : 'POST',
//                data : {id : id},
                success:function(msg){

                    $("#plu").val(msg[0]['kodeplu']);
                    $("#harga").val(msg[0]['hrg_jualigr']);
                    $("#desc").val(msg[0]['description']);
                    $("#cat").val(msg[0]['KAT_NAMAKATEGORI']);
                    $("#unit").val(msg[0]['unit_igr']);
                    $("#frac").val(msg[0]['frac_igr']);
//                    $("#display").val(msg[0]['display']);
                    $("#tag").val(msg[0]['tag']);
                    $("#toko").val(msg[0]['nama']);

                }

            });

        });

        $("#btn_saveplu").click(function(){
            var id = $("#txt_id").val();
            var hrg_jualigr = $("#hargax").val();

            $.ajax({
                url: 'editplu',
                type : 'POST',
                data : {
                    id : id,
                    hrg_jualigr : hrg_jualigr,
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

                    $('#EditModalPlu').modal('hide');

//                    location.reload();
                }
            });
//            }
        });

    </script>
@endsection
