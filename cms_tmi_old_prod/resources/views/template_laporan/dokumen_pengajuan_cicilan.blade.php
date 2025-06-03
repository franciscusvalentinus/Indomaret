<!doctype html>
<html>
    <head>
        <title>Ini Adalah Title</title>
        <style>
            @page { margin: 100px 25px; }
            header { position: fixed; top: -60px; left: 0px; right: 0px; height: 80px; }
            footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 80px; }
            .table_ttd{
                border: 1px solid black;
                padding: 10px;
                border-collapse: collapse;
                text-align: center;
            }
            th{
                text-align: left;
            }
            .form_table{
                padding-top: 0px;
                padding-bottom: 0px;
                margin-top: -10px;
                margin-bottom: -10px;
            }
            .form_table tr td{
                padding-top: 0px;
                padding-bottom: -3px;
                margin-top: 0px;
                margin-bottom: 0px;
            }
	
        </style>
    </head>
    <body>
        <header>
            <p style="font-weight: bold; font: italic">PT. INTICAKRAWALA CITRA</p>
            <p style="font: italic">Toko Igr. {{$branch_name}}</p>
        </header>

        <main>
{{--
<h1>fsdfsdfsfsdfsss sssssssssssssssssss sssssssssssss ssssssssssss ssssssssssss ssssss</h1> <br>
<h1>fsdfsdfsfsdfsss sssssssssssssssssss sssssssssssss ssssssssssss ssssssssssss ssssss</h1> <br>
<p>ini p1 </p> <br>
<p>ini p2 </p> <br>
<p>ini p3 </p> 
--}}

            <h2 style="text-align: center">Dokumen Pengajuan Cicilan TMI</h2><br>
        <p style="text-align: center">Nomor : {{$credit_approval_number}}</p><br>
<br>
        <h4>DATA CALON TMI</h4><br>
        <table class="form_table">
            <tr>
                <td>Nama</td>
                <td>: {{$username}}</td>
            </tr>
            @if($store_name != null)
                <tr>
                    <td>Nama Toko</td>
                    <td>: {{$store_name}}</td>
                </tr>
            @endif
            @if($nik != null)
                <tr>
                    <td>NIK</td>
                    <td>: {{$nik}}</td>
                </tr>
            @endif
            <tr>
                <td>Nomor Member Toko Igr  </td>
                <td>: {{$member_code}}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>: {{$address}}</td>
            </tr>
            <tr>
                <td>Nomor Telepon</td>
                <td>: {{$phone_number}}</td>
            </tr>
            <tr>
                <td>NPWP</td>
                <td>: {{$npwp}}</td>
            </tr>
            <tr>
                <td>PKP</td>
                <td>: {{$pkp}}</td>
            </tr>
        </table><br>

        <h4>DETAIL PENGAJUAN FASILITAS CICILAN</h4><br>
        <table class="form_table">
            <tr>
                <td>Tipe TMI</td>
                <td>: {{$member_type}}</td>
            </tr>
            <tr>
                <td>Nilai (Rp.) maksimal cicilan</td>
                <td>: {{$credit_limit}}</td>
            </tr>
            <tr>
                <td>Tenor cicilan</td>
                <td>: {{$tenor}}</td>
            </tr>
            <tr>
                <td>Batas waktu penggunaan fasilitas cicilan  </td>
                <td>: {{$last_order_date}}</td>
            </tr>
            <tr>
                <td>Masa tenggang pembayaran cicilan</td>
                <td>: {{$end_period}}</td>
            </tr>
            <tr>
                <td>Lama masa tenggang</td>
                <td>: {{$grace_period}}</td>
            </tr>
        </table><br>
{{--
        <br>
        <table class="table_ttd" style="width: 100%">
            <tr class="table_ttd">
                <td class="table_ttd" colspan="2">Disetujui,</td>
                <td class="table_ttd">Diketahui,</td>
            </tr>
            <tr class="table_ttd">
                <td class="table_ttd" style="height: 70px"></td>
                <td class="table_ttd" style="height: 70px"></td>
                <td class="table_ttd" style="height: 70px"></td>
            </tr>
            <tr class="table_ttd">
                <td class="table_ttd">Operation Director</td>
                <td class="table_ttd">Operation TMI</td>
                <td class="table_ttd">Customer Service Mgr./Jr. Mgr.</td>
            </tr>
        </table>
        <p style="font-style: italic; font-weight: bold; display: block">Catatan : </p>
        
        <p>Pemohon wajib melengkapi formulir ini dengan identitas diri (contoh: fotocopy KTP) yang masih berlaku.</p>
--}}
        </main>

    </body>
</html>