@extends('dashboard')
@section('content')

    <style>
        .table-striped tr:nth-child(odd) td,
        .table-striped tr:nth-child(odd) th {
            background-color: #dfe6e9;
        }
    </style>
    <div class="container-fluid" style="margin-top: 100px;padding-left: 250px;padding-right: 5%;">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <div class="panel-body progress-panel">
                        <div class="row">
                            <div class="col-lg-8 task-progress pull-left">
                                <h1>Laporan PNL</h1>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-3 control-label">Pilih Cabang</label>

                                <label class="col-md-3 control-label" style="visibility:hidden;">Pilih Toko</label>

                                <label class="col-md-3 control-label"></label>

                                <label class="col-md-3 control-label">Pilih Jangka Waktu</label>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="scopecabang" id="scopecabang" class="selectpicker form-control" data-live-search="true" onchange="changeCabang()">
                                        {!! $optionbranch !!}
                                    </select>

                                </div>
                                <div class="col-md-3" style="visibility:hidden;">
                                    <select name="scopetoko" id="scopetoko" class="selectpicker form-control" data-live-search="true" onchange="changeToko()">
                                        <option style='font-size: 12px;' value='%' selected>SEMUA TOKO</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="scopekasir" id="scopekasir" class="selectpicker form-control invisible" data-live-search="true">
                                        <option style='font-size: 12px;' value='%' selected>SEMUA KASIR</option>
                                        {{--Append goes here--}}
                                    </select>
{{--                                    todo rapihin view nya!--}}
                                    <div id="containertahun">
                                        <select name="scopetahun" id="scopetahun" class="selectpicker form-control" data-live-search="true">
                                            <option style='font-size: 12px;' value='%-' selected>SEMUA TAHUN</option>
                                            {!! $tahun !!}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
{{--                                    <select name="scopehari" id="scopehari" class="selectpicker form-control" data-live-search="true" onchange="showChooseDate()">--}}
{{--                                        <option style='font-size: 12px;' value='%'>ALL TIME</option>--}}
{{--                                        <option style='font-size: 12px;' value='yearmonth'>TAHUN / BULAN</option>--}}
{{--                                        <option style='font-size: 12px;' value='daterange'>HARIAN</option>--}}
{{--                                    </select>--}}
                                    <div id="containerbulan">
                                        <select name="scopebulan" id="scopebulan" class="selectpicker form-control" data-live-search="true">
                                            <option style='font-size: 12px;' value='%' selected>SEMUA BULAN</option>
                                            <option style='font-size: 12px;' value='01'>JANUARI</option>
                                            <option style='font-size: 12px;' value='02'>FEBRUARI</option>
                                            <option style='font-size: 12px;' value='03'>MARET</option>
                                            <option style='font-size: 12px;' value='04'>APRIL</option>
                                            <option style='font-size: 12px;' value='05'>MEI</option>
                                            <option style='font-size: 12px;' value='06'>JUNI</option>
                                            <option style='font-size: 12px;' value='07'>JULI</option>
                                            <option style='font-size: 12px;' value='08'>AGUSTUS</option>
                                            <option style='font-size: 12px;' value='09'>SEPTEMBER</option>
                                            <option style='font-size: 12px;' value='10'>OKTOBER</option>
                                            <option style='font-size: 12px;' value='11'>NOVEMBER</option>
                                            <option style='font-size: 12px;' value='12'>DESEMBER</option>
                                        </select>
                                    </div>
                                    <input type="text" class="form-control" id="daterange" name="daterange" style="display:none;" autocomplete="off" value="" />
                                </div>
                            </div>
                            <div class="row" style="margin-top: 20px;">
                            </div>
                            <div class="row">
                                <div class="col-md-6">

                                </div>
                                <div class="col-md-3" style="text-align:center;">
                                    <button id="exportButton" class=" btn btn-success" type="button" onclick="exportIt(); this.style.display='none';" style="width: 100%; display: none">EXPORT
                                </div>
                                <div class="col-md-3" style="text-align:center;">
                                    <button class="col-md-3 btn btn-primary" type="button" onclick="showPNLList()" style="width: 100%">SUBMIT
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="table-wrapper">
                                        <div id="table-scroll" style="max-height:300px;overflow:auto;">
                                            <table class="datatable table table-striped table-bordered responsive" id="dtTablePNL" style="display: none;">
                                                <thead>
                                                <tr>
                                                    <th class="font-14" style="text-align: center;">Cabang</th>
                                                    <th class="font-14" style="text-align: center;">Nama Toko</th>
                                                    <th class="font-14" style="text-align: center;">No.Member</th>
                                                    <th class="font-14" style="text-align: center;">Hari Buka</th>
                                                    <th class="font-14" style="text-align: center;">Sales</th>
                                                    <th class="font-14" style="text-align: center;">SPD</th>
                                                    <th class="font-14" style="text-align: center;">Margin</th>
                                                    <th class="font-14" style="text-align: center;">%Margin</th>
                                                    <th class="font-14" style="text-align: center;">Pendapatan Sewa</th>
                                                    <th class="font-14" style="text-align: center;">Pendapatan Lain-Lain</th>
                                                    <th class="font-14" style="text-align: center;">Biaya DF</th>
                                                    <th class="font-14" style="text-align: center;">Biaya Listrik</th>
                                                    <th class="font-14" style="text-align: center;">Biaya Lain_Lain</th>
                                                    <th class="font-14" style="text-align: center;">Dibuat oleh</th>
                                                    <th class="font-14" style="text-align: center;">Aksi</th>
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
                                                    <th id="ftotali" style="color: black; text-align: right;"></th>
                                                    <th id="mtotali" style="color: black; text-align: right;"></th>
                                                    <th></th>
                                                    <th></th>
{{--                                                    <th></th>--}}
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
                        <input type="text" id="eftipe" name="eftipe" hidden>
                    </form>
                </section>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var projTable = 2;
        var monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        var selectedMonth = 0;
        var selectedYear = 0;

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
            // changeCabang();
            showChooseDate();
        });

        function downloadPDF(user_id) {
            var year = selectedYear.replace("-", "");
            console.log(user_id);
            window.open('/getpnlreportfile/'+user_id);

            return '';
            $.ajax({
                url:'getpnlreportfile',
                type : 'GET',
                data : {
                    '_token' : '{{ csrf_token() }}',
                    'user_id' : user_id,
                    'year' : year,
                    'month' : selectedMonth
                },
                success:function(response)
                {
                    window.open('/getpnlreportfile/');
/*
                    var blob = new Blob([response], {type: 'application/pdf'})
                    var url = URL.createObjectURL(blob);
                    location.assign(url);
*/
                    /*
                    var blob = new Blob([response]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = "Sample.pdf";
                    link.click();*/

                    // var a = document.createElement("a");
                    // a.href = response.file;
                    // a.download;
                    // a.download = response.name;
                    // document.body.appendChild(a);
                    // a.click();
                    // a.remove();
                }
            });
        }

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
                // requestSales();
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
                        $('#exportButton').css("display","none");
                        // requestSales();
                    }
                });
            }
        }

        function showChooseDate()
        {
            // var day = $('#scopehari').val();
            // if(day === "daterange")
            // {
            //     $('#containertahun').css('display','none');
            //     $('#containerbulan').css('display','none');
            //     $('#daterange').css('display','');
            // }
            // else if(day === "yearmonth")
            // {
                $('#containertahun').css('display','');
                $('#containerbulan').css('display','');
                $('#daterange').css('display','none');
            // }
            // else
            // {
            //     $('#containertahun').css('display','none');
            //     $('#containerbulan').css('display','none');
            //     $('#daterange').css('display','none');
            //     requestSales();
            // }
        }

        function numberWithCommas(x) {
            var parts = x.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return parts.join(".");
        }

        function showPNLList()
        {
            date = $('#scopetahun').val() + $('#scopebulan').val() + $('#scopecabang').val();
            console.log(date + ' -> DATE');

            if(projTable != 2)
            {
                projTable.destroy();
            }
            $('#dtTablePNL').css("display","");

            selectedMonth = $('#scopebulan').val();
            selectedYear = $('#scopetahun').val();

            projTable = $('#dtTablePNL').DataTable( {
                dom: "<'row'<'col-sm-4'l><'col-sm-4'B><'col-sm-4'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",

                buttons: [
                    // {
                    //     text: 'Export Excel',
                    //     className: "newexportbutton",
                    //     action: function ( e, dt, node, config ) {
                    //         exportIt();
                    //     }
                    // }
                    'copy', 'csv', 'excel', 'pdf', 'print'

                ],
                paging : false,
                info : false,
                processing: true,
                serverSide: true,
                ordering: true,
                searching : true,
                autoWidth: false,
                "columnDefs": [
                    {"className": "dt-center", "targets": "_all"}
                ],
                ajax: {
                    url: 'getpnldatatable',
                    data: function (d) {
                        d.branch_id = $('#scopecabang').val();
                        d.month = selectedMonth;
                        d.year = selectedYear;
                    }
                },
        //Cabang
        //Nama Toko
        //No.Member
        //Hari Buka
        //Sales
        //SPD
        //Margin
        //%Margin
                columns: [
                    { data: 'branch_name', name: 'branch_name'},
                    { data: 'store_name', name: 'store_name'},
                    { data: 'member_code', name: 'member_code'},
                    { data: 'total_sign', name: 'total_sign'},
                    { data: 'grand_total', name: 'grand_total', className: "text-right",render: $.fn.dataTable.render.number( ',', ',', 0 )},
                    { data: 'spd', name: 'spd', className: "text-right",render: $.fn.dataTable.render.number( ',', ',', 0 )},
                    { data: 'total_margin', name: 'total_margin', className: "text-right",render: $.fn.dataTable.render.number( ',', ',', 0 )},
                    { data: 'pmargin', name: 'pmargin',className: "text-right",render: $.fn.dataTable.render.number( ',', ',', 0 )},
                    { data: 'income_rent', name: 'income_rent',className: "text-right",render: $.fn.dataTable.render.number( ',', ',', 0 )},
                    { data: 'other_income', name: 'other_income',className: "text-right",render: $.fn.dataTable.render.number( ',', ',', 0 )},
                    { data: 'distribution_fee', name: 'distribution_fee', className: "text-right",render: $.fn.dataTable.render.number( ',', ',', 0 )},
                    { data: 'electricity_spending', name: 'electricity_spending',className: "text-right",render: $.fn.dataTable.render.number( ',', ',', 0 )},
                    { data: 'other_spending', name: 'other_spending',className: "text-right",render: $.fn.dataTable.render.number( ',', ',', 0 )},
                    { data: 'created_by', name: 'created_by'},
                    { data: 'action', name: 'action'}
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
                // drawCallback : function(setting, json) {
                //     var data = setting.json;
                //     document.getElementById('ftotali').innerHTML = numberWithCommas(data.totalt);
                //     document.getElementById('mtotali').innerHTML = numberWithCommas(data.totalm);
                // }
            } );
            projTable.draw();
        }

        function requestSalesInvoice()
        {
            projTable.destroy();
            $('#dtTableDate').css("display","none");
            $('#dtTableProduct').css("display","none");
            $('#dtTableInvoice').css("display","");
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
            projTable = $('#dtTableInvoice').DataTable( {
                dom: "<'row'<'col-sm-4'l><'col-sm-4'B><'col-sm-4'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",

                buttons: [
                    // {
                    //     text: 'Export Excel',
                    //     className: "newexportbutton",
                    //     action: function ( e, dt, node, config ) {
                    //         exportIt();
                    //     }
                    // }
                    'copy', 'csv', 'excel', 'pdf', 'print'

                ],
                paging: false,
                "bPaginate": false,
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
                        "width": '1%',
                        "className": "dt-center", 
                        "targets": "_all"
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
                    document.getElementById('ftotali').innerHTML = numberWithCommas(data.totalt);
                    document.getElementById('mtotali').innerHTML = numberWithCommas(data.totalm);
                }
            } );
            projTable.draw();
        }

        function requestSalesDay()
        {
            var date = $('#scopehari').val();
            var cab;
            if($('#scopecabang').val() === "%"){
                cab = "%";
            } else{
                cab = $('#scopecabang').val();
            }
            // if($('#scopecabang').val() === "%") //[17-06-2020 #VANVAN] ini di uncomment
            // {
            //     alert("Silahkan tentukan cabang");
            // }
            // else
            // {
            if ( date === "yearmonth" )
            {
                if($('#scopetahun').val() === "%-" || $('#scopebulan').val() === "%-%")
                {
                    alert("Silahkan tentukan bulan dan tahun");
                }
                else
                {
                    if(projTable !== 2)
                    {
                        projTable.destroy();
                    }
                    $('#dtTableDay').css("display","");
                    $('#dtTableProduct').css("display","none");
                    $('#dtTableDate').css("display","none");
                    $('#dtTableMonth').css("display","none");
                    $('#dtTableInvoice').css("display","none");
                    $('#dtTableBranch').css("display","none");
                    $('#dtTableRecap').css("display","none");
                    $('#dtTableBranchDate').css("display","none");

                    date = $('#scopetahun').val() + $('#scopebulan').val();
                    // alert(date);
                    $.ajax({
                        url:'getsalesdaydatatable',
                        type : 'GET',
                        data : {
                            'branch' : cab,
                            'store' : $('#scopetoko').val(),
                            'cashier' : $('#scopekasir').val(),
                            'date' : date
                        },
                        dataType : 'html',
                        success:function(response)
                        {
                            $("#thmonth").text(monthNames[parseInt($('#scopebulan').val().substring(0,2))-1]+" "+$('#scopetahun').val().substring(0,4));
                            $("#dtTableDay tbody").empty();
                            $("#dtTableDay tbody").append(response);
                            $('#exportButton').css("display","");
                        }
                    });
                }
            }
            else
            {
                alert("Silahkan pilih jangka waktu TAHUN / BULAN");
            }
            // }
        }

        function requestSalesProduct()
        {
            if($('#scopetahun').val() === "%-"){
                var thn = new Date().getFullYear();
                $('#scopetahun').val(thn+"-");
            }
            if($('#scopecabang').val() === "%")
            {
                alert("Silahkan tentukan cabang");
            }
            else if($('#scopetoko').val() === "%")
            {
                alert("Silahkan tentukan toko");
            }
            else if($('#scopetahun').val() === "%-")
            {
                alert("Silahkan tentukan tahun");
            }
            else
            {
                if(projTable !== 2)
                {
                    projTable.destroy();
                }
                $('#dtTableDay').css("display","none");
                $('#dtTableProduct').css("display","");
                $('#dtTableDate').css("display","none");
                $('#dtTableMonth').css("display","none");
                $('#dtTableInvoice').css("display","none");
                $('#dtTableBranch').css("display","none");
                $('#dtTableRecap').css("display","none");
                $('#dtTableBranchDate').css("display","none");

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

                $.ajax({
                    url:'getsalesproductdatatable',
                    type : 'GET',
                    data : {
                        'branch' : $('#scopecabang').val(),
                        'store' : $('#scopetoko').val(),
                        'date' : date,
                        'startdate' : startdate,
                        'enddate' : enddate
                    },
                    dataType : 'html',
                    success:function(response)
                    {
                        $("#dtTableProduct tbody").empty();
                        $("#dtTableProduct tbody").append(response);
                        $('#exportButton').css("display","");
                    }
                });
            }
        }

        function requestSalesBranch()
        {
            var date = $('#scopehari').val();
            // if($('#scopecabang').val() === "%")
            // {
            //     alert("Silahkan tentukan cabang");
            // }
            // else
            // {
            if ( date === "yearmonth" )
            {
                if($('#scopetahun').val() === "%-" || $('#scopebulan').val() !== "%-%")
                {
                    alert("Silahkan tentukan tahun, dan pilih option semua bulan");
                }
                else
                {
                    if(projTable !== 2)
                    {
                        projTable.destroy();
                    }
                    $('#dtTableDay').css("display","none");
                    $('#dtTableProduct').css("display","none");
                    $('#dtTableDate').css("display","none");
                    $('#dtTableMonth').css("display","none");
                    $('#dtTableInvoice').css("display","none");
                    $('#dtTableBranch').css("display","");
                    $('#dtTableRecap').css("display","none");
                    $('#dtTableBranchDate').css("display","none");

                    date = $('#scopetahun').val() + $('#scopebulan').val();

                    $.ajax({
                        url:'getsalesbranchdatatable',
                        type : 'GET',
                        data :
                            {
                                'branch' : $('#scopecabang').val(),
                                'store' : $('#scopetoko').val(),
                                'date' : date
                            },
                        dataType : 'html',
                        success:function(response)
                        {
                            $("#dtTableBranch tbody").empty();
                            $("#dtTableBranch tbody").append(response);
                            $('#exportButton').css("display","");
                        }
                    });
                }
            }
            else
            {
                alert("Silahkan pilih jangka waktu TAHUN / BULAN");
            }
            // }
        }

        function requestSalesMonth()
        {
            var date = $('#scopehari').val();
            // if($('#scopecabang').val() === "%")
            // {
            //     alert("Silahkan tentukan cabang");
            // }
            // else
            // {
            if ( date === "yearmonth" )
            {
                if($('#scopetahun').val() === "%-" || $('#scopebulan').val() !== "%-%")
                {
                    alert("Silahkan tentukan tahun, dan pilih option semua bulan");
                }
                else
                {
                    if(projTable !== 2)
                    {
                        projTable.destroy();
                    }
                    $('#dtTableDay').css("display","none");
                    $('#dtTableProduct').css("display","none");
                    $('#dtTableDate').css("display","none");
                    $('#dtTableMonth').css("display","");
                    $('#dtTableInvoice').css("display","none");
                    $('#dtTableBranch').css("display","none");
                    $('#dtTableRecap').css("display","none");
                    $('#dtTableBranchDate').css("display","none");

                    date = $('#scopetahun').val() + $('#scopebulan').val();

                    $.ajax({
                        url:'getsalesmonthdatatable',
                        type : 'GET',
                        data :
                            {
                                'branch' : $('#scopecabang').val(),
                                'store' : $('#scopetoko').val(),
                                'date' : date
                            },
                        dataType : 'html',
                        success:function(response)
                        {
                            $("#dtTableMonth tbody").empty();
                            $("#dtTableMonth tbody").append(response);
                            $('#exportButton').css("display","");
                        }
                    });
                }
            }
            else
            {
                alert("Silahkan pilih jangka waktu TAHUN / BULAN");
            }
            // }
        }

        function requestSalesRecap()
        {
            if($('#scopecabang').val() === "%")
            {
                alert("Silahkan tentukan cabang");
            }
            else
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
                if(projTable !== 2)
                {
                    projTable.destroy();
                }
                $('#dtTableDay').css("display","none");
                $('#dtTableProduct').css("display","none");
                $('#dtTableDate').css("display","none");
                $('#dtTableMonth').css("display","none");
                $('#dtTableInvoice').css("display","none");
                $('#dtTableBranch').css("display","none");
                $('#dtTableRecap').css("display","");
                $('#dtTableBranchDate').css("display","none");

                $.ajax({
                    url:'getsalesrecapdatatable',
                    type : 'GET',
                    data :
                        {
                            'branch' : $('#scopecabang').val(),
                            'store' : $('#scopetoko').val(),
                            'date' : date,
                            'startdate' : startdate,
                            'enddate' : enddate
                        },
                    dataType : 'html',
                    success:function(response)
                    {
                        $("#dtTableRecap tbody").empty();
                        $("#dtTableRecap tbody").append(response);
                        $('#exportButton').css("display","");
                    }
                });

            }
        }

        function requestSalesBranchDate()
        {
            // if($('#scopecabang').val() === "%")
            // {
            //     alert("Silahkan tentukan cabang");
            // }
            // else
            // {
            if(projTable !== 2)
            {
                projTable.destroy();
            }
            $('#dtTableDay').css("display","none");
            $('#dtTableProduct').css("display","none");
            $('#dtTableDate').css("display","none");
            $('#dtTableMonth').css("display","none");
            $('#dtTableInvoice').css("display","none");
            $('#dtTableBranch').css("display","none");
            $('#dtTableRecap').css("display","none");
            $('#dtTableBranchDate').css("display","");

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

            $.ajax({
                url:'getsalesbranchdatedatatable',
                type : 'GET',
                data : {
                    'branch' : $('#scopecabang').val(),
                    'store' : $('#scopetoko').val(),
                    'date' : date,
                    'startdate' : startdate,
                    'enddate' : enddate
                },
                dataType : 'html',
                success:function(response)
                {
                    $("#dtTableBranchDate tbody").empty();
                    $("#dtTableBranchDate tbody").append(response);
                    $('#exportButton').css("display","");
                }
            });
            // }
        }

        function exportIt()
        {
            // alert('export woy');
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
            $('#eftipe').val($('#scopejenis').val());

            form.submit();
        }
    </script>
    <style>
        .newexportbutton{
            margin-top: 20px;
        }
        #table-wrapper {
            position:relative;
        }
        #table-scroll {
            height:100%;
            overflow:auto;
            margin-top:20px;
        }
        #table-wrapper table {
            width:100%;

        }
        #table-wrapper table * {
            /*background:yellow;*/
            color:black;
        }
        #table-wrapper table thead th .text {
            position:absolute;
            top:-20px;
            z-index:2;
            height:20px;
            width:35%;
            border:1px solid red;
        }
        th {
            background: white;
            position: sticky;
            top: 0;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        }
    </style>
@endsection
