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
                                <div class="col-md-12">
                                    <table class="datatable table table-striped table-bordered responsive" id="dtTable">
                                        <thead>
                                        <tr>
                                            <th class="font-14" style="text-align: center;">Tanggal</th>
                                            <th class="font-14" style="text-align: center;">Total</th>
                                            <th class="font-14" style="text-align: center;">Margin</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th></th>
                                            <th id="ftotal" style="color: black; text-align: right;"></th>
                                            <th id="mtotal" style="color: black; text-align: right;"></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form id="exportform" action="exportformsales" method="post" target="_blank">
                        <input type="text" name="_token" value="{{ csrf_token() }}" hidden>
                        <input type="text" id="efcabang" name="efcabang" hidden>
                        <input type="text" id="eftoko" name="eftoko" hidden>
                        <input type="text" id="efkasir" name="efkasir" hidden>
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
                requestSales();
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
                changeToko();
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
                        changeToko();
                    }
                });
            }
        }

        function changeToko()
        {
            var toko = $('#scopetoko').val();
            if(toko === "%")
            {
                $('#scopekasir').empty().append("<option style='font-size: 12px;' value='%' selected>SEMUA KASIR</option>");
                $('#scopekasir').selectpicker('refresh');
                requestSales();
            }
            else
            {
                $.ajax({
                    url:'getcashierofstore',
                    type : 'POST',
                    data : {
                        '_token' : '{{ csrf_token() }}',
                        'store' : toko
                    },
                    dataType : 'html',
                    success:function(response)
                    {
                        $('#scopekasir').empty().append(response);
                        $('#scopekasir').selectpicker('refresh');
                        requestSales();
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
                requestSales();
            }
        }

        function numberWithCommas(x) {
            var parts = x.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return parts.join(".");
        }

        function requestSales()
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
                autoWidth: false,
                ajax: {
                    url: 'getsalesdatatable',
                    data: function (d) {
                        d.branch = $('#scopecabang').val();
                        d.store = $('#scopetoko').val();
                        d.cashier = $('#scopekasir').val();
                        d.date = date;
                        d.startdate = startdate;
                        d.enddate = enddate;
                    }
                },
                columns: [
                    { data: 'branch', name: 'branch'},
                    { data: 'store', name: 'store'},
                    { data: 'cashier', name: 'cashier'},
                    { data: 'invoice', name: 'invoice'},
                    { data: 'date', name: 'date'},
                    { data: 'total', name: 'total',className: "text-right",render: $.fn.dataTable.render.number( '.', '.', 0 )},
                    { data: 'margin', name: 'margin',className: "text-right",render: $.fn.dataTable.render.number( '.', '.', 0 )}
                ],
                "columnDefs": [
                    {
                        "targets": [5,6],
                        "width": '1%'
                    }
                ],
                order: [4,'asc'],
                bResetDisplay: true,
                "bStateSave": true,
                "bDestroy": true,
                fixedColumns : {
                    leftColumns: 1
                },
                drawCallback : function(setting, json) {
                    var data = setting.json;
                    document.getElementById('ftotal').innerHTML = numberWithCommas(data.totalt);
                    document.getElementById('mtotal').innerHTML = numberWithCommas(data.totalm);
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
    <style>
        .newexportbutton{
            margin-top: 20px;
        }
    </style>
@endsection