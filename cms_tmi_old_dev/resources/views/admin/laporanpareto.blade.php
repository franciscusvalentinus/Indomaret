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

                                <label class="col-md-3 control-label">Pilih Tipe</label>

                                <label class="col-md-3 control-label">Pilih Jangka Waktu</label>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="scopecabang" id="scopecabang" class="selectpicker form-control" data-live-search="true" onchange="changeCabang()">
                                        {!! $optionbranch !!}
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="scopetoko" id="scopetoko" class="selectpicker form-control" data-live-search="true">
                                        <option style='font-size: 12px;' value='%' selected>SEMUA TOKO</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="scopetipe" id="scopetipe" class="selectpicker form-control" data-live-search="true">
                                        <option style='font-size: 12px;' value='cnt' selected>BY JUMLAH TRANSAKSI</option>
                                        <option style='font-size: 12px;' value='qty'>BY JUMLAH TERJUAL (QTY)</option>
                                        <option style='font-size: 12px;' value='prc'>BY HARGA TOTAL TERJUAL (Rp)</option>
                                    </select>
                                    <div id="containertahun" style="display:none;">
                                        <select name="scopetahun" id="scopetahun" class="selectpicker form-control" data-live-search="true">
                                            <option style='font-size: 12px;' value='%-' selected>SEMUA TAHUN</option>
                                            {!! $tahun !!}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select name="scopehari" id="scopehari" class="selectpicker form-control" data-live-search="true" onchange="changeHari()">
                                        <option style='font-size: 12px;' value='%'>ALL TIME</option>
                                        <option style='font-size: 12px;' value='yearmonth'>TAHUN / BULAN</option>
                                        <option style='font-size: 12px;' value='daterange'>ANTARA 2 TANGGAL</option>
                                    </select>
                                    <div id="containerbulan" style="display:none;">
                                        <select name="scopebulan" id="scopebulan" class="selectpicker form-control" data-live-search="true">
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
                                <div class="col-md-3">
                                    <button onclick="requestPareto()">Cari</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="datatable table table-striped table-bordered responsive" id="dtTable">
                                        <thead>
                                            <tr>
                                                <th class="font-14" style="text-align: center;">Cabang</th>
                                                <th class="font-14" style="text-align: center;">Toko</th>
                                                <th class="font-14" style="text-align: center;">Produk</th>
                                                <th class="font-14" style="text-align: center;">Total</th>
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
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form id="exportform" action="exportformpareto" method="post" target="_blank">
                        <input type="text" name="_token" value="{{ csrf_token() }}" hidden>
                        <input type="text" id="efcabang" name="efcabang" hidden>
                        <input type="text" id="eftoko" name="eftoko" hidden>
                        <input type="text" id="eftipe" name="eftipe" hidden>
                        <input type="text" id="efhari" name="efhari" hidden>
                        <input type="text" id="efstart" name="efstart" hidden>
                        <input type="text" id="efend" name="efend" hidden>
                    </form>
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
                //requestPareto();
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
                //requestPareto();
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
                        //requestPareto();
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
            else if(day === "yearmonth")
            {
                $('#containertahun').css('display','');
                $('#containerbulan').css('display','');
                $('#daterange').css('display','none');
            }
            else
            {
                $('#containertahun').css('display','none');
                $('#containerbulan').css('display','none');
                $('#daterange').css('display','none');
                //requestPareto();
            }
        }

        function requestPareto()
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
            var projTable = $('#dtTable').DataTable( {
                dom: "<'row'<'col-sm-4'l><'col-sm-4'B><'col-sm-4'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [
                    {
                        text: 'Export Excel',
                        className: "newexportbutton",
                        action: function ( e, dt, node, config ) {
                            exportIt();
                        }
                    }
                ],
                processing: true,
                serverSide: true,
                ordering: true,
                searching : true,
                autoWidth: true,
                ajax: {
                    url: 'getparetodatatable',
                    data: function (d) {
                        d.branch = $('#scopecabang').val();
                        d.store = $('#scopetoko').val();
                        d.type = $('#scopetipe').val();
                        d.date = date;
                        d.startdate = startdate;
                        d.enddate = enddate;
                    }
                },
                columns: [
                    { data: 'branch', name: 'branch'},
                    { data: 'store', name: 'store'},
                    { data: 'product', name: 'product'},
                    { data: 'total', name: 'total', className: "text-right", render: $.fn.dataTable.render.number( '.', '.', 0 )}
                ],
                "columnDefs": [
                    {
                        "targets": [3],
                        "width": '1%'
                    }
                ],
                order: [3,'desc'],
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
            $('#eftipe').val($('#scopetipe').val());
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