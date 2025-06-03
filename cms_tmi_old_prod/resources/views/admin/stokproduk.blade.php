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
                                <h1>Laporan Stok Produk</h1>
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
                                    <select name="scopetoko" id="scopetoko" style="visibility:hidden" class="selectpicker form-control" data-live-search="true" onchange="document.getElementById('exportButton').style.display='none';">
                                        <option style='font-size: 12px;' value='%' selected>PILIH TOKO</option>
                                    </select>
                                    <img id="storeAnimationLoading" style="visibility:hidden" width="90px" src="img/loading.gif"/>
                                </div>
                                <div class="col-md-3" style="text-align:center;">
                                    <button id="exportButton" class=" btn btn-success" type="button" onclick="exportIt(); this.style.display='none';" style="width: 100%; display: none">EXPORT</button>
                                </div>
                                <div class="col-md-3" style="text-align:center;">
                                    <button class="col-md-3 btn btn-primary" type="button" onclick="requestStok()" style="width: 100%">SUBMIT</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="table-wrapper">
                                        <div id="table-scroll" style="max-height:300px;overflow:auto;">
                                            <table class="datatable table table-striped table-bordered responsive" id="dtTable" style="display: none">
                                                <thead>
                                                    <tr>
                                                        <th class="font-14" style="text-align: center;">DIV</th>
                                                        <th class="font-14" style="text-align: center;">DEP</th>
                                                        <th class="font-14" style="text-align: center;">KAT</th>
                                                        <th class="font-14" style="text-align: center;">PLU</th>
                                                        <th class="font-14" style="text-align: center;">DESKRIPSI</th>
                                                        <th class="font-14" style="text-align: center;">STOK QTY</th>
                                                        <th class="font-14" style="text-align: center;">AVG SALES</th>
                                                        <th class="font-14" style="text-align: center;">QTY TERJUAL</th>
                                                        <th class="font-14" style="text-align: center;">MIN QTY</th>
                                                        <th class="font-14" style="text-align: center;">MAX QTY</th>
                                                        <th class="font-14" style="text-align: center;">HRG POKOK</th>
                                                        <th class="font-14" style="text-align: center;">HRG JUAL</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form id="exportform" action="exportformstok" method="post" target="_blank">
                        <input type="text" name="_token" value="{{ csrf_token() }}" hidden>
                        <input type="text" id="efcabang" name="efcabang" hidden>
                        <input type="text" id="eftoko" name="eftoko" hidden>
                    </form>
                </section>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function changeCabang()
        {
            document.getElementById("scopetoko").style.visibility = 'hidden';
            document.getElementById("storeAnimationLoading").style.visibility = 'visible';
            var cab = $('#scopecabang').val();

            if(cab == null)
            {
                $('#scopetoko').empty().append("<option style='font-size: 12px;' value='%' selected>PILIH TOKO</option>");
                $('#scopetoko').selectpicker('refresh');
                // document.getElementById("scopetoko").style.visibility = 'visible';
                // document.getElementById("storeAnimationLoading").style.visibility = 'hidden';
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
                        $('#exportButton').css("display","none");
                    }, complete:function(){
                        document.getElementById("scopetoko").style.visibility = 'visible';
                        document.getElementById("storeAnimationLoading").style.visibility = 'hidden';
                    }
                });
            }
        }

        function numberWithCommas(x) {
            var parts = x.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return parts.join(".");
        }

        function requestStok()
        {
            if($('#scopecabang').val() === "%")
            {
                alert("Silahkan tentukan cabang");
            }
            else if($('#scopetoko').val() === "%")
            {
                alert("Silahkan tentukan toko");
            }
            else
            {
                $('#dtTable').css("display","");
                $.ajax({
                    url:'getstokdatatable',
                    type : 'GET',
                    data : {
                        'branch' : $('#scopecabang').val(),
                        'store' : $('#scopetoko').val()
                    },
                    dataType : 'html',
                    success:function(response)
                    {
                        // $.alert(response);
                        $("#dtTable tbody").empty();
                        $("#dtTable tbody").append(response);
                        $('#exportButton').css("display","");
                    },fail:function (resp) {
                        $.alert('test');
                    }
                });
            }
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