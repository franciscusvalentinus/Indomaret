<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{$title}}</title>
    <style>
         @page {
            margin: 0px 0px 0px 0px !important;
            padding: 100px 0px 0px 0px !important;
            height: 10px;
        };
        * {
            background-color: #F2F2F2;
            font-family:"Times New Roman", Times, serif;
        };
        title{
            padding: 100px;
            margin: 100px;
        };
        .subtitle{
            padding-left: 30px;
        };
        .value{
            font-weight:bold;
        };
    </style>
</head>
<body>
    <div class="header"  style="display:flex; padding-bottom: -10px; padding-top: 5px">
            <p style="text-align: center; margin-left:10px; margin-top:30px">
                {{$store_name}}
                <br>
                LAPORAN LABA RUGI
                <br>
                PERIODE {{$monthyear}}
            </p>
            <img src="{{ storage_path('/images/LOGO_TMI_02.png') }}" 
            alt="" 
            style="height: 80px; float: right;"
            />
    </div>
    <hr style="margin-bottom:15px">
    <div>
        <div style="display: flex">
            <b class="title" style="float: left; margin-left:10px;">Pendapatan</b>
            <b class="value" style="float: right; margin-right: 40px"></b>
        </div>
        <br>
        <div style="display: flex">
            <p style="float: left; margin-left: 30px;">Penjualan</p>
            <b class="value" style="float: right; margin-right: 150px">{{$total_sales}}</b>
        </div>
        <br>
        <div style="display: flex">
            <p style="float: left; margin-left: 30px;">Sewa</p>
            <b class="value" style="float: right; margin-right: 150px">{{$rent_income}}</b>
        </div>
        <br>
        <div style="display: flex">
            <p style="float: left; margin-left: 30px;">Lain-lain</p>
            <b class="value" style="float: right; margin-right: 150px">{{$other_income}}</b>
        </div>
        <br>
        <div style="display:flex;">
            <div style="width:35%; float:right; margin-right: 10px; padding-top:-10px">
                <hr>
            </div>
        </div>
        <br>
        <div style="display: flex">
            <p style="float: left; margin-left: 30px;">Total Pendapatan</p>
            <b class="value" style="float: right; margin-right: 150px">{{$total_income}}</b>
        </div>
        <br><br>
        <div style="display: flex">
            <b class="title" style="float: left; margin-left:10px;">Biaya-biaya</b>
            <b class="value" style="float: right; margin-right: 150px"></b>
        </div>
        <br>
        <div style="display: flex">
            <p style="float: left; margin-left: 30px;">Harga Pokok Penjualan</p>
            <b class="value" style="float: right; margin-right: 150px">{{$total_cost}}</b>
        </div>
        <br>
        <div style="display: flex">
            <p style="float: left; margin-left: 30px;">Biaya DF</p>
            <b class="value" style="float: right; margin-right: 150px">{{$df_fee}}</b>
        </div>
        <br>
        <div style="display: flex">
            <p style="float: left; margin-left: 30px;">Biaya Listrik</p>
            <b class="value" style="float: right; margin-right: 150px">{{$electric_fee}}</b>
        </div>
        <br>
        <div style="display: flex">
            <p style="float: left; margin-left: 30px;">Biaya Lain-lain</p>
            <b class="value" style="float: right; margin-right: 150px">{{$other_fee}}</b>
        </div>
        <br>
        <div style="display:flex;">
            <div style="width:35%; float:right; margin-right: 10px; padding-top: -10px">
                <hr>
            </div>
        </div>
        <br>
        <div style="display: flex">
            <p style="float: left; margin-left: 30px;">Total Biaya</p>
            <b class="value" style="float: right; margin-right: 150px">{{$total_fee}}</b>
        </div>
        <br>
        <br>

        <div style="display: flex">
            <p style="float: left; margin-left: 10px">Laba / Rugi Bersih</p>
            <b class="value" style="float: right; margin-right: 150px">{{$total_pnl}}</b>
        </div>
        <br><br><br>
        <div style="display: flex">
            <p style="float: left; margin-left: 10px">Di buat oleh,</p>
        </div>
<div>
		<br><br><br>
	<p style="float: left; margin-left: 10px">{{$created_by}}</p>
</div>
        <br>
    {{-- Pendapatan
    Penjualan		Rp
    Sewa			Rp
    Lain-lain		Rp
    Total Pendapatan	Rp

    Biaya-biaya
    Harga Pokok Penjualan	Rp
    Biaya DF		Rp
    Biaya Listrik		Rp
    Biaya Lain-lain		Rp
    Total Biaya		Rp

    Laba/(Rugi) Bersih		Rp


    Dibuat oleh,			Menyetujui,

    SD1				Nama Pemilik Toko
    --}}

</body>
</html>