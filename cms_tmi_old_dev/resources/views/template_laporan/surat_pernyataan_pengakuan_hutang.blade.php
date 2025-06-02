<!doctype html>
<html>
    <head>
        <title>Ini Adalah Title</title>
        <style>
            @page { margin: 0px 25px; }
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
            .credit_table{
                border: 1px solid black;
                border-collapse: collapse;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <header>
            <div style="width:100%">
                <p style="width: 50%; display: inline-block; font-weight: bold; font: italic">
                    PT. INTICAKRAWALA CITRA<br><i>Toko Igr. {{$branch_name}}</i>
                </p>
                <p style="width: 50%; display: inline-block;">
                    Nomor Surat : {{$spph_number}}
                </p>
            </div>
        </header>
        <h3 style="text-align: center; padding-top: -30px;">SURAT PERNYATAAN PENGAKUAN HUTANG</h3>
        <p style="padding-bottom: -3px;">Yang bertanda tangan di bawah ini:</p>
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
                <td>Alamat </td>
                <td>: {{$address}}</td>
            </tr>
            <tr>
                <td>No. KTP</td>
                <td>: &nbsp;</td>
            </tr>
            <tr>
                <td>Nama Toko</td>
                <td>: {{$store_name}}</th>
            </tr>
            <tr>
                <td>Alamat Toko &nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td>: {{$srore_address}}</td>
            </tr>
        </table>

        <p style="padding-bottom: -10px;">Dengan ini menyatakan :</p>
        <ol>
            <li>Saya menyatakan mengakui benar-benar dan secara sah telah berhutang kepada PT INTI CAKRAWALA CITRA sebesar {{$total_credit}}, dalam bentuk pengadaan persediaan barang dagangan toko saya dengan perincian sebagaimana disebutkan dalam lampiran Surat Pernyataan ini.</li>
            <li>Saya dengan ini berjanji dan mengikatkan diri, dengan cara di angsur dalam jangka waktu {{$total_month}} bulan secara berturut-turut, dimulai 3 (tiga) bulan terhitung sejak tanggal pembukaan toko, dengan ketentuan sebagai berikut :</li>
                <ul>
                    <li>Pembayaran angsuran sebesar {{$credit_per_month}},- setiap bulan secara berturut-turut</li>
                    <li>Pembayaran dilakukan paling lambat pada tanggal .....[INI DI ISI OLEH OPR NYA!] setiap bulan</li>
                    <li>Pembayaran dilakukan dengan cara transfer ke rekening ............ atas nama PT INTI CAKRAWALA CITRA</li>
                    <li>Pembayaran selain tersebut diatas dianggap sebagai pembayaran tidak sah</li>
                </ul>
        </ol>
        <ol start="3"><li>Sebagai jaminan atas pelaksanaan pembayaran hutang saya kepada PT INTI CAKRAWALA CITRA tersebut di atas, maka saya atas kerelaan sendiri dan tanpa paksaan dengan ini menyatakan bahwa seluruh barang dagangan yang terdapat dalam toko saya tersebut diatas sebagai jaminan kepada PT INTICAKRAWALA CITRA, dan sehingga segera setelah saya tidak melakukan pembayaran angsuran sesuai dengan ketentuan angka 2 di atas, maka saya dengan ini secara tegas memberikan kuasa kepada PT INTICAKRAWALA CITRA untuk memasuki toko, mengambil secara langsung, menjual jaminan tersebut guna pelunasan seluruh sisa hutang saya, yang tanpa kuasa mana Surat Pernyataan ini tidak akan dibuat, karenanya kuasa tersebut tidak dapat dicabut kembali dan tidak akan berakhir berdasarkan alasan apapun juga tanpa persetujuan tertulis dari PT INTICAKRAWALA CITRA serta disebabkan oleh hal-hal yang dimaksud dalam Pasal 1813 Kitab Undang-Undang hukum Perdata</li>
            <li>Mengenai surat pengakuan hutang hutang ini dan segala akibat hukumnya serta pelaksanaannya saya memilih tempat kedudukan (domisili) yang tetap dan umum di kantor kepaniteraan Pengadilan Negeri Jakarta Utara di Jakarta</li>
        </ol>
        <p>Demikianlah Surat Pernyataan Pengakuan Hutang ini saya buat dalam keadaan sadar dan tanpa paksaan, untuk dapat di pergunakan sebagaimana mestinya.</p>

        <p>Jakarta, ..............</p>
        <div style="width:100%">
            <p style="width: 50%; display: inline-block">
                Hormat saya,
            </p>
            <p style="width: 50%; display: inline-block;">
                Menerima Pengakuan Hutang,<br>PT INTI CAKRAWALA CITRA
            </p>
        </div>
        
        <div style="width:99%">
            <p style="width: 50%; display: inline-block">
                ..............................................
            </p>
            <p style="width: 50%; display: inline-block;">
                ..............................................
            </p>
        </div>

        <div class="page_2">
            <p style="font-weight: bold">Contoh Lampiran Surat Pernyataan Pengakuan Hutang</p>
            <h3 style="text-align: center">LAMPIRAN SURAT PERNYATAAN PENGAKUAN HUTANG</h3>
            <table>
                <tr>
                    <td>Kode/Nama TMI</td>
                    <td>: {{$member_code}} / {{$username}}</td>
                </tr>
                <tr>
                    <td>Kode Referensi SPPH</td>
                    <td>: {{$spph_number}}</td>
                </tr>
                <tr>
                    <td>Nilai (Rp.) total penggunaan cicilan</td>
                    <td>: {{$credit_per_month}}</td>
                </tr>
                <tr>
                    <td>Nilai (Rp.)</td>
                    <td>: {{$total_credit}}</td>
                </tr>
                <tr>
                    <td>Tenor cicilan</td>
                    <td>: {{$tenor}} Bulan</td>
                </tr>
            </table>

            <table class="credit_table" style="width: 99%;">
                <tr class="credit_table">
                    <th class="credit_table">No.</th>
                    <th class="credit_table">Bulan</th>
                    <th class="credit_table">Nilai (Rp.)</th>
                    <th class="credit_table">Sisa Pokok</th>
                    <th class="credit_table">Keterangan</th>
                </tr>
                <tr class="credit_table">
                    <td class="credit_table" style="font-weight: bold" colspan="3">Total Penggunaan Cicilan</td>
                    <td class="credit_table">{{$total_credit}}</td>
                    <td class="credit_table"></td>
                </tr>
                @foreach($credit_detail as $detail)
                    <tr class="credit_table">
                        <td class="credit_table">{{$detail['no']}}</td>
                        <td class="credit_table">{{$detail['month_year']}}</td>
                        <td class="credit_table">{{$detail['credit_per_month']}}</td>
                        <td class="credit_table">{{$detail['sisa_pokok']}}</td>
                        <td class="credit_table">{{$detail['keterangan']}}</td>
                    </tr>
                @endforeach
                {{-- nanti disini bagian looping --}}

                {{-- <tr class="credit_table">
                    <td class="credit_table">1</td>
                    <td class="credit_table">No-2020</td>
                    <td class="credit_table">Rp 999,250</td>
                    <td class="credit_table">Rp 18,985,750</td>
                    <td class="credit_table">Cicilan bulan ke - 1</td>
                </tr>
                <tr class="credit_table">
                    <td class="credit_table">1</td>
                    <td class="credit_table">No-2020</td>
                    <td class="credit_table">Rp 999,250</td>
                    <td class="credit_table">Rp 18,985,750</td>
                    <td class="credit_table">Cicilan bulan ke - 1</td>
                </tr>
                <tr class="credit_table">
                    <td class="credit_table">1</td>
                    <td class="credit_table">No-2020</td>
                    <td class="credit_table">Rp 999,250</td>
                    <td class="credit_table">Rp 18,985,750</td>
                    <td class="credit_table">Cicilan bulan ke - 1</td>
                </tr>
                <tr class="credit_table">
                    <td class="credit_table">1</td>
                    <td class="credit_table">No-2020</td>
                    <td class="credit_table">Rp 999,250</td>
                    <td class="credit_table">Rp 18,985,750</td>
                    <td class="credit_table">Cicilan bulan ke - 1</td>
                </tr>
                <tr class="credit_table">
                    <td class="credit_table">1</td>
                    <td class="credit_table">No-2020</td>
                    <td class="credit_table">Rp 999,250</td>
                    <td class="credit_table">Rp 18,985,750</td>
                    <td class="credit_table">Cicilan bulan ke - 1</td>
                </tr>
                <tr class="credit_table">
                    <td class="credit_table">1</td>
                    <td class="credit_table">No-2020</td>
                    <td class="credit_table">Rp 999,250</td>
                    <td class="credit_table">Rp 18,985,750</td>
                    <td class="credit_table">Cicilan bulan ke - 1</td>
                </tr>
                <tr class="credit_table">
                    <td class="credit_table">1</td>
                    <td class="credit_table">No-2020</td>
                    <td class="credit_table">Rp 999,250</td>
                    <td class="credit_table">Rp 18,985,750</td>
                    <td class="credit_table">Cicilan bulan ke - 1</td>
                </tr>
                <tr class="credit_table">
                    <td class="credit_table">1</td>
                    <td class="credit_table">No-2020</td>
                    <td class="credit_table">Rp 999,250</td>
                    <td class="credit_table">Rp 18,985,750</td>
                    <td class="credit_table">Cicilan bulan ke - 1</td>
                </tr>
                <tr class="credit_table">
                    <td class="credit_table">1</td>
                    <td class="credit_table">No-2020</td>
                    <td class="credit_table">Rp 999,250</td>
                    <td class="credit_table">Rp 18,985,750</td>
                    <td class="credit_table">Cicilan bulan ke - 1</td>
                </tr>
                <tr class="credit_table">
                    <td class="credit_table">1</td>
                    <td class="credit_table">No-2020</td>
                    <td class="credit_table">Rp 999,250</td>
                    <td class="credit_table">Rp 18,985,750</td>
                    <td class="credit_table">Cicilan bulan ke - 1</td>
                </tr> --}}
            </table>
        </div>
    </body>
</html>