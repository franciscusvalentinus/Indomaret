<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use App\Models\TDBUser;
use Maatwebsite\Excel\Excel;

if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
// Ignores notices and reports all other kinds... and warnings
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
// error_reporting(E_ALL ^ E_WARNING); // Maybe this is enough
}

Route::get('login', 'LoginController@index');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/logout', function () {
    Auth::logout();
    return redirect('login');
});

Route::post('getlogin', 'LoginController@getLoginWeb');
Route::post('testvancurl', function(){
    return response()->json(['result'=>'hello van!!!']);
});

//Route::post('getpnldatatablet', 'ProjectController@getPNLList');

Route::group(['middleware' => 'auth'], function(){

    //Register TMI
    Route::get('inputtipetmi', 'MemberController@getTipeTmi');
    Route::post('posttipetmi', 'MemberController@getPostTipeTmi');
    Route::get('admin/tipetmi/datatable', 'MemberController@getTipeTmiDatatable'); 
    //Register Member
    Route::get('inputmember', 'MemberController@getMemberTmi');
//    Route::post('addmember', 'ProjectController@postAddMember');
    Route::get('admin/member/datatable', 'MemberController@getMemberDatatables');
    Route::get('get_plu_rejection_list', 'ProjectController@getPluRejectionList');
    Route::post('get_detail_plu_rejection', 'ProjectController@getDetailPluRejection');
    Route::post('restore_plu_rejection', 'ProjectController@restoreRejectedPlu');
    Route::get('get_member', 'MemberController@get_member');
    Route::post('registermember', 'MemberController@getRegisterMember');
    Route::post('editmember', 'MemberController@EditMember');
    Route::post('deactivatemember', 'MemberController@DeactivateMember');
    Route::post('resetpasswordmember', 'MemberController@ResetPasswordMember');
    Route::post('memberAjax', 'MemberController@getMemberAjax');

    //Permohonan Kredit Limit
    // Route::get('permohonan_kredit', 'MemberController@getCreditRequest');
    Route::post('store_credit_request', 'MemberController@storeCreditRequest');

    Route::get('daftar_persetujuan_dpc', 'MemberController@getDPCApproval');
    Route::get('daftar_cetak_spph', 'MemberController@getSPPHPage');
    Route::get('daftar_karyawan', 'MemberController@getEmployeePage');

    Route::post('get_employee_data', 'MemberController@getEmployeeData');

    Route::get('get_credit_approval_waiting_list', 'MemberController@getCreditApprovalWaitingList');
    Route::post('get_detail_credit', 'MemberController@getDetailCredit');

    Route::post('get_sales_data', 'MemberController@getIGRSalesData');
    Route::post('approve_credit', 'MemberController@sendCreditApproved');
    Route::post('dpc_approval', 'MemberController@approveDPC');

    Route::get('uploadplutmi', 'ProjectController@getViewMember');

    Route::get('plutolakan', 'ProjectController@getViewPluRejection');
    Route::post('submitplutolakan', 'ProjectController@submitPluTolakan');

    Route::get('admin/plu/datatable', 'ProjectController@getMemberPLU');

    Route::post('uploadplu', 'ProjectController@UploadPlu');

    Route::get('listmember', 'ProjectController@getListMember');

    Route::get('deleteplu', 'ProjectController@deletePlu');

    Route::post('pluAjax', 'ProjectController@getPluAjax');

    Route::post('editplu', 'ProjectController@EditPluAjax');

    Route::get('404page', 'ProjectController@getDashBoard');

    Route::get('pdf','ProjectController@export_pdf');

    Route::post('cabPlu', 'ProjectController@getCabPlu');

    Route::post('searchPlu', 'ProjectController@getSearchPlu');


    Route::post('aktivasimember', 'ProjectController@AktivasiMember');

    Route::get('uploadmargin', 'ProjectController@getUploadMargin');
    Route::post('uploadmastermargin', 'ProjectController@PostUploadMargin');
    Route::post('uploadmaxhargacontroller', 'ProjectController@PostUploadMaxHarga');
    Route::get('listmastermargin', 'ProjectController@getListMasterMargin');
    Route::get('uploadmaxharga', 'ProjectController@getUploadMaxHarga');
    Route::get('admin/mastermargin/datatable', 'ProjectController@getMarginDatatable');

    Route::get('changedep', 'ProjectController@getDepartemen');
    Route::get('changekat', 'ProjectController@getKategori');

    Route::post('addmastermargin', 'ProjectController@PostMasterMargin');
    Route::post('MarginAjax', 'ProjectController@getMarginAjax');

    Route::post('editmargin', 'ProjectController@EditMargin');

    Route::get('cabangongkirdialog', 'ProjectController@getCabangViewAjax');

    Route::post('getstoreofbranch','ProjectController@getStoreOfBranch');
    Route::post('getcashierofstore','ProjectController@getCashierOfStore');
    Route::post('getplugroup','ProjectController@getPluGroup');

    //sales
    Route::get('chartsales', function () {
        return view('admin/chartofsales');
    });
    Route::post('getchartdata', 'ProjectController@getSalesChart');
    Route::get('laporansales', 'ProjectController@returnSales');
    Route::get('laporan_spd_std_apc', function(){return "Sudah tidak tersedia";});
    Route::get('laporan_spd_std_apc_old', 'ProjectController@returnSpdStdApc');
    Route::get('laporan_rekap_sinkronisasi', 'ProjectController@returnRekapSinkronisasi');
    Route::get('laporan_pembayaran_isaku', 'ProjectController@returnLaporanPembayaranIsaku');
    Route::get('laporan_transaksi_per_plu', 'ProjectController@returnLaporanTransaksiPerProduk');
    Route::get('laporan_pnl', 'ProjectController@returnLaporanLaporanPNL');
    Route::get('laporan_epp', 'ProjectController@returnLaporanLaporanEPP');
    Route::get('getpnldatatable', 'ProjectController@getPNLList');
    Route::get('getpnlreportfile/{unique}', 'ProjectController@getPNLReportFile');
    Route::get('getsalesdatatable','ProjectController@getSalesDatatable');
    Route::get('getsalesdatedatatable','ProjectController@getSalesDateDatatable');
    Route::get('getsalesproductdatatable','ProjectController@getSalesProductDatatable');
    Route::get('getsalesdaydatatable','ProjectController@getSalesDayDatatable');
    Route::get('getsalesbranchdatatable','ProjectController@getSalesBranchDatatable');
    Route::get('getsalesrecapdatatable','ProjectController@getSalesRecapDatatable');
    Route::get('getsalesmonthdatatable','ProjectController@getSalesMonthDatatable');
    Route::get('getexportSyncReportsalesbranchdatedatatable','ProjectController@getSalesBranchDateDatatable');
    //todo kalau mau naikin ke production, routes ini juga di naikin
    Route::post('exportformsales','ProjectController@exportSalesVersiYKN');
    Route::post('export_spd_std_apc','ProjectController@exportSalesVersiDKN');//default nya yang atas
    Route::post('export_sync_report','ProjectController@exportSyncReport');
    Route::post('export_isaku_payment','ProjectController@exportIsakuPayment');
    Route::post('export_laporan_transaksi_per_plu','ProjectController@exportLapTrxPerPlu');
    Route::post('export_epp_file','ProjectController@exportEPPFile');//default nya yang atas

    //pareto
    Route::get('laporanpareto', 'ProjectController@returnPareto');
    Route::get('getparetodatatable','ProjectController@getParetoDatatable');
    Route::post('exportformpareto','ProjectController@exportPareto');

    //pb
    Route::get('laporanpb', 'ProjectController@returnPb');
    Route::get('getpbdatatable','ProjectController@getPbDatatable');
    Route::post('exportformpb','ProjectController@exportPb');

    //promo
    Route::get('laporanpromosi', 'ProjectController@returnPromo');
    Route::get('getpromodatatable','ProjectController@getPromoDatatable');

    //arsip
    Route::get('laporanarsip', 'ProjectController@returnArchive');
    Route::get('getarsipdatatable','ProjectController@getArchiveDatatable');
    Route::post('exportformarsip','ProjectController@exportArchive');

    //stok
    Route::get('laporanstok', 'ProjectController@returnStock');
    Route::get('getstokdatatable','ProjectController@getStockDatatable');
    Route::post('exportformstok','ProjectController@exportStock');

    Route::get('laporanmap', function () {
        return view('admin/laporanmap');
    });

    //version
    Route::get('manageversion', function () {
        return view('admin/manageversion');
    });
    Route::post('devgetaccess', "ProjectController@getDevAccessToken");
    Route::post('devgetallver', "ProjectController@getDevAllVersion");
    Route::post('devupdatever', "ProjectController@devUpdateVersion");
    Route::post('devinsertver', "ProjectController@devInsertVersion");

    //create AdminCabang
    //
    Route::get('createAdmin', 'MemberController@GetCreateAdmin');
    Route::post('addadmin', 'MemberController@createAdmin');
    Route::get('listadmin', 'MemberController@getListAdmin');
    Route::get('admin/admintmi/datatable', 'MemberController@getAdminDatatable');
    Route::get('deleteadmin', 'MemberController@deleteAdmin');


    //route helper saja
    Route::post('encrypt_data', 'ProjectController@encryptString');

});
Route::get('get_dpc_file/{credit_number}', 'ProjectController@getCreditApprovalDoc');
Route::get('get_spph_file/{credit_number}', 'ProjectController@getStatementOfDebtAcknowledgment');

Route::post('export_sync_report_test','ProjectController@exportSyncReport');

//change Passwrd
Route::get('chpass', 'MemberController@getchangePassword');
//Route chpasswoerd


//Route For API

Route::post('gettipetmi', 'ProjectApiController@getMasterTipeTmi');
Route::post('getprodmast', 'ProjectApiController@getMasterPlu');

Route::post('authenticate', 'AuthAPIController@authenticate'); 
Route::get('getuserinfo', 'AuthAPIController@getUserFromToken');

Route::get('getcabang', 'ProjectApiController@getCabangApi');
Route::get('vanvan', function(){
    dd('test');
});

Route::get('export_spd_std_apc_van','ProjectController@exportSalesVersiDKN');//ini cmn dummy, nnti hapus aja
//Route::get('hello_user', function (){
//   $user = TDBUser::find(2);
//   dd($user);
//});


Route::get('exportvan', function (){
    $header = array();

    $row1[0] = 'PT. INTI CAKRAWALA CITRA - TMI';
    array_push($header, $row1);
    $row2[0] = 'PERIODE';
    $row2[1] = 'NULL';
    array_push($header, $row2);
    $row3[0] = 'IGR';
    $row3[1] = 'CABANG X';
    array_push($header, $row3);

    \Maatwebsite\Excel\Facades\Excel::create('vanexcel', function ($excel) use ($header){
        $excel->sheet('Sheetname', function($sheet) use ($header) {
            $test = array();

//            $a['A'] = 'data1';
//            $a['B'] = 'data2';
//            array_push($test, $a);
//            $a['A'] = 'data3';
//            $a['B'] = 'data4';
//            array_push($test, $a);
//            $a['A'] = 'data5';
//            $a['B'] = 'data6';
//            array_push($test, $a);
            $test = array(['data1', array('data2', 'data3')]);

            $sheet->fromArray($test);

        });
    })->download('xls');
});









