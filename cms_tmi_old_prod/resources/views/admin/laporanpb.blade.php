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
                                    <select name="scopetoko" id="scopetoko" class="selectpicker form-control" data-live-search="true" onchange="requestPb()">
                                        <option style='font-size: 12px;' value='%' selected>SEMUA TOKO</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="selectpicker form-control invisible">
                                        <option style='font-size: 12px;' value='%' selected>SEMUA KASIR</option>
                                    </select>
                                    <div id="containertahun" style="display:none;">
                                        <select name="scopetahun" id="scopetahun" class="selectpicker form-control" data-live-search="true" onchange="requestPb()">
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
                                        <select name="scopebulan" id="scopebulan" class="selectpicker form-control" data-live-search="true" onchange="requestPb()">
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
                                                <th class="font-14" style="text-align: center;">Nomor PO</th>
                                                <th class="font-14" style="text-align: center;">Tanggal</th>
                                                <th class="font-14" style="text-align: center;">Free Ongkir</th>
                                                <th class="font-14" style="text-align: center;">Status</th>
{{--                                                <th class="font-14" style="text-align: center;">Pemesan</th>--}}
{{--                                                <th class="font-14" style="text-align: center;">No. Telp</th>--}}
{{--                                                <th class="font-14" style="text-align: center;">Alamat</th>--}}
                                                <th class="font-14" style="text-align: center;">Jumlah Item<br>(terealisasi / order)</th>
                                                <th class="font-14" style="text-align: center;">Jumlah Qty<br>(terealisasi / order)</th>
                                                <th class="font-14" style="text-align: center;">Harga<br>(terealisasi / order)</th>
{{--                                                <th class="font-14" style="text-align: center;">Free Ongkir</th>--}}
{{--                                                <th class="font-14" style="text-align: center;">Status</th>--}}
{{--                                                <th class="font-14" style="text-align: center;">Terkirim</th>--}}
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
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
{{--                                                <th></th>--}}
{{--                                                <th></th>--}}
{{--                                                <th></th>--}}
                                            </tr>
                                        </tfoot>
                                    </table>

{{--                                    <table style="width: 100%; visibility: hidden" id="footer_table">--}}
{{--                                        <tr>--}}
{{--                                            <th>Quantity</th>--}}
{{--                                            <td>x PCS</td>--}}
{{--                                        </tr>--}}
{{--                                        <tr>--}}
{{--                                            <th>Plu</th>--}}
{{--                                            <td>x PCS</td>--}}
{{--                                        </tr>--}}
{{--                                        <tr>--}}
{{--                                            <th>Sales</th>--}}
{{--                                            <td>Rupiah</td>--}}
{{--                                        </tr>--}}
{{--                                    </table>--}}
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
                requestPb();
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
                requestPb();
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
                        requestPb();
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
                requestPb();
            }
        }

        function numberWithCommas(x) {
            var parts = x.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return parts.join(".");
        }

        function requestPb()
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
                        extend: 'excel',
                        text: 'Export Excel To Excel',
                        exportOptions: {
                            modifier: {
                                selected: null
                            }
                        }
                    
                    },
                    // 'copy', 'csv', 'excel', 'pdf', 'print'

                    {
                        text: 'Export Excel By Code',
                        className: "newexportbutton",
                        action: function ( e, dt, node, config ) {
                            exportIt();
                        }
                    }
                ],
                processing: true,
                // serverSide: true,
                ordering: true,
                searching : true,
                autoWidth: true,
                ajax: {
                    url: 'getpbdatatable',
                    data: function (d) {
                        d.branch = $('#scopecabang').val();
                        d.store = $('#scopetoko').val();
                        d.date = date;
                        d.startdate = startdate;
                        d.enddate = enddate;
                    }
                },
                columns: [
                    { data: 'branch', name: 'branch'},
                    { data: 'store', name: 'store'},
                    { data: 'po_no', name: 'po_no'},
                    { data: 'po_date', name: 'po_date'},
                    { data: 'isfree', name: 'date'},
                    { data: 'status', name: 'date'},
                    // { data: 'name', name: 'name'},
                    // { data: 'phone', name: 'phone'},
                    // { data: 'address', name: 'address'},
                    { data: null, name: 'item', className: 'text-right', render: function (data, type, row) {
                        return row.itemf + " / " + row.itemo +
                                "("+(parseInt(row.itemf*10000/row.itemo)/100)+"%)";
                        }},
                    { data: null, name: 'qty', className: 'text-right', render: function ( data, type, row ) {
                            return row.qtyf+" / "+row.qtyo +
                                " ("+(parseInt(row.qtyf*10000/row.qtyo)/100)+"%)";
                        }
                    },
                    { data: null, name: 'price', className: 'text-right', render: function ( data, type, row ) {
                            return "Rp"+numberWithCommas(row.pricef)+" / Rp"+numberWithCommas(row.priceo) +
                                " ("+(parseInt(row.pricef*10000/row.priceo)/100)+"%)";
                        }
                    },
                    // { data: 'isfree', name: 'date'},
                    // { data: 'status', name: 'date'},
                ],
                "columnDefs": [
                    {
                        "targets": [7,8],
                        "width": '1%',
                        "className": "dt-center"
                    },
                    {
                        "targets": [5,6],
                        "width": '1%'
                    }
                ],
                order: [2,'asc'],
                bResetDisplay: true,
                "bStateSave": true,
                "bDestroy": true,
                fixedColumns : {
                    leftColumns: 1
                },
                "rowCallback": function( row, data ) {
                    var last_position_column = projTable.columns().header().length-1;
                    if ( data.itemf >= data.itemo ){
                        $('td:eq('+(last_position_column-2)+')', row)
                            .css('background-color', 'rgba(152,251,152, 0.2)')
                            .css('color', 'green');
                    } else{
                        $('td:eq('+(last_position_column-2)+')', row)
                            .css('background-color','rgba(255,192,203, 0.2)')
                            .css('color', 'red');
                    }
                    if ( data.qtyf >= data.qtyo ) {
                        $('td:eq('+(last_position_column-1)+')', row)
                            .css('background-color','rgba(152,251,152, 0.2)')
                            .css('color', 'green');
                    }
                    else
                    {
                        $('td:eq('+(last_position_column-1)+')', row)
                            .css('background-color','rgba(255,192,203, 0.2)')
                            .css('color', 'red');
                    }

                    if ( data.pricef >= data.priceo ) {
                        $('td:eq('+(last_position_column)+')', row)
                            .css('background-color','rgba(152,251,152, 0.2)')
                            .css('color', 'green')
                        ;
                    } else {
                        $('td:eq('+(last_position_column)+')', row)
                            .css('background-color','rgba(255,192,203, 0.2)')
                            .css('color', 'red');
                    }


                    // if ( data.issent === 1 ) {
                    //     $('td:eq('+last_position_column+')', row).html('&#x2714');
                    // }
                    // else
                    // {
                    //     $('td:eq('+last_position_column+')', row).html('&#x2718');
                    // }
                },
                "footerCallback": function( row, data, start, end, display ){

                    var quantity_order = 0;
                    var quantity_fulfilled = 0;
                    var plu_order = 0;
                    var plu_fulfilled = 0;
                    var price_order = 0;
                    var price_fulfilled = 0;
                    data.forEach(function (item, index) {
                        quantity_order += item['qtyo'];
                        quantity_fulfilled += item['qtyf'];

                        plu_order += item['itemo'];
                        plu_fulfilled += item['itemf'];

                        price_order += item['priceo'];
                        price_fulfilled += item['pricef'];
                    })


                    //quantity, plu, rupiah
                    // console.log(quantity_order + " | " + quantity_fulfilled);
                    // console.log(plu_order + " | " + plu_fulfilled);
                    // console.log(price_order + " | " + price_fulfilled);
                    // var last_position_column = projTable.columns().header().length-1;
                    //                                " ("+(parseFloat((row.pricef/row.priceo).toFixed(2))*100)+"%)";

                    var api = this.api();
                    $(api.column(0).footer()).html("Total");
                    $(api.column(6).footer()).html(
                        plu_fulfilled+" / " + plu_order +
                        " ("+parseInt(plu_fulfilled*100/plu_order)+"%)"
                    )

                    $(api.column(7).footer()).html(
                        quantity_fulfilled+" / " + quantity_order +
                        " ("+parseInt(quantity_fulfilled*100/quantity_order)+"%)"
                    )

                    $(api.column(8).footer()).html(
                        "Rp" + numberWithCommas(price_fulfilled)+" / Rp" + numberWithCommas(price_order) +
                        " ("+parseInt(price_fulfilled*100/price_order)+"%)"
                    )
                },
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