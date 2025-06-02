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
                                <h1>Laporan Arsip Produk</h1>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-3 control-label">Pilih Cabang</label>

                                <label class="col-md-3 control-label">Pilih Toko</label>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <select name="scopecabang" id="scopecabang" class="selectpicker form-control" data-live-search="true" onchange="changeCabang()">
                                        {!! $optionbranch !!}
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="scopetoko" id="scopetoko" class="selectpicker form-control" data-live-search="true" onchange="requestArsip()">
                                        <option style='font-size: 12px;' value='%' selected>SEMUA TOKO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="datatable table table-striped table-bordered responsive" id="dtTable">
                                        <thead>
                                            <tr>
                                                <th class="font-14" style="text-align: center;">Cabang</th>
                                                <th class="font-14" style="text-align: center;">Toko</th>
                                                <th class="font-14" style="text-align: center;">PLU</th>
                                                <th class="font-14" style="text-align: center;">Deskripsi</th>
                                                <th class="font-14" style="text-align: center;">Satuan</th>
                                                <th class="font-14" style="text-align: center;">Fraksi</th>
                                                <th class="font-14" style="text-align: center;">Tanggal Diarsipkan</th>
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
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form id="exportform" action="exportformarsip" method="post" target="_blank">
                        <input type="text" name="_token" value="{{ csrf_token() }}" hidden>
                        <input type="text" id="efcabang" name="efcabang" hidden>
                        <input type="text" id="eftoko" name="eftoko" hidden>
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
                requestArsip();
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
                        requestArsip();
                    }
                });
            }
        }

        function numberWithCommas(x) {
            var parts = x.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return parts.join(".");
        }

        function requestArsip()
        {
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
                    url: 'getarsipdatatable',
                    data: function (d) {
                        d.branch = $('#scopecabang').val();
                        d.store = $('#scopetoko').val();
                    }
                },
                columns: [
                    { data: 'branch', name: 'branch'},
                    { data: 'store', name: 'store'},
                    { data: 'plu', name: 'plu'},
                    { data: 'desc', name: 'desc'},
                    { data: 'unit', name: 'unit'},
                    { data: 'frac', name: 'frac'},
                    { data: 'date', name: 'date'}
                ],
                order: [6,'asc'],
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
            var form = $('#exportform');
            $('#efcabang').val($('#scopecabang').val());
            $('#eftoko').val($('#scopetoko').val());

            form.submit();
        }
    </script>
    <style>
        .newexportbutton{
            margin-top: 20px;
        }
    </style>
@endsection