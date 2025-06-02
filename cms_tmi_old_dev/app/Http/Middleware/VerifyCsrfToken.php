<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'pluAjax',
        'editplu',
        'getlogin',
        'searchPlu',
        'memberAjax',
        'editmember',
        'aktivasimember',
        'registermember',
        'addmastermargin',
        'MarginAjax',
        'editmargin',
        'getprodmast', 'testvancurl',//test ini nnti dihapus ya
        'uploadmastermargin',
        'gettipetmi',
        'export_laporan_transaksi_per_plu',
        'submitplutolakan',
//        'get_plu_rejection_list',
        'get_detail_plu_rejection',
        'restore_plu_rejection',
        'deactivatemember',
        'resetpasswordmember',
        'getpnldatatablet',
        'getplugroup',
        'authenticate' , 'export_spd_std_apc', 'export_sync_report', 'export_sync_report_test', 'export_isaku_payment',
	'store_credit_request', 'export_epp_file', 'approve_credit', 'dpc_approval', 'get_detail_credit', 'encrypt_data',
        'get_employee_data',
        'get_sales_data'
    ];
}
