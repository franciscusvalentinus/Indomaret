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
                <section class="panel">
                    <div class="panel-body progress-panel">
                        <div class="row">
                            <div class="col-lg-8 task-progress pull-left">
                                <h1>Laporan Sales</h1>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-3 control-label">Pilih Cabang</label>

                                <label class="col-md-3 control-label">Pilih Toko</label>

                                <label class="col-md-3 control-label"></label>

                                <label class="col-md-3 control-label">Pilih Jangka Waktu</label>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="scopecabang" id="scopecabang" class="selectpicker form-control" data-live-search="true" onchange="changeCabang()">
                                        {!! $optionbranch !!}
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="scopetoko" id="scopetoko" class="selectpicker form-control" data-live-search="true" onchange="requestPromo()">
                                        <option style='font-size: 12px;' value='%' selected>SEMUA TOKO</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
{{--                                    <select name="scopekasir" id="scopekasir" class="selectpicker form-control" data-live-search="true" onchange="requestPromo()">--}}
{{--                                        <option style='font-size: 12px;' value='%' selected>SEMUA KASIR</option>--}}
{{--                                        --}}{{--Append goes here--}}
{{--                                    </select>--}}
                                    <div id="containertahun" style="display:none;">
                                        <select name="scopetahun" id="scopetahun" class="selectpicker form-control" data-live-search="true" onchange="requestPromo()">
                                            <option style='font-size: 12px;' value='%-' selected>SEMUA TAHUN</option>
                                            {!! $tahun !!}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select name="scopehari" id="scopehari" class="selectpicker form-control" data-live-search="true" onchange="changeHari()">
                                        <option style='font-size: 12px;' value='%'>ALL TIME</option>
{{--                                        <option style='font-size: 12px;' value='yearmonth'>TAHUN / BULAN</option>--}}
                                        <option style='font-size: 12px;' value='daterange'>ANTARA 2 TANGGAL</option>
                                    </select>
                                    <div id="containerbulan" style="display:none;">
                                        <select name="scopebulan" id="scopebulan" class="selectpicker form-control" data-live-search="true" onchange="requestPromo()">
                                            <option style='font-size: 12px;' value='%-%' selected>SEMUA BULAN</option>
                                            <option style='font-size: 12px;' value='01-%'>JANUARI</option>
                                            <option style='font-size: 12px;' value='02-%'>FEBRUARI</option>
                                            <option style='font-size: 12px;' value='03-%'>MARET</option>
                                            <option style='font-size: 12px;' value='04-%'>APRIL</option>
                                            <option style='font-size: 12px;' value='05-%'>MEI</option>
                                            <option style='font-size: 12px;' value='06-%'>JUNI</option>
                                            <option style='font-size: 12px;' value='07-%'>JULI</option>
                                            <option style='font-size: 12px;' value='08-%'>AGUSTUS</option>
                                            <option style='font-size: 12px;' value='09-%'>SEPTEMBER</option>
                                            <option style='font-size: 12px;' value='10-%'>OKTOBER</option>
                                            <option style='font-size: 12px;' value='11-%'>NOVEMBER</option>
                                            <option style='font-size: 12px;' value='12-%'>DESEMBER</option>
                                        </select>
                                    </div>
                                    <input type="text" class="form-control" id="daterange" name="daterange" style="display:none;" autocomplete="off" value="" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="datatable table table-striped table-bordered responsive" id="dtTable">
                                        <thead>
                                            <tr>
                                                <th class="font-14" style="text-align: center;">Cabang</th>
                                                <th class="font-14" style="text-align: center;">Toko</th>
                                                <th class="font-14" style="text-align: center;">Nama Promo</th>
                                                <th class="font-14" style="text-align: center;">Keterangan</th>
                                                <th class="font-14" style="text-align: center;">Masa Aktif</th>
                                                <th class="font-14" style="text-align: center;">Quantity Terjual</th>
                                                <th class="font-14" style="text-align: center;">Biaya Promosi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function() {

            $('input[name="daterange"]').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                requestPromo();
            });

            $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

        });

        $( document ).ready(function() {
            changeCabang();
        });

        function changeCabang()
        {
            var cab = $('#scopecabang').val();
            if(cab === "%")
            {
                $('#scopetoko').empty().append("<option style='font-size: 12px;' value='%' selected>SEMUA TOKO</option>");
                $('#scopetoko').selectpicker('refresh');
                requestPromo();
            }
            else
            {
                $.ajax({
                    url:'getstoreofbranch',
                    type : 'POST',
                    data : {
                        '_token' : '{{ csrf_token() }}',
                        'branch' : cab
                    },
                    dataType : 'html',
                    success:function(response)
                    {
                        $('#scopetoko').empty().append(response);
                        $('#scopetoko').selectpicker('refresh');
                        requestPromo();
                    }
                });
            }
        }

        function changeHari()
        {
            var day = $('#scopehari').val();
            if(day === "daterange")
            {
                $('#containertahun').css('display','none');
                $('#containerbulan').css('display','none');
                $('#daterange').css('display','');
            }
            // else if(day === "yearmonth")
            // {
            //     $('#containertahun').css('display','');
            //     $('#containerbulan').css('display','');
            //     $('#daterange').css('display','none');
            // }
            else
            {
                $('#containertahun').css('display','none');
                $('#containerbulan').css('display','none');
                $('#daterange').css('display','none');
                requestPromo();
            }
        }

        function numberWithCommas(x) {
            var parts = x.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return parts.join(".");
        }

        function requestPromo()
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
            // else if ( date === "yearmonth" )
            // {
            //     date = $('#scopetahun').val() + $('#scopebulan').val();
            //     startdate = '%';
            //     enddate = '%';
            // }
            var projTable = $('#dtTable').DataTable( {
                dom: "<'row'<'col-sm-4'l><'col-sm-4'><'col-sm-4'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                processing: true,
                serverSide: true,
                ordering: true,
                searching : true,
                autoWidth: false,
                ajax: {
                    url: 'getpromodatatable',
                    data: function (d) {
                        d.branch = $('#scopecabang').val();
                        d.store = $('#scopetoko').val();
                        d.date = date;
                        d.startdate = startdate;
                        d.enddate = enddate;
                    }
                },
                //Quantity Terjual
                // Biaya Promosi
                columns: [
                    { data: 'branch', name: 'branch'},
                    { data: 'store', name: 'store'},
                    { data: 'promo', name: 'promo'},
                    { data: null, name: 'desc', render: function ( data, type, row ) {
                            return "Beli ("+row.name+") ("+row.min+") buah mendapat potongan Rp"+numberWithCommas(row.disc);
                        }
                    },
                    { data: null, name: 'date',className: "text-right", render: function ( data, type, row ) {
                            return row.startdate+" s/d "+row.enddate;
                        }
                    },
                    { data: 'sold_quantity'},
                    { data: 'cost_promotion'}
                ],
                bResetDisplay: true,
                "bStateSave": true,
                "bDestroy": true,
                fixedColumns : {
                    leftColumns: 1
                }
            } );
            projTable.draw();
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
            $('#efkasir').val($('#scopekasir').val());
            $('#efhari').val(date);
            $('#efstart').val(startdate);
            $('#efend').val(enddate);

            form.submit();
        }
    </script>
@endsection