<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Models\Margin;
use App\Models\MarginDetail;
use App\Models\MasterPlu;
use App\Models\MasterToko;
use App\Models\Member;
use App\Models\TDBOauthAccessToken;
use App\Models\TDBUser;
use App\Models\TDBUserProduct;
use App\Models\User;
use App\Models\UserTmi;
use Carbon\Carbon;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\TDBEmployees;
use App\Models\TDBTmiType;
use App\Models\TDBUserCredit;
use App\Models\TDBUserApprovalToken;
use App\Models\TDBUserMigrationLog;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function getchangePassword()
    {
        return view('auth.chpass');
    }

    public function GetCreateAdmin(Request $request){

        $branchFormat="";

        $branch = \DB::table('branches')
            ->wherenotin('kode_igr', ['00'])
            ->get();

        foreach($branch as $index => $row) {
            $branchFormat .= "<option style='font-size: 12px;' value='" . $row->kode_igr . "'>" . $row->kode_igr . " --> " . $row->name . "</option>";
        }

        return view('admin.createadmin')->with('branch',$branchFormat);
    }

    public function getListAdmin(Request $request){

        $branchFormat="";

        $branch = \DB::table('branches')
            ->wherenotin('kode_igr', ['00'])
            ->get();

        foreach($branch as $index => $row) {
            $branchFormat .= "<option style='font-size: 12px;' value='" . $row->kode_igr . "'>" . $row->kode_igr . " --> " . $row->name . "</option>";
        }

        return view('admin.listadmin')->with('branch',$branchFormat);
    }

    public function getAdminDatatable(){

        $AdminTmi = \DB::table('users')
            ->Selectraw('users.id as iduser, branches.name as cabang, users.name as namaadmin, email,keterangan, null as action')
            ->leftJoin('branches', 'users.kode_igr', '=', 'branches.kode_igr')
            ->leftJoin('roles', 'users.role', '=', 'roles.id')
            ->where('role', '!=', 1)
            ->Get();

        $collection = collect($AdminTmi);


        foreach ($collection as $row) {
            $row->status = "<a href=\"#\" class=\"btn btn-danger btn-ok\"  data-toggle=\"modal\" data-target=\"#confirm-delete\" data-href=\"deleteadmin?id=" .$row->iduser ."\"><i class='fa fa-times'></i> Delete </a>";
        }
        return \Datatables::of($collection)->make(true);

    }

    public function deleteAdmin(Request $Request){

        \DB::beginTransaction();
        try {

            \DB::table('users')->where('id', $Request->get('id'))->delete();

            \DB::commit();

            return redirect('listadmin');
        } catch (Exception $ex) {
            \DB::rollBack();
            return redirect('listadmin')->with('err', 'Gagal menyimpan data, silahkan coba lagi');
        }


    }


    public function createAdmin(Request $Request){

        $date = new \DateTime;
        $email = $Request->get('email');
        $name = $Request->get('name');
        $role = 2;
        $kdcab = $Request->get('cabang');

        try {

            $this->validate($Request, [
                'email' => 'required|email|max:50|unique:users',
                'password' => 'required|string|min:6|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'password_confirmation' => 'required|same:password',
                'cabang' => 'required',
                'name' => 'required',
            ]);

            $newAdm = new User();
            $newAdm->email = $email;
            $newAdm->name = $name;
            $newAdm->password = bcrypt($Request->get('password'));
            $newAdm->kode_igr = $kdcab;
            $newAdm->role = $role;
            $newAdm->created_at = $date;
            $newAdm->updated_at = $date;
            $newAdm->created_by = \Auth::User()->email;
            $newAdm->save();

            return redirect('listadmin');

        } catch (Exception $e) {
            $Request->session()->flash('error_message', $e);
        }


    }


    public function getTipeTmi(Request $request){

        return view('admin.inputtipetmi');
    }

    public function getPostTipeTmi(Request $request){

        $kodetmi = $request->input('kodetmi');
        $namatmi = $request->namatoko;


        $kodetmi = \DB::table('tipe_tmi')
            ->where('kode_tmi', $kodetmi)
            ->pluck('kode_tmi');

        if(count($kodetmi) > 0){
            $request->session()->flash('error_message', 'Gagal menambahkan ,Kode TMI sama');
            return view('admin.inputtipetmi');
        }else{

            \DB::table('tipe_tmi')->insert(['nama' => $namatmi, 'kode_tmi' => $request->input('kodetmi'), 'created_by' => 'ADM']);

            \DB::connection(config('cms_config.connection_tmi_api'))->table('tmi_types')->insert(['code' => $request->input('kodetmi'), 'description' => $namatmi]);

            $request->session()->flash('success_message', 'Berhasil Menambahkan Kode TMI');
            return view('admin.inputtipetmi');

        }


    }

    public function getTipeTmiDatatable(){

        $TipeTmi = \DB::table('tipe_tmi')
            ->Get();

        $collection = collect($TipeTmi);


        foreach ($collection as $row) {
      }
        return \Datatables::of($collection)->make(true);

    }


    public function getMemberTmi(Request $request){
        ini_set('memory_limit', '-1');
        $memberFormat="";
        $tipeFormat="<option style='font-size: 12px;' value='0' selected>PILIH TIPE</option>";
        $branchFormat="<option style='font-size: 12px;' value='0' selected>PILIH CABANG</option>";
        $idcab = $request->get('id_cab');

        $countmember = \DB::connection(config('cms_config.connection_tmi_api'))->table('users')->count();


        if($countmember > 0){
            $memberTmi = TDBUser::distinct()->select('member_code')->lists('member_code')->toArray();

            $MemberBinaan = \DB::connection(config('cms_config.connection_tmi_cms'))->table('customers')
                ->whereNotIn('kode_member', $memberTmi)
                ->Take(10)
                ->Get();

            foreach($MemberBinaan as $index => $row) {
                $memberFormat .= "<option style='font-size: 12px;' value='" . $row->kode_member . "'>" . $row->kode_member . " --> " . $row->name . "</option>";
            }

        }

        $tipeMember = \DB::connection(config('cms_config.connection_tmi_cms'))->table('tipe_tmi')
            ->Get();

        foreach($tipeMember as $index => $row) {
            $tipeFormat .= "<option style='font-size: 12px;' value='" . $row->kode_tmi . "'>" . $row->nama . "</option>";
        }

        $branch = \DB::table('branches')
            ->get();

        foreach($branch as $index => $row) {
            $branchFormat .= "<option style='font-size: 12px;' value='" . $row->kode_igr . "'>" . $row->kode_igr . " --> " . $row->name . "</option>";
        }

        return view('admin.inputmember')->with('member',$memberFormat)->with('tipetmi',$tipeFormat)->with('branch',$branchFormat);
    }

    public function getCreditRequest(Request $request){
        //todo kerjain disini untuk credis limit
        $data_user = TDBUser::join('cms_tmi.customers', 'users.member_code', '=','customers.kode_member')
                    ->select(DB::raw('users.member_code as member_code, users.name as username, users.id as user_id, customers.id as customer_id'))
                    ->orderBy(DB::raw('UPPER(users.name)'), 'ASC')
                    ->get();
        $current_date = Carbon::now()->format('Y-m-d');

        // <option value="{{$user->member_code}}">{{$user->username}}({{$user->member_code}})</option>
        $option_member = '';
        foreach($data_user as $u){
            $option_member .= '<option value='.$u->member_code.'>'.$u->username.'('.$u->member_code.')</option>';
        }
        return view('admin.credit_request')->with('data_user',$option_member)->with('current_date', $current_date);
    }

    public function storeCreditRequest(Request $request){
        //ini di panggil dari function ini : getCreditRequest. Function ini ga di pake lagi
        $credit_limit_max = $request->credit_limit_max;
        $credit_period = $request->credit_period;
        $end_period = $request->end_period;
        $start_period = $request->start_period;
        $grace_period = $request->grace_period;
        $last_order_period = $request->last_order_period;
        $identity_card = $request->identity_card;
        $member_code = $request->member_code;
        $nominal_installment = $request->nominalinstallment;
        $is_employee = $request->is_employee;
        $nik = $request->nik;

        //$member_code = 'Z08443';//sementara hardcode dlu

        $data_user = TDBUser::where('member_code', '=', $member_code)
                    ->first();

        if(empty($data_user)){
            return response()->json(['status'=>0, 'message'=>'Kode Member Tidak di Temukan!']);
        }
        $uCredit = new TDBUserCredit();
        // $uCredit = TDBUserCredit::firstOrNew([
        //     'user_id'=>$data_user->id
        // ]);
        
        // if(!empty($uCredit)){
        //     return response()->json(['status'=>0, 'message'=>'Member sudah pernah di ajukan kredit']); 
        // }

        //'user_id', 'nik', 'credit_number', 'credit_status_id', 'credit_type_id', 'credit_limit', 'tenor',
        // 'last_order_period', 'start_period', 'end_period', 'grace_period', 'description',
        // 'approved_by', 'approved_date'
        $uCredit->user_id = $data_user->id;
        $uCredit->nik = $is_employee==1?$nik:null;
        $uCredit->identity_number = $identity_card;
        $uCredit->credit_number = 'NANTI AKAN DI GENERATE!';
        $uCredit->credit_status_id = 1;
        $uCredit->credit_type_id = $is_employee==1? 1 : 2;
        $uCredit->credit_limit = $credit_limit_max;
        $uCredit->tenor = $credit_period;
        $uCredit->last_order_period = $last_order_period;
        $uCredit->start_period = $start_period;
        $uCredit->end_period = $end_period;
        $uCredit->grace_period = $grace_period;
        // $uCredit->description = ;
        //kedua object dibawah ini blm ada isinya klo baru insert
        // $uCredit->approved_by = ;
        // $uCredit->approved_date = ;
        $uCredit->save();
        

        return response()->json(['status'=>1, 'message'=>$request->all()]);
        // $validator = Validator::make($request->all(), [
        //     'member_code'=>'required',
        //     'credit_limit_max'=>'required',
        //     'credit_period'=>'required',
        //     'start_period'=>'required',
        //     'end_period'=>'required',
        //     'nominalinstallment'=>'required'
        // ], [
        //     'required'=>':attribute harus di isi!'
        // ]);
        // if($validator->fails()){
        //     return (['error'=>'validation_error', 'message'=>$validator->errors());
        // }
        // return back()->with('success', json_encode($request->all()));
    }

    public function getSPPHPage(){
        
        $user_credit = TDBUserCredit::join('users', 'user_credits.user_id', '=', 'users.id')
            ->leftJoin('employees', 'user_credits.nik', '=', 'employees.nik')
            ->select('users.name', 'user_credits.credit_number', 'users.member_code', 'user_credits.credit_limit', 'user_credits.real_order')
            ->where('user_credits.credit_status_id', '=', 2)
            ->get();
        return view('admin.submit_spph')->with('pending_approval_list',$user_credit);
    }

    public function getEmployeePage(){
        
        $employees = TDBEmployees::LeftJoin('user_credits', 'employees.nik', '=', 'user_credits.nik')
            ->leftJoin('user_credit_statuses', 'user_credits.credit_status_id', '=', 'user_credit_statuses.id')
            ->leftJoin('users', 'user_credits.user_id', '=', 'users.id')
            ->select('employees.name', 'employees.nik', 'employees.rec_letter_number', 
            'employees.ticket_number', 'employees.unit_id', 'employees.job_position', 
            DB::raw('IFNULL(users.member_code, "-") as member_code'), 
            DB::raw('IFNULL(user_credit_statuses.name, "-") as dpc_status'), DB::raw('(
                CASE WHEN user_credits.real_order IS NULL THEN "BELUM"
                ELSE "SUDAH"
                END
            ) as is_spph_submit'))
            ->get();
        return view('admin.employee_list')->with('employee_list',$employees);
    }

    public function getEmployeeData(Request $request){
        $rec_letter = $request->rec_letter;
        if(empty($rec_letter)){
            return response()->json(['status'=>0, 'message'=>'Nomor Rekomendasi harus di isi!']);
        }
        $helper = new Helper();
        // 'hr_domain' => 'http://172.20.12.24/RESTSecurityDev/RESTSecurity.svc/'
        //
        /**http://172.20.12.25/TMIRestAPI/LoginESSREST.svc/SentDataTransferToICC */
        $url = config('cms_config.hr_domain').'SentDataTransferToICC';
        $body = '{'.
                    '"user":{'.'"nik":"IGR_HO", "pass": "tmi_icc_IGR_HO", "nosurat": "'.$rec_letter.'"}'.
                '}';
        $response = $helper->curlHelper('POST', $url, 0, $body);
        $employee_data = $response['SentDataTransferToICCResult'];
        if(count($employee_data) <= 0){
            return response()->json(['status'=>0, 'message'=>'Tidak ada data karyawan dari Nomor Rekomendasi yang anda masukkan']);
        }
        DB::beginTransaction();
        foreach($employee_data as $data){
            $employee = TDBEmployees::updateOrCreate([
                'nik'=>$data['NIK']
            ],[
                'ticket_number'=>$data['NO_TIKET'],
                'rec_letter_number'=>$rec_letter,                
                'name'=>$data['NAMA'],
                'unit_name'=>$data['UNIT_ID'],
                'unit_id'=>$data['UNIT_ID'],
                'branch_name'=>null,//belum di lampirkan oleh sd2
                'branch_id'=>empty($data['CABANG_ID'])?null:$data['CABANG_ID'],
                'job_position'=>$data['JABATAN'],
                'path_proposal'=>null,//belum di lampirkan oleh sd2
                'va_number'=>empty($data['NOMOR_VIRTUAL_ACCOUNT'])?null:$data['NOMOR_VIRTUAL_ACCOUNT']
            ]);
        }
        DB::commit();
        return response()->json(['status'=>1, 'message'=>'Data karyawan berhasil di tambahkan!']);
    }

    public function getDPCApproval(){
        //todo kerjain disini untuk credis limit
        // $data_user = TDBUser::join('cms_tmi.customers', 'users.member_code', '=','customers.kode_member')
        //             ->select(DB::raw('users.member_code as member_code, users.name as username, users.id as user_id, customers.id as customer_id'))
        //             ->orderBy(DB::raw('UPPER(users.name)'), 'ASC')
        //             ->get();
        // $current_date = Carbon::now()->format('Y-m-d');
        
        $user_credit = TDBUserCredit::join('users', 'user_credits.user_id', '=', 'users.id')
            ->leftJoin('employees', 'user_credits.nik', '=', 'employees.nik')
            ->select('users.name', 'user_credits.credit_number', 'users.member_code', 'user_credits.credit_limit', 'user_credits.credit_status_id')
            ->get();
        return view('admin.dpc_approval')->with('pending_approval_list',$user_credit);
    }

    public function getCreditApprovalWaitingList(Request $request){
        $data = array();
        for($i=0; $i<10; $i++){
            array_push($data, array(
                'data1'=>'key_a'.$i,
                'data2'=>'key_b'.$i
            ));
        }
        return \Datatables::of($data)->make(true);
    }

    public function getDetailCredit(Request $request){
        $credit_number = $request->credit_number;
        if(empty($credit_number)){
            return response()->json(['status'=>0, 'message'=>'Nomor Credit harus di isi!']);
        }


        $uCredit = TDBUserCredit::join('users', 'user_credits.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
            	->join('user_credit_statuses', 'user_credits.credit_status_id', '=', 'user_credit_statuses.id')
                ->where('user_credits.credit_number', '=', $credit_number)
                ->select('users.name', 'users.member_code', 'user_credits.credit_number', 'users.store_name',
                    DB::raw('user_credits.credit_limit as total_credit'), 'user_credits.tenor',
                    DB::raw('DATE(user_credits.start_period) as start_period'), 'user_credits.va_number',
                    DB::raw('DATE(user_credits.end_period) as end_period'), 'user_credits.real_order', 
                    'user_credits.credit_status_id', 'user_credit_statuses.name as credit_status_name', 'branches.code as branch_code',
                    DB::raw('"" as igr_total_sales'))
                ->first();
        if(empty($uCredit)){
            return response()->json(['status'=>0, 'message'=>'Nomor Kredit tidak di temukan!']);
        }
///////////////////////////////////////////////////////////////////////////////////////////////////
        //request igr_sales!
        $message = array();

        $body = array(
            'branch_code'=>$uCredit->branch_code,
            'member_code'=>$uCredit->member_code,
            'dpc'=> $uCredit->credit_number
        );
	
        $helper = new Helper();
        $IgrSales = $helper->curlhelper(
            'POST', config('cms_config.gateway_branch_api').'client/inq',
            2, $body
        );

        $status = $IgrSales['status'];
        if($status != 1){
            $message['status'] = 0;
            $message['message'] = $IgrSales['message'];
            // return response()->json($message);
        }

        $data = $IgrSales['data'];
        if(count($data) <= 0){
            $message['status'] = 0;
            $message['message'] = 'Data yang di minta tidak ada pada kasir IGR!';
            // return response()->json($message);
        }
        $data = $data[0];
        $dpc = $data['tmd_nomor_dpc'];

        $user_credit = TDBUserCredit::where('credit_number', '=', $dpc)
                    ->first();
        
        $igr_total_sales = $data['total'];
        $last_invoice_date = $data['tgl_akhir'];
        if(empty($last_invoice_date)){
            $message['status'] = 0;
            $message['message'] = 'Tanggal Akhir Struk tidak diberikan!';
            // return response()->json($message);
        } else{
            $user_credit->update([
                'max_invoice_date'=>$last_invoice_date
            ]);
        }

        if(empty($user_credit)){
            $message['status'] = 0;
            $message['message'] = 'Nomor DPC yang di terima dari cabang tidak di temukan!';
            //nomor dpc yang dikirim oleh api gateway grosir tidak di temukan di mysql tmi
            // return response()->json($message);
        }

        $uCredit->igr_total_sales = $igr_total_sales;

        return response()->json(['status'=>1, 'message'=>$uCredit, 'request_message'=>$message]);
    }

    public function approveDPC(Request $request){
        $credit_number = $request->credit_number;
        $approval_token = $request->approval_token;

        if(empty($credit_number)){
            return response()->json(['status'=>0, 'message'=>'Nomor pengajuan harus di isi!']);
        }
        if(empty($approval_token)){
            return response()->json(['status'=>0, 'message'=>'Approval token harus di isi!']);
        }
        $uCredit = TDBUserCredit::where('credit_number', '=', $credit_number)
                    ->first();
        if(empty($uCredit)){
            return response()->json(['status'=>0, 'message'=>'Nomor pengajuan tidak di temukan!']);
        }

        $userApprovalToken = TDBUserApprovalToken::where('id','=', $uCredit->user_approval_token_id)
                            ->first();
                            //todo klo sudah bener semua, uncomment kodingan di bawah!
        if(empty($userApprovalToken)){
            return response()->json(['status'=>0, 'message'=>'Persetujuan ini belum memiliki token!']);
        }
        if($userApprovalToken->token != $approval_token){
            return response()->json(['status'=>0, 'message'=>'Token yang anda masukkan tidak valid!']);
        }
        ////////////////////// api untuk kirim ke igr gateway
        $helper = new Helper();
// 560194
        $token = $helper->getIgrGatewayAccessToken();
        
        if($token['status'] == 0){
            return response()->json(['status'=>0, 'message'=>$token['message']]);
        }
        $url = config('cms_config.gateway_branch_api').'client/insertCredit';
        $ch = curl_init();

        $credit_data = TDBUserCredit::join('users', 'user_credits.user_id', '=', 'users.id')
                    ->join('branches', 'users.branch_id', '=', 'branches.id')
                    ->select('branches.code as branch_code', 'users.member_code as member_code', 'user_credits.credit_number as dpc',
                    'user_credits.credit_limit as credit_limit', 'user_credits.tenor as tenor', 'user_credits.last_order_period as date_end')
                    ->where('user_credits.credit_number', '=', $credit_number)
                    ->first();
        $data = array(
            'credit_limit'=>$credit_data->credit_limit,
            'branch_code'=>$credit_data->branch_code,
            'member_code'=>$credit_data->member_code,
            'dpc'=>$credit_data->dpc,
            'tenor'=>$credit_data->tenor,
            'date_end'=>$credit_data->date_end
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer '.$token['message']
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 400);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_PROXY, '');

        $response = curl_exec($ch);
        curl_close($ch);
        $get_response = json_decode($response, true);

        $status = $get_response['status'];

        if($status == 0){
            return response()->json(['status'=>0, 'message'=>$response]);
        }

        $userApprovalToken->is_used = 1;
        $userApprovalToken->save();
        $userApprovalToken->delete();
        
        $uCredit->credit_status_id = 2;
        $uCredit->save();

        return response()->json(['status'=>1, 'message'=>'Nomor pengajuan berhasil di setujui!']);
    }
    
    public function getIGRSalesData(Request $request){
        $message['status'] = 0;
        $message['message'] = '';

        $member_code = $request->member_code;
        $branch_code = $request->branch_code;
        $dpc = $request->dpc;

        if(empty($member_code)||empty($branch_code)||empty($dpc)){
            $message['message'] = 'Silahkan lengkapi data!';
            return response()->json($message);
        } 
        //branch_code : 39
        // member_code : AD2133
        // dpc : TMI/C-NK/2021/03/29-AD2133-001
        $body = array(
            'branch_code'=>$branch_code,
            'member_code'=>$member_code,
            'dpc'=> $dpc
        );
    
        $helper = new Helper();
        $IgrSales = $helper->curlhelper(
            'POST', 'http://172.20.28.23:8080/ho-gateway-dev/public/api/client/inq',
            2, $body
        );

        $status = $IgrSales['status'];
        if($status != 1){
            $message['status'] = 0;
            $message['message'] = $IgrSales['message'];
            return response()->json($message);
        }

        $data = $IgrSales['data'];
        if(count($data) <= 0){
            $message['status'] = 0;
            $message['message'] = 'Data yang di minta tidak ada pada kasir IGR!';
            return response()->json($message);
        }
        $data = $data[0];
        $dpc = $data['tmd_nomor_dpc'];
        
        $igr_total_sales = $data['total'];
        $last_invoice_date = $data['tgl_akhir'];
        if(empty($last_invoice_date)){
            $message['status'] = 0;
            $message['message'] = 'Tanggal Akhir Struk tidak diberikan!';
            return response()->json($message);
        }

        $user_credit = TDBUserCredit::where('credit_number', '=', $dpc)
                    ->first();
        if(empty($user_credit)){
            $message['status'] = 0;
            $message['message'] = 'Nomor DPC yang di terima cabang tidak di temukan!';
            return response()->json($message);
        }
        $user_credit->update([
            'max_invoice_date'=>$last_invoice_date
        ]);
        $message['status'] = 1;
        $message['message'] = $igr_total_sales;
        return response()->json($message);
    }

    public function sendCreditApproved(Request $request){
        $is_approve = $request->is_approve;
        $credit_number = $request->credit_number;
        $fix_order = $request->fix_order;
        $va_number = $request->va_number;

        if(empty($is_approve)){
            return response()->json(['status'=>0,'message'=>'Tidak ada informasi di setujui atau tidak!']);
        } else if(empty($credit_number) && empty($fix_order) && empty($va_number)){
            return response()->json(['status'=>0,'message'=>'Nomor Pengajuan, Nominal Fix Order, dan nomor VA harus di isi!']);
        } else if(empty($credit_number)){
            return response()->json(['status'=>0,'message'=>'Nomor Pengajuan harus di isi!']);
        } else if($is_approve == 1 && empty($fix_order)){
            return response()->json(['status'=>0,'message'=>'Nominal Fix order harus di isi!']);
        } else if($is_approve == 1 && empty($va_number)){
            return response()->json(['status'=>0,'message'=>'Nomor VA harus di isi!']);
        }
        if($is_approve == 2){
            $fix_order = 0;
        }
        $curl = curl_init();
        $auth_data = array(
            'client_id' 		=> 1,
            'client_secret' 	=> 'RkUeQyBuyiQG78sv9FA4BajVnGGdolhbGfR2YrFD',
        );

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $auth_data);
        curl_setopt($curl, CURLOPT_URL, config('cms_config.tmi_api').'login_tmi_machine_api');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_PROXY, '');

        $result = curl_exec($curl);
        
        if(!$result){die("Connection Failure");}
        curl_close($curl);

        $data_login_machine = json_decode($result, true);

        if($data_login_machine['status'] == 0){
            return response()->json(['status'=>0, 'message'=>$data_login_machine['message']]);
        }

        $token =  json_decode(trim($result))->access_token;
        $token = $token;

        $url = config('cms_config.tmi_api').'send_employee_data_to_hr';
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'is_approve'=>$is_approve,
            'credit_number'=>$credit_number,
            'fix_order'=>$fix_order,
            'va_number'=>$va_number
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer '.$token
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 400);
        curl_setopt($ch, CURLOPT_PROXY, '');

        $get_response = curl_exec($ch);
        // return response()->json(['status'=>0, 'message'=>$get_response]);

        curl_close($ch);
        $get_response = json_decode($get_response, true);
        
        $status = $get_response['status'];
        if($status == 1){
            $uCredit = TDBUserCredit::where('credit_number', '=', $credit_number)
                        ->update(['va_number'=>$va_number]);
                        
            return response()->json(['status'=>1, 'message'=>$get_response['message']]);
        } else {
            return response()->json(['status'=>0, 'message'=>$get_response['message']]);
        }
    }

    public function getIGRSales(Request $request){
        
    }

    public function getCreditCreditTMIGO(){
        $file_name = 'DPC[YYMMDD].csv';
        

    }

    public function getMemberDatatables(){
        $MemberTmiAssoc = TDBUser::SelectRaw('email, store_name, branches.name as cabang,'. 
                'tmi_types.description as tipetmi, member_code, users.id as idmember, flag_active,'.
                'users.phone_number as phone_number, users.address as addressmember, count(user_products.id) as total_products')
            ->leftJoin('tmi_types', 'users.tmi_type_id', '=', 'tmi_types.id')
            ->leftJoin('branches', 'users.branch_id', '=', 'branches.id')
            ->leftJoin('user_products', 'users.id', '=', 'user_products.user_id')
            ->groupBy('users.id')
            ->get();

        foreach ($MemberTmiAssoc as $row) {
            if ($row->flag_active == 0) {
//                $row->aksi .= "<span>-</span>";
                $row->status .= "<span class='label label-danger flat' style='font-size: 15px; font-weight: bold;'>Member Belum Aktif</span>";
            }else{
                $row->status = '<button id="btn_edit_member" type="button" class="btn btn-info flat" style="width:70px;margin-bottom: 5px;" value="' . $row->idmember . '"> Edit </button>';
//                $row->status = "<span class='label label-info flat' style='font-size: 15px; font-weight: bold;'>Member Sudah Aktif</span>";
            }

        }
        return \Datatables::of($MemberTmiAssoc)->make(true);

    }

    public function get_member(Request $request){
        $html_cab = '<option value=0>-- Pilih Member --</option>';
        $idcab = $request->get('id_cab');

        $memberTmi = TDBUser::distinct()->select('member_code')->lists('member_code')->toArray();

        $MemberBinaan = \DB::table('customers')
            ->whereNotIn('kode_member', $memberTmi)
            ->where('kode_igr', $idcab)
//            ->Take(10)
            ->Get();

        foreach ($MemberBinaan as $key => $row) {
            $html_cab .= "<option style='font-size: 12px;' value='" . $row->kode_member . "'>" . $row->kode_member . " --> " . $row->name . "</option>";
        }
        return $html_cab;
    }

    public function getRegisterMember(Request $request){
        //$tempData = str_replace("\\", "",$postedData);
        // $cleanData = json_decode($tempData);
        // var_dump($cleanData);
        $credit_request = json_decode(json_decode($request->credit_request, true));
        
        if(!empty($credit_request)){
            if($credit_request->is_need_credit == 1){
                $today = Carbon::now()->format('Y/m/d');
                
                $credit_number = 'TMI/C-';
                //TMI/C-K/2021/02/01-789861-001
                //TMI/C-NK/2021/02/01-789861-001
                if($credit_request->is_idm_employee == 1){
                    $credit_number .= 'K/';
                } else{
                    $credit_number .= 'NK/' ;
                }
                $credit_number .= $today.'-'.$request->member.'-001';// 001 ini di hardcode karena pas register, member baru pertama ngajuin kredit. nanti di review lagi code nya
                $credit_request->credit_number = $credit_number;
            }
        }
        $getnama = \DB::table('customers')
//            ->Select('name')
            ->where('kode_member', $request->member)
            ->Pluck('name');

        \DB::beginTransaction();
        try{
            $igr_code = $request->txt_cab;

            // if($igr_code == '01'){
            //     $con = 'igrcpg';
            // }elseif($igr_code == '03'){
            //     $con = 'igrsby';
            // }elseif($igr_code == '04'){
            //     $con = 'igrbdg';
            // }elseif($igr_code == '05'){
            //     $con = 'igrtgr';
            // }elseif($igr_code == '06'){
            //     $con = 'igrygy';
            // }elseif($igr_code == '15'){
            //     $con = 'igrmdn';
            // }elseif($igr_code == '16'){
            //     $con = 'igrbks';
            // }elseif($igr_code == '17'){
            //     $con = 'igrplg';
            // }elseif($igr_code == '18'){
            //     $con = 'igrkmy';
            // }elseif($igr_code == '20'){
            //     $con = 'igrpku';
            // }elseif($igr_code == '21'){
            //     $con = 'igrsmd';
            // }elseif($igr_code == '22'){
            //     $con = 'igrsmg';
            // }elseif($igr_code == '25'){
            //     $con = 'igrbgr';
            // }elseif($igr_code == '26'){
            //     $con = 'igrptk';
            // }elseif($igr_code == '27'){
            //     $con = 'igrbms';
            // }elseif($igr_code == '28'){
            //     $con = 'igrmdo';
            // }elseif($igr_code == '31'){
            //     $con = 'igrmks';
            // }elseif($igr_code == '32'){
            //     $con = 'igrjbi';
            // }elseif($igr_code == '33'){
            //     $con = 'igrkri';
            // }elseif($igr_code == '35'){
            //     $con = 'igrcpt';
            // }elseif($igr_code == '36') {
            //     $con = 'igrkrw';
            // }elseif($igr_code == '37') {
            //     $con = 'igrmlg';
            // }elseif($igr_code == '34') {
            //     $con = 'igramb';
            // }elseif($igr_code == '38') {
            //     $con = 'igrbdl';
            // }elseif($igr_code == '39'){
            //     $con = 'igrslo';
            // }else{
            //     return response()->json(['status'=>0,'message'=>'Silahkan Pilih Cabang!']);
            // }

            //ongkir_exp_date
            //go_date
//            return response()->json(['status'=>0, 'message'=>($request->ongkir_exp_date. ' | ' . $request->go_date)]);

            $response = array();

            $response["email"] = $request->email;
            $response["password"] = $request->password;
            $response["user_name"] = $getnama;
            $response["store_name"] = $request->namatoko;
            $response["branch_code"] = $request->txt_cab;
            $response["member_code"] = $request->member;
            $response["flag_active"] = 0;
            $response["tmi_code"] = $request->txt_tipe;
            $response["phone_number"] = $request->nohape;
            $response["address"] = $request->alamat;
            $response["created_at"] = Carbon::now();
            $response["updated_at"] = Carbon::now();
            $response["created_by"] = 'ADM';
            $response["total_product"] = 0;
            $response["ongkir_exp_date"] = $request->ongkir_exp_date;
            $response["go_date"] = $request->go_date;


            $curl = curl_init();
            $auth_data = array(
                'client_id' 		=> 1,
                'client_secret' 	=> 'RkUeQyBuyiQG78sv9FA4BajVnGGdolhbGfR2YrFD',
            );
            // dd((config('cms_config.tmi_api').'login_tmi_machine_api'));
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $auth_data);
            curl_setopt($curl, CURLOPT_URL, config('cms_config.tmi_api').'login_tmi_machine_api');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_PROXY, '');

            $result = curl_exec($curl);
            
//            return $result;
            if(!$result){die("Connection Failure");}
            curl_close($curl);

            $data_login_machine = json_decode($result, true);

            if($data_login_machine['status'] == 0){
                return response()->json(['status'=>0, 'message'=>$data_login_machine['message']]);
            }

            $token =  json_decode(trim($result))->access_token;
            $token = $token;
            // dd($token);
            // dd('panjang karakter : '. strlen($token), $token);
            // $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImRjNDY3M2I2NzVmM2NiNGJjZTVhNTQ1NzAzZDFjNjc4YWI4YzkyYTkzNTU1YTI1ZDhlODE1MmI2NDZiYTFkMWQzYzdkZjM4ZmVhODA4N2VmIn0.eyJhdWQiOiIxIiwianRpIjoiZGM0NjczYjY3NWYzY2I0YmNlNWE1NDU3MDNkMWM2NzhhYjhjOTJhOTM1NTVhMjVkOGU4MTUyYjY0NmJhMWQxZDNjN2RmMzhmZWE4MDg3ZWYiLCJpYXQiOjE2MTI5MzM0ODksIm5iZiI6MTYxMjkzMzQ4OSwiZXhwIjoxNjE0MTQzMDg5LCJzdWIiOiIiLCJzY29wZXMiOltdfQ.YphTlAKuYF4Erq0BKLIOBgsaIOeWaaijvSXjoFEDiPksSwmouv9Wwgo7LCwywHOHv1DaV7lArZqg6Q65SAxoiVpz8VOf5HD6Nie8f6BPqKFoRubBjW7pj9lgbiON3YBfDN3gwdMhqxAbCfrhWifOGI7WgqPSVIZnKtWPStQZ2KsBixg6knYvsyz_l0WJJ0muL8Orfg_fp_2x2J-sthsToV_JO3bhb2fXa3Gz8YboQVPr1Iyx96Y5LSF6I6Vxrckjupsim1xj2XT_6g6gho_ee4SNAzJ2SWvejjM4_2AT1L7VC2Y0v9OoUWMH1Km09pTbz5aWbKSdWhKc3VHplEGHKOCW31R_3GoICD3RixOxhtFyJEBbrWfbYynOdM_PfBY93118FFEttqg3wKjHW9Fc_KTLfnS-etjGVYGklkwop4Zh8WVNF1i7DXc2Tqx00jq1-FrYPC3C-ZT2XFn_U4WvcC9h9eusvInxze8BunpScfDb29Hb_-E9DZLB0bfIRw1XTghrVaL2PeK8ZOrK2Mf7SNaYL-UbYWSllQqjM3iEaovtwRJI0HbcTpsI-TEs1HolwOvuEQ3jZyjJG3rGba7qhaci5Q7LapShGdLnO0EUcYCGwY0qAtwRJc98mwoiICQWHQ4YM3tR7rsul95otyzGaQPPkmTH2H7Vb7Ep2lVjIZ8';
            $ch = curl_init(config('cms_config.tmi_api').'register_member_api');
//            curl_setopt($ch, CURLOPT_URL, $ch);
            curl_setopt($ch, CURLOPT_POST, true);
            // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//                'X-Authorization: d22cc9cf7ba5462da85d62c8b856e185e112c361'
//            ));

            curl_setopt($ch, CURLOPT_PROXY, '');

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer ' . $token,
            ));

            //$response["ongkir_exp_date"] = $request->ongkir_exp_date;
            //            $response["go_date"] = $request->go_date;
            $array = ['user_name'=>$getnama, 'email'=>$request->email, 'store_name'=>$request->namatoko,
                'password'=>$request->password, 'phone_number'=>$request->nohape, 'member_code'=>$request->member,
                'branch_code'=>$request->txt_cab, 'address'=>$request->alamat, 'flag_active'=>0,
                'tmi_code'=>$request->txt_tipe, 'total_product'=>0, 'limit_free_plu'=>$request->limitfreeplu,
                'ongkir_exp_date'=>$request->ongkir_exp_date, 'go_date'=>$request->go_date,
                'created_by'=>'ADM', 'credit_request'=>json_encode($credit_request)];

            $data2 = http_build_query($array);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data2);

            curl_setopt($ch, CURLOPT_TIMEOUT, 600);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 600);
            //execute post
            $result = curl_exec($ch);
            //close connection
            curl_close($ch);
            $data = json_decode($result, true);
//            return response()->json(['message'=>json_encode($data), 'status'=>0]);
            return response()->json($data);
            //hanya sampai kodingan atas aja!
//            return 'TAGVANVAN';
            //update Jenis Member
            $curl = curl_init();
            $auth_data = array(
                'grant_type' => 'password',
                'username' 	=> 'CMS_TMI',
                'password' 	=> 'CMS_TMI',
            );

            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $auth_data);
            curl_setopt($curl, CURLOPT_URL, 'gateway.indogrosir.co:801/api/oauth/token');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

            $result = curl_exec($curl);
            $result = json_decode($result);

            $token = $result->access_token;

            $data = array(
                'branch_code' => $request->txt_cab,
                'member_code' 	=> $request->member,
            );

            //can't read array in CURL
            $data2 = http_build_query($data);
            
            $url ="gateway.indogrosir.co:801/api/client/tmi-aws/member/update";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer ' . $token,
                'Accept:application/json'
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response1  = curl_exec($ch);

            //close connection
            curl_close($ch);

            \DB::commit();

            return 'TRUE';

        }catch(Exception $ex){
            \DB::rollBack();
            return redirect('register')->with('err', 'Gagal menyimpan data, silahkan coba lagi');
        }

    }

    public function EditMember(Request $request){
        $user = Auth::user();
        
        $id = $request->id;
        $kodeTmi = $request->tipetmi;
        $reasonChange = $request->reason_change;
        
        $message = array();

        try{        
        $data = TDBUser::find($id);
        // $data->tmi_type_id = $request->tipetmi;
        
        $tmiData = TDBTmiType::find($data->tmi_type_id);
        if(!empty($kodeTmi) 
                && $kodeTmi != 0 
                && $kodeTmi != $tmiData->code) {
            $helper = new Helper();
            // $getTmiAccessToken = $helper->getTmiMachineAccessToken();
            // $result = $getTmiAccessToken['status'];
            // if($result != 1) {
            //     $message['status'] = 0;
            //     $message['message'] = $getTmiAccessToken['message'];
            //     return $message;
            // }
            // $accessToken = $getTmiAccessToken['message'];

            $response = $helper->curlHelper('POST', 
            config('cms_config.tmi_api').'migrate_member'
            , $helper->REQUEST_TMI_ACCESS_TOKEN, array(
                'member_code'=>$data->member_code,
                'new_tmi_code'=>$kodeTmi,
                'reason'=>$reasonChange,
                'admin_id'=>$user->id
            ));
            if($response['status'] != 1) {
                $errorMessage = $response['message'];
                if(!empty($response['error_message'])){
                    if($response['error_message'] != null){
                        $temp = $response['error_message'];
                        $errorMessage ="";
                        if(!empty($temp['member_code'])){
                            foreach($temp['member_code'] as $t) {
                                $errorMessage = $errorMessage . "+ " . $t. "\n";
                            }
                        }
                        if(!empty($temp['new_branch_code'])){
                            foreach($temp['new_branch_code'] as $t) {
                                $errorMessage = $errorMessage . "+ " . $t. "\n";
                            }
                        }
                        if(!empty($temp['new_tmi_code'])) {
                            foreach($temp['new_tmi_code'] as $t) {
                                $errorMessage = $errorMessage . "+ " . $t. "\n";
                            }
                        }
                        if(!empty($temp['reason'])) {
                            foreach($temp['reason'] as $t) {
                                $errorMessage = $errorMessage . "+ " . $t. "\n";
                            }
                        }
                        if(!empty($temp['admin_id'])) {
                            foreach($temp['admin_id'] as $t) {
                                $errorMessage = $errorMessage . "+ " . $t. "\n";
                            }
                        }
                    }
                }
                $message['status'] = 0;
                $message['message'] = $errorMessage;
                return $message;
            }
            $data = TDBUser::find($id);
        }
        $data->store_name = $request->namatoko;
        $data->phone_number = $request->nohp;
        $data->address = $request->address;
        $data->email = $request->email;
        $data->limit_free_plu = empty($request->limit_free_plu)?0:$request->limit_free_plu;
        $data->free_shipping = $request->free_ongkir_exp_date;
        $data->save();

        }catch(Exception $ex){
            $message['status'] = 0;
            $message['message'] = '[ERROR]'.$ex->getMessage();
            return $message;
        }

        $message['status'] = 1;
        $message['message'] = 'SUCCESS';
        return $message;

    }

    public function DeactivateMember(Request $request)
    {
        $member_code = $request->member_code;
        if(empty($member_code)){
            return response()->json(['status'=>0, 'message'=>'Kode Member harus di isi!']);
        }
        try {
            //delete
            $user = TDBUser::where('member_code', $member_code)->first();
            if(empty($user)){
                return response()->json(['status' => 0, 'message' => 'Kode Member tidak di temukan!']);
            }
            //todo test kodingan $user->delete();
            $user_id = $user->id;
            $accessToken = TDBOauthAccessToken::where('user_id', $user_id)
                            ->update(['revoked'=>1]);

            $user->delete();
            return response()->json(['status' => 1, 'message' => 'Member berhasil di hapus']);
        }catch (Exception $e){
            return response()->json(['status' => 0, 'message' => $e->getMessage()]);
        }
        return response()->json(['status'=>0, 'message'=>'Terjadi kesalahan!']);
    }

    public function ResetPasswordMember(Request $request)
    {
        $member_code = $request->member_code;
        if(empty($member_code)){
            return response()->json(['status'=>0, 'message'=>'Kode Member harus di isi!']);
        }
        try{
            $user = TDBUser::where('member_code', $member_code)
                ->update(['password'=>bcrypt('123456')]);
            return response()->json(['status' => 1, 'message' => 'Berhasil me-reset ulang password member!']);
        }catch (Exception $e){
            dd($e);
        }
        return response()->json(['status'=>0, 'message'=>'Terjadi kesalahan!']);
    }

    public function getMemberAjax(Request $request){
        $id = $request->id;
        $user = \Auth::user();
        $data = TDBUser::distinct()
            ->SelectRaw('email, store_name, branches.name as cabang, 
            tmi_types.description as tipetmi, member_code, users.id as idmember, 
            flag_active, users.phone_number as phone_number, users.address as addressmember, 
            users.limit_free_plu as limitfreeplu, users.free_shipping')
//            ->leftJoin('tipe_tmi', 'users.tmi_type_id', '=', 'tipe_tmi.kode_tmi')
            ->leftJoin('tmi_types', 'users.tmi_type_id', '=', 'tmi_types.id') //[12-06-2020 #VANVAN] sebelumnya salah join, harusnya join ke kolom tmi_types.id
            ->leftJoin('branches', 'users.branch_id', '=', 'branches.id') // ini sama kasus nya seperti yang di atas
            ->Where('users.id', $id)
            ->get();
        $data[0]['iduser'] = $user->id;

        return $data;
    }

    public function migrateDataMember(Request $request){
        $old_user_id = $request->user_id_old;
        $new_user_id = $request->user_id_new;

        if(empty($old_user_id)){
            return response()->json(['status'=>0, 'message'=>'Data user lama harus di isi!']);
        }
        if(empty($new_user_id)){
            return response()->json(['status'=>0, 'message'=>'Data user baru harus di isi!']);
        }

        $old_user = TDBUser::select('users.*', 'tmi_types.description as tmi_description')
                    ->join('tmi_types', 'users.tmi_type_id', '=', 'tmi_types.id')
                    ->where('users.user_id','=',$old_user_id)
                    ->first();
        if(empty($old_user)){
            return response()->json(['status'=>0, 'message'=>'data member lama tidak di temukan!']);
        } else if(str_contains(strtoupper($old_user->tmi_description), 'BESAR')){
            return response()->json(['status'=>0, 'message'=>'Migrasi member hanya di khususkan tipe kecil saja!']);
        }
        $new_user = TDBUser::where('user_id', '=', $new_user_id)
                    ->first();
        if(empty($new_user)){
            return response()->json(['status'=>0, 'message'=>'data member baru tidak di temukan!']);
        }

        $user_product_new = TDBUserProduct::where('user_id', '=', $new_user_id)
                            ->count();
        if($user_product_new <= 0){
            return response()->json(['status'=>0, 'message'=>'Data produk member baru belum tersedia!']);
        }

        $user_product_old = TDBUserProduct::select('user_products.*', 'products.plu as plu_grosir')
                            ->join('products', 'user_products.product_id', '=', 'products.id')
                            ->where('user_id','=', $old_user_id)
                            ->get();
        
        if($user_product_old->count() <= 0){
            return response()->json(['status'=>0, 'message'=> 'Data produk member lama tidak tersedia!']);
        }

        try{
            DB::beginTransaction();

            
        foreach($user_product_old as $old){
            $user_products = TDBUserProduct::where('user_id', '=', $new_user_id)
                            ->where(DB::raw('SUBSTR(plu, 1, 6)'), '=', substr($old->plu_grosir, 0, 6))
                            ->update([
                                'price'=>$old->price,
                                'stock'=>$old->stock,
                                'cost'=>$old->cost
                                ]);
        }
        $uml = new TDBUserMigrationLog();
        $uml->user_id_old = $old_user_id;
        $uml->user_id_new = $new_user_id;
        $uml->save();
        DB::rollback();
        }catch(\Exception $e){
            DB::rollback();
        }
        return response()->json(['status'=>0, 'message'=>'Migrasi berhasil !']);
    }

}
