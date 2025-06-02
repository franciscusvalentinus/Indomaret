<?php

namespace App\Http\Controllers;

use App\Model\TDBMasterPnl;
use App\Model\TDBPnlTrxDetail;
use App\Model\TDBPnlTrxHeader;
use App\Models\Margin;
use App\Models\MarginDetail;
use App\Models\MasterPlu;
use App\Models\MasterToko;
use App\Models\Member;
use App\Models\TDBBranch;
use App\Models\TDBLogSync;
use App\Models\TDBOperator;
use App\Models\TDBPbDetail;
use App\Models\TDBPbHeader;
use App\Models\TDBProduct;
use App\Models\TDBProductCodeRejected;
use App\Models\TDBTrxHeader;
use App\Models\TDBUser;
use App\Models\TDBUserProduct;
use App\Models\TDBUserCredit;
use App\Models\UserTmi;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use DatePeriod;
use DateTime;
use http\Env\Response;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Datatables;
use Exception;
use PDF;
use App\Helper;
use App\Models\CustomerAddress;
use App\Models\MasterKodepos;
use App\Models\TDBTrxDetail;
use Illuminate\Support\Facades\Crypt;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.inputmember');
    }


    public function getViewMember()
    {

        $branchFormat = "";

        $branch = \DB::table('branches')
            ->get();

        foreach ($branch as $index => $row) {
            $branchFormat .= "<option style='font-size: 12px;' value='" . $row->kode_igr . "'>" . $row->kode_igr . " --> " . $row->name . "</option>";
        }

        return view('admin.uploadplu')->with('getmember', $this->MemberView())->with('branch', $branchFormat);
    }

    public function getViewPluRejection()
    {

        return view('admin.plutolakan');
    }

    public function submitPluTolakan(Request $request)
    {
        $path = $request->file('file');

        try {
            $plu = Excel::load($path)->calculate()->toArray();
            DB::beginTransaction();
            foreach ($plu as $p) {
                $b = TDBBranch::where('code', '=', $p['branch_code'])
                    ->first();
                $plu_rejected = TDBProductCodeRejected::withTrashed()
                    ->where(DB::raw('SUBSTR(plu, 1, 6)'), '=', substr($p['plu'], 0, 6))
                    ->where('branch_id', '=', $b->id)
                    ->first();
                if (!empty($plu_rejected)) {
                    if($plu_rejected->trashed()){
                        $plu_rejected->restore();
                    } else {
//                        $insertPluRejected = new TDBProductCodeRejected();
                        $plu_rejected->plu = $p['plu'];
                        $plu_rejected->branch_id = $b->id;
                        $plu_rejected->save();
                    }
                } else{
                    $insertPluRejected = new TDBProductCodeRejected();
                    $insertPluRejected->plu = $p['plu'];
                    $insertPluRejected->branch_id = $b->id;
                    $insertPluRejected->save();
                }
            }
            DB::commit();
        }catch (\Exception $exception){
            DB::rollback();
        }

        return response()->json(['status'=>1, 'message'=>'Berhasil menambahkan plu tolakan']);
    }

    public function getPluRejectionList()
    {
        $data = TDBProductCodeRejected::
            join('branches', 'product_code_rejecteds.branch_id', '=','branches.id')
            ->select('branches.code as branch_code', 'branches.name as branch_name', 'product_code_rejecteds.plu as plu',
                'product_code_rejecteds.id')
            ->get();
        return Datatables::of($data)
            ->addColumn('aksi', function($row){
                $btn = '<a 
                        href="javascript:void(0)" 
                        style="text-align: center; " 
                        class="edit btn btn-primary btn-sm"
                        onclick="optionModal('.$row->id.')"
                        >
                        AKSI</a>'; //todo nanti ganti tulisan nya jadi aktifkan
                return $btn;
            })
            ->make(true);
    }

    public function getDetailPluRejection(Request $request)
    {
        $id = $request->id;
        if(empty($id)){
            return response()->json(['status'=>0, 'message'=>'id tidak di temukan!']);
        }
        $pluRejected = TDBProductCodeRejected::withTrashed()->find($id);
        return response()->json(['status'=>1, 'message'=>$pluRejected]);
    }

    public function restoreRejectedPlu(Request $request)
    {
        $id = $request->id;
        if(empty($id)){
            return response()->json(['status'=>0, 'message'=>'id tidak di temukan!']);
        }
        $pluRejected = TDBProductCodeRejected::find($id);

        if(empty($pluRejected)){
            return response()->json(['status'=>0, 'message'=>'Pencarian tidak di temukan!']);
        }

        $plu = $pluRejected->plu;

        $pluRejected->delete();

        return response()->json(['status'=>1, 'message'=>'PLU '.
            $plu.' berhasil di pulihkan']);

    }

    public function getUploadMargin()
    {
        $branchFormat = "";

        $branch = \DB::table('branches')
            ->get();

        foreach ($branch as $index => $row) {
            $branchFormat .= "<option style='font-size: 12px;' value='" . $row->kode_igr . "'>" . $row->kode_igr . " --> " . $row->name . "</option>";
        }

        return view('admin.uploadmargin')->with('branch', $branchFormat);

    }

    public function getDepartemen(Request $Request)
    {
        $DIV = $Request->get('div');
        $Departemen = \DB::Table('department')->Distinct()
            ->Select('dep_kodedivisi', 'dep_namadepartement', 'dep_kodedepartement')
            ->Where('DEP_KODEDIVISI', "LIKE", "%" . $DIV . "%")
            ->OrderBy('DEP_KODEDEPARTEMENT')
            ->Get();
        $depFormat = "<option>--Pilih Departemen--</option>";

        foreach ($Departemen as $index => $row) {
            $depFormat .= "<option style='font-size: 12px;' value='" . $row->dep_kodedepartement . "'>(" . $row->dep_kodedepartement . ") " . $row->dep_namadepartement . "</option>";
        }
        return $depFormat;
    }

    public function getKategori(Request $Request)
    {
        $DEP = $Request->get('dep');
        $Kategori = \DB::Table('category')->Distinct()
            ->Select('kat_kodedepartement', 'kat_namakategori', 'kat_kodekategori')
            ->Where('kat_kodedepartement', "LIKE", "%" . $DEP . "%")
            ->OrderBy('KAT_KODEKATEGORI')
            ->Get();

        $katFormat = "<option>--Pilih Kategori--</option>";

        foreach ($Kategori as $index => $row) {
            $katFormat .= "<option style='font-size: 12px;' value='" . $row->kat_kodekategori . "'>(" . $row->kat_kodekategori . ") " . $row->kat_namakategori . "</option>";
        }
        return $katFormat;
    }

    public function getDashBoard()
    {

//        $Member = \DB::table('master_plu')
//            ->SelectRaw('nama, count(kodeplu)as sumplu, tipe_tmi.id as idtoko')
//            ->leftJoin('tipe_tmi', 'tipe_tmi.id', '=', 'master_plu.tmi_id')
//            ->GroupBy('nama')
//            ->Get();

        $countaktif = \DB::table('master_toko')
            ->wherenotnull('status')
            ->count();

        $countbelumaktif = \DB::table('master_toko')
            ->whereNull('status')
            ->count();


        return view('admin.404')->with('aktif', $countaktif)->with('belumaktif', $countbelumaktif);
    }


    public function getListMasterMargin()
    {
        $memberFormat = "";
        $pluFormat = "";
        $branchFormat = "";
        $tipeFormat = "";
        $prodmast = "";


        $divFormat = "";
        $depFormat = "";
        $katFormat = "";

        $divisiAssoc = \DB::Table('divisi')->Distinct()
            ->Select('div_namadivisi', 'div_kodedivisi')
            ->OrderBy('DIV_KODEDIVISI')
            ->Get();

        $departemenAssoc = \DB::Table('department')->Distinct()
            ->Select('dep_kodedivisi', 'dep_namadepartement', 'dep_kodedepartement')
//            ->Where('DEP_KODEDIVISI', "LIKE", "%" . $divisi . "%")
            ->OrderBy('DEP_KODEDEPARTEMENT')
            ->Get();

//        dd($departemenAssoc);

        $kategoriAssoc = \DB::Table('category')->Distinct()
            ->Select('kat_kodedepartement', 'kat_namakategori', 'kat_kodekategori')
//            ->Where('KAT_KODEDEPARTEMEN',  "LIKE", "%" . $departemen . "%")
            ->OrderBy('KAT_KODEKATEGORI')
            ->Get();


        foreach ($divisiAssoc as $index => $row) {

            $divFormat .= "<option style='font-size: 12px;' value='" . $row->div_kodedivisi . "'>(" . $row->div_kodedivisi . ") " . $row->div_namadivisi . "</option>";
        }
        foreach ($departemenAssoc as $index => $row) {
            $depFormat .= "<option style='font-size: 12px;' value='" . $row->dep_kodedepartement . "'>(" . $row->dep_kodedepartement . ") " . $row->dep_namadepartement . "</option>";
        }
        foreach ($kategoriAssoc as $index => $row) {
            $katFormat .= "<option style='font-size: 12px;' value='" . $row->kat_kodekategori . "'>(" . $row->kat_kodekategori . ") " . $row->kat_namakategori . "</option>";
        }


        $branch = \DB::table('branches')
            ->get();

        $tipeMember = \DB::table('tipe_tmi')
            ->Get();

        foreach ($tipeMember as $index => $row) {
            $tipeFormat .= "<option style='font-size: 12px;' value='" . $row->kode_tmi . "'>" . $row->nama . "</option>";
        }

        foreach ($branch as $index => $row) {
            $branchFormat .= "<option style='font-size: 12px;' value='" . $row->kode_igr . "'>" . $row->kode_igr . " --> " . $row->name . "</option>";
        }


        return view('admin.listmargin')->with('member', $memberFormat)->with('plu', $pluFormat)->with('prodmast', $prodmast)->with('branch', $branchFormat)->with('tipetmi', $tipeFormat)->with('divisiOpt', $divFormat)->with('departemenOpt', $depFormat)->with('kategoriOpt', $katFormat);
    }

    function incrementLetter($val, $increment = 2)
    {
        for ($i = 1; $i <= $increment; $i++) {
            $val++;
        }

        return $val;
    }

    public function getListMember()
    {
        $memberFormat = "";
        $pluFormat = "";
        $branchFormat = "";
        $tipeFormat = "";
        $prodmast = "";


        $MemberTMI = \DB::table('tipe_tmi')
            ->get();

        $branch = \DB::table('branches')
            ->get();

        $tipeMember = \DB::table('tipe_tmi')
            ->Get();

        foreach ($tipeMember as $index => $row) {
            $tipeFormat .= "<option style='font-size: 12px;' value='" . $row->kode_tmi . "'>" . $row->nama . "</option>";
        }

        foreach ($branch as $index => $row) {
            $branchFormat .= "<option style='font-size: 12px;' value='" . $row->kode_igr . "'>" . $row->kode_igr . " --> " . $row->name . "</option>";
        }

        foreach ($MemberTMI as $index => $row) {
            $memberFormat .= "<option style='font-size: 12px;' value='" . $row->id . "'>" . $row->id . " --> " . $row->nama . "</option>";
        }

        return view('admin.listmember')->with('member', $memberFormat)->with('plu', $pluFormat)->with('prodmast', $prodmast)->with('branch', $branchFormat)->with('tipetmi', $tipeFormat);
    }

    public function getCabangViewAjax(Request $Request)
    {

        if ($Request->ajax()) {
            $cFormatRaw = "";

            $id = $Request->id;

            $CabAssoc = Margin::SelectRaw('name, nama, kat_namakategori')
                ->leftJoin('margin_details', 'master_margin.id', '=', 'margin_details.margin_id')
                ->leftJoin('tipe_tmi', 'master_margin.kode_tmi', '=', 'tipe_tmi.kode_tmi')
                ->leftJoin('branches', 'margin_details.kode_igr', '=', 'branches.kode_igr')
                ->leftJoin('divisi', 'div_kodedivisi', '=', 'div')
//            ->Join('department', 'dep_kodedepartement', '=', 'dep')
                ->leftJoin('department', function ($join) {
                    $join->on('master_margin.dep', '=', 'department.dep_kodedepartement');
                    $join->on('divisi.div_kodedivisi', '=', 'department.dep_kodedivisi');
                })
                ->leftJoin('category', function ($join) {
                    $join->on('master_margin.kat', '=', 'category.kat_kodekategori');
                    $join->on('department.dep_kodedepartement', '=', 'category.kat_kodedepartement');
                })
                ->Where('master_margin.id', $id)
                ->Where('flag_cab', 0)
                ->WhereNull('deleted_at')
//            ->Join('category', 'kat_kodekategori', '=', 'kat')
                ->get();

//            dd($data);

            $cFormatRaw = "<div class='table-responsive'>
                            <table class='table'>
                                        <tr style='text-align: center'>
                                                <th class='font-12' style='text-align: center;'>
                                                    Nama Cabang
                                                </th>
                                                <th class='font-12' style='text-align: center;'>
                                                    Tipe TMI
                                                </th>
                                                <th class='font-12' style='text-align: center;'>
                                                    Kategori
                                                </th>
                                            </tr>";

            $sindex = 0;

            foreach ($CabAssoc as $row) {

                $cFormatRaw .= "
                                    <tr>
                                     <td class=\"font-12\"; style='vertical-align: middle; text-align: center;'>
                                             " . $row->name . "
                                        </td>
                                        <td class=\"font-12\"; style='vertical-align: middle; text-align: center;'>
                                             " . $row->nama . "
                                        </td>
                                         <td class=\"font-12\"; style='vertical-align: middle; text-align: center;'>
                                               " . $row->kat_namakategori . "
                                        </td>


                                    </tr>";
            }
            $cFormatRaw .= "</table>";
            $cFormatRaw .= "</div>";
            return $cFormatRaw;
        }
    }

    public function getMarginDatatable(Request $Request)
    {

        if ($Request->get('tipemember') == "x") {
            $tipe = "%";
        } else {
            $tipe = $Request->get('tipemember');
        }

        if ($Request->get('tipecabang') == "x") {
            $cab = "%";
        } else {
            $cab = $Request->get('tipecabang');
        }

        $MarginAssoc = Margin::SelectRaw('tipe_tmi.nama as tipetmi, margin_min, margin_max, margin_saran, kat_namakategori, master_margin.id as idmrg, kode_igr, kode_mrg')
            ->leftJoin('tipe_tmi', 'master_margin.kode_tmi', '=', 'tipe_tmi.kode_tmi')
//            ->leftJoin('branches', 'master_margin.kode_igr', '=', 'branches.kode_igr')
            ->leftJoin('divisi', 'div_kodedivisi', '=', 'div')
            ->leftJoin('department', function ($join) {
                $join->on('master_margin.dep', '=', 'department.dep_kodedepartement');
                $join->on('divisi.div_kodedivisi', '=', 'department.dep_kodedivisi');
            })
            ->leftJoin('category', function ($join) {
                $join->on('master_margin.kat', '=', 'category.kat_kodekategori');
                $join->on('department.dep_kodedepartement', '=', 'category.kat_kodedepartement');
            })
            ->Where('tipe_tmi.kode_tmi', $tipe)
            ->Where('master_margin.kode_igr', $cab)
            ->OrderBy('master_margin.id', 'DESC')
            ->get();

        return \Datatables::of($MarginAssoc)->make(true);

    }

    public function getStoreOfBranch(Request $request)
    {
        $id = $request->get('branch');

        $stores = TDBUser::where('branch_id', '=', $id)->get();
//        $stores = TDBUser::where(function($query) use($id){
//                                foreach($id as $item){
//                                    $query->orWhere('branch_id','LIKE',$item);
//                                }
//                            })->get();

        $optionstore = "<option style='font-size: 12px;' value='%'>PILIH TOKO</option>";
        foreach ($stores as $store) {
            $optionstore .= "<option style='font-size: 12px;' value='" . $store->id . "'>(" . $store->member_code . ") " . $store->store_name . "</option>";
        }

        return $optionstore;
    }

    public function getCashierOfStore(Request $request)
    {
        $id = $request->get('store');

        $operators = TDBOperator::where('user_id', '=', $id)->get();

        $optionoperator = "<option style='font-size: 12px;' value='%'>SEMUA KASIR</option>";
        foreach ($operators as $operator) {
            $optionoperator .= "<option style='font-size: 12px;' value='" . $operator->id . "'>(" . $operator->code . ") " . $operator->name . "</option>";
        }

        return $optionoperator;
    }

    public function getPluGroup(Request $request)
    {
        $id = $request->get('store');

        $plus = TDBProduct::select('id', 'plu', 'description')->groupBy(DB::raw('substring(plu,1,6)'))->get();

        $optionoperator = "<option style='font-size: 12px;' value='%'>PILIH PLU</option>";
        foreach ($plus as $p) {
            $optionoperator .= "<option style='font-size: 12px;' value='" . $p->plu . "'>" . $p->plu . " (" . $p->description . ")</option>";
        }

        return $optionoperator;
    }


    public function getSalesChart()
    {
        $data = TDBBranch::with('users', 'users.trxHeaders')
            ->get();

        $datasets = array();
        foreach ($data as $branch) {
            $branchname = $branch->name;
            $branchid = $branch->id;
            $total = 0;
            foreach ($branch->users as $user) {
                foreach ($user->trxHeaders as $header) {
                    $total += $header->grand_total;
                }
            }
            $datasets[] = ['branchname' => $branchname, 'total' => $total, 'id' => $branchid];
        }

        return json_encode($datasets);
    }

    public function returnSales(Request $request)
    {
        $id = $request->get('branchid');
//        dd($this->getSelectedBranches($id));
        return view('admin.laporansales')->with('optionbranch', $this->getSelectedBranches($id))->with('tahun', $this->getYearOfTrxHeader());
    }

    public function returnSpdStdApc(Request $request)
    {
        $id = $request->get('branchid');
        return view('admin.laporan_spd_std_apc')->with('optionbranch', $this->getSelectedBranches($id))/*->with('tahun', $this->getYearOfTrxHeader())*/;
    }

    public function returnRekapSinkronisasi(Request $request)
    {
        $id = $request->get('branchid');
        return view('admin.laporan_rekap_sinkronisasi')->with('optionbranch', $this->getSelectedBranches($id))->with('tahun', $this->getYearOfTrxHeader());
    }

    public function returnLaporanPembayaranIsaku(Request $request)
    {
        $id = $request->get('branchid');
        return view('admin.laporan_pembayaran_isaku')->with('optionbranch', $this->getSelectedBranches($id))->with('tahun', $this->getYearOfTrxHeader());
    }

    public function returnLaporanTransaksiPerProduk(Request $request)
    {
        $id = $request->get('branchid');
        return view('admin.laporan_transaksi_per_produk')->with('optionbranch', $this->getSelectedBranches($id))->with('tahun', $this->getYearOfTrxHeader());
    }

    public function returnLaporanLaporanPNL(Request $request)
    {
        $id = $request->get('branchid');
        return view('admin.laporanpnl')->with('optionbranch', $this->getSelectedBranches($id))->with('tahun', $this->getYearOfTrxHeader());
    }

    public function returnLaporanLaporanEPP(Request $request)
    {
        $id = $request->get('branchid');
        return view('admin.laporanepp')->with('optionbranch', $this->getSelectedBranches($id))->with('tahun', $this->getYearOfTrxHeader());
    }

    public function returnLaporanSalesPakAnjay(Request $request)
    {
        return view('admin.laporan_sales_pak_anjay');
    }

    public function returnPareto()
    {
        return view('admin.laporanpareto')->with('optionbranch', $this->getBranches())->with('tahun', $this->getYearOfTrxHeader());
    }

    public function returnPb()
    {
        return view('admin.laporanpb')->with('optionbranch', $this->getBranches())->with('tahun', $this->getYearOfPbHeader());
    }

    public function returnPromo()
    {
        return view('admin.laporanpromosi')->with('optionbranch', $this->getBranches());
    }

    public function returnArchive()
    {
        return view('admin.arsipproduk')->with('optionbranch', $this->getBranches());
    }

    public function returnStock(Request $request)
    {
        $id = $request->get('branchid');
        return view('admin.stokproduk')->with('optionbranch', $this->getSelectedBranches($id));
//        return view('admin.stokproduk')->with('optionbranch',$this->getBranches());
    }

    public function getYearOfTrxHeader()
    {
        //$result = TDBTrxHeader::select(\DB::raw('YEAR(trx_date) as year'))->distinct()->get();
        //$years = $result->pluck('year');
$minId = TDBTrxHeader::select(\DB::raw('MIN(id) as id'))
                    ->first()->id;
        $firstYear = TDBTrxHeader::select(\DB::raw('YEAR(trx_date) as year'))
                ->where('id', '=', $minId)
                ->first()->year;
        $currentYear = Carbon::now()->year;

        $years = array();
        for($i = $firstYear; $i <= $currentYear; $i++) {
            array_push($years, $i);
        }
        $optionyear = "";

        foreach ($years as $year) {
            $optionyear .= "<option style='font-size: 12px;' value='" . $year . "-'>" . $year . "</option>";
        }

        return $optionyear;
    }

    public function getYearOfPbHeader()
    {
        $result = TDBPbHeader::select(\DB::raw('YEAR(po_date) as year'))->distinct()->get();
        $years = $result->pluck('year');
        $optionyear = "";

        foreach ($years as $year) {
            $optionyear .= "<option style='font-size: 12px;' value='" . $year . "-'>" . $year . "</option>";
        }

        return $optionyear;
    }

    public function getSelectedBranches($id)
    {

        if (\Auth::user()->role == 7) {
            $branches = TDBBranch::all();
        } else {
            $branches = TDBBranch::where('code', \Auth::user()->kode_igr)->Where('code', '<>', '00')->get();
        }

        $optionbranch = "<option style='font-size: 12px;' value='%'>SEMUA CABANG</option>";

        foreach ($branches as $branch) {
            if ($branch->id == $id) {
                $optionbranch .= "<option style='font-size: 12px;' value='" . $branch->id . "' selected>(" . $branch->code . ") " . $branch->name . "</option>";
            } else {
                $optionbranch .= "<option style='font-size: 12px;' value='" . $branch->id . "'>(" . $branch->code . ") " . $branch->name . "</option>";
            }
        }

        return $optionbranch;
    }

    public function getBranches()
    {
        if (\Auth::user()->role == 7) {
            $branches = TDBBranch::all();
        } else {
            $branches = TDBBranch::where('code', \Auth::user()->kode_igr)->Where('code', '<>', '00')->get();
        }

        if (\Auth::user()->role == 7) {
            $optionbranch = "<option style='font-size: 12px;' value='%' selected>SEMUA CABANG</option>";
        }
        foreach ($branches as $branch) {
            $optionbranch .= "<option style='font-size: 12px;' value='" . $branch->id . "'>(" . $branch->code . ") " . $branch->name . "</option>";
        }

        return $optionbranch;
    }

    public function getSalesDatatable(Request $request)
    {
        $branch = $request->get('branch');
        $store = $request->get('store');
        $cashier = $request->get('cashier');
        $date = $request->get('date');

        if ($date === "daterange") {
            $startdate = $request->get('startdate') . " 00:00:00";
            $enddate = $request->get('enddate') . " 23:59:59";

            $data = TDBBranch::ConnectToHeader($branch, $store, $cashier)
                ->whereBetween('trx_headers.trx_date', [$startdate, $enddate])
                ->get(['branches.name as branch', 'users.store_name as store', 'operators.name as cashier', 'trx_headers.trx_no as invoice', 'trx_headers.trx_date as date', 'trx_headers.grand_total as total', 'trx_headers.margin as margin']);
        } else {
            $data = TDBBranch::ConnectToHeader($branch, $store, $cashier)
                ->where('trx_headers.trx_date', 'LIKE', $date)
                ->get(['branches.name as branch', 'users.store_name as store', 'operators.name as cashier', 'trx_headers.trx_no as invoice', 'trx_headers.trx_date as date', 'trx_headers.grand_total as total', 'trx_headers.margin as margin']);
        }

        $totalt = $data->sum('total');
        $totalm = $data->sum('margin');

        return \Datatables::of($data)->with('totalt', $totalt)->with('totalm', $totalm)->make(true);
    }

    public function getPNLList(Request $request)
    {
        $month = $request->month;
        $year = $request->year;
        $branch_id = $request->branch_id;

        $data = TDBUser::Join('branches', 'users.branch_id', '=', 'branches.id')
            /*->leftjoin('trx_headers', function ($join) use ($month, $year){
                $join->on('users.id', '=', 'trx_headers.user_id');
                $join->on(DB::raw('MONTH(trx_headers.trx_date)'), '=', DB::Raw('\''.$month.'\''));
                $join->on(DB::raw('YEAR(trx_headers.trx_date)'), '=', DB::Raw('\''.$year.'\''));
            })*/->leftJoin(DB::raw(
                '(
                    SELECT MONTH(h.trx_date) as tmonth, YEAR(h.trx_date) as tyear, DATE(h.trx_date) as ttrx_date, count(*) as tcount_invoice, 
                        h.user_id as tuser_id, SUM(h.grand_total) as tsales, SUM(h.margin) as tmargin
                    FROM users u 
                    LEFT JOIN trx_headers h on u.id = h.user_id 
                        AND MONTH(h.trx_date) = \'' . $month . '\'
                        AND YEAR(h.trx_date) = \'' . $year . '\'
                    GROUP BY u.id, DATE(h.trx_date)
                ) as thelp'),  function ($join) use ($month, $year){
                    $join->on('users.id', '=', 'thelp.tuser_id');
                    $join->on('thelp.tmonth', '=', DB::raw('\''.$month.'\''));
                    $join->on('thelp.tyear', '=', DB::raw('\''.$year.'\''));
                });
        if($branch_id !== '%'){
            $data = $data->where(['users.branch_id'=>$branch_id]);
        }
        $data = $data->select('branches.name as branch_name', 'users.store_name', 'users.member_code',
            DB::raw('COUNT(thelp.tcount_invoice) as total_sign'), DB::raw('SUM(thelp.tsales) as grand_total'),
            DB::raw('IFNULL(SUM(thelp.tsales)/COUNT(thelp.tcount_invoice), 0) as spd'),
            DB::raw('IFNULL(SUM(thelp.tmargin), 0) as total_margin'),
            DB::raw('IFNULL(ROUND((SUM(thelp.tmargin)/SUM(thelp.tsales))*100, 0), 0) as pmargin'),
            DB::raw('IFNULL(users.id, 0) as user_id')
            )
                ->groupBy('users.id', DB::raw('YEAR(thelp.ttrx_date)'), DB::raw('MONTH(thelp.ttrx_date)') /*'year', 'month'*/ /*,  DB::raw('DATE(trx_headers.trx_date)')*/)
                ->orderBy(DB::raw('UPPER(users.name)'), 'ASC')
                ->get();
        
        $year = str_replace("-","",$year);
        $trx_code = $month.$year;
//        dd($trx_code, $month, $year);
        //PENDAPATAN SEWA	PENDAPATAN LAIN-LAIN	BIAYA DF	BIAYA LISTRIK	BIAYA LAIN-LAIN
        $pnl_data = TDBUser::select(DB::raw('master_pnls.id as pnl_type_id, users.member_code, master_pnls.type, 
            pnl_trx_headers.user_id, pnl_trx_details.currency, pnl_trx_headers.created_by'))
            ->leftJoin('pnl_trx_headers', 'users.id', '=', 'pnl_trx_headers.user_id')
            ->join('pnl_trx_details', 'pnl_trx_headers.id', '=', 'pnl_trx_details.pnl_trx_header_id')
            ->join('master_pnls', 'pnl_trx_details.master_pnl_id', '=', 'master_pnls.id')
            ->where(['pnl_trx_headers.trx_code'=>$trx_code, 'branch_id'=>$branch_id])
            ->groupBy('users.id', 'master_pnls.id')
            ->get();
        $pnl_data = collect($pnl_data);

//        return response()->json($pnl_data);
        for($i=0; $i<$data->count(); $i++){

            $data_user = $data[$i];
            
            $temp_data = $pnl_data->where('member_code', $data_user->member_code);
            if($temp_data != null){
                $income_rent = $temp_data->where('pnl_type_id', 1)->first()['currency'];
                if($income_rent == null){
                    $income_rent = 0;
                }
                $data[$i]['income_rent'] = $income_rent;

                $other_income = $temp_data->where('pnl_type_id', 2)->first()->currency;
                if($other_income == null){
                    $other_income = 0;
                }
                $data[$i]['other_income'] = $other_income;

                $electricity_spending = $temp_data->where('pnl_type_id', 3)->first()->currency;
                if($electricity_spending == null){
                    $electricity_spending = 0;
                }
                $data[$i]['electricity_spending'] = $electricity_spending;

                $other_spending = $temp_data->where('pnl_type_id', 4)->first()->currency;
                if($other_spending == null){
                    $other_spending = 0;
                }

                $df_fee = $temp_data->where('pnl_type_id', 5)->first()->currency;
                if($df_fee == null){
                    $df_fee = 0;
                }
                $data[$i]['distribution_fee'] = $df_fee;
                $data[$i]['other_spending'] = $other_spending;
                $data[$i]['created_by'] = $temp_data->first()->created_by;
            }
        }
        // 1 = pendapatan sewa
        // 2 = pendapatan lain-lain
        // 3 = biaya listrik
        // 4 = biaya lain-lain
//        return response()->json(['count'=>count($data)]);
//        return response()->json($data);

//        $output = ;
//        return $data;
        return \Datatables::of($data)->addColumn('action', function($row) use($month,$year){
            $uniqueCode = '\''.$row->user_id.'*'.$month.$year.'\'';
            $btn = '<a onclick="downloadPDF('.$uniqueCode.')" 
                        style="text-align: center; " 
                        class="edit btn btn-primary btn-sm"
                        >
                        Download</a>';
            return $btn;
        })
            ->make(true);

    }

    public function getPNLReportFile($unique)
    {
        //            $uniqueCode = $row->user_id.'*'.$month.$year;
        $data = explode('*', $unique);
        if(count($data) != 1) {
            //salah
        }
        $user_id = $data[0];
        $yearmonth = $data[1];
        $month = substr($yearmonth, 0, 2);
        $year = substr($yearmonth, 2, 6);

        $user = TDBUser::find($user_id);
        if(empty($user)){
            //return user tidak di temukan
        }

        $dateObj   = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj->format('F');

        $pnl_detail = TDBPnlTrxHeader::join('pnl_trx_details', 'pnl_trx_headers.id', '=', 'pnl_trx_details.pnl_trx_header_id')
            ->join('master_pnls', 'pnl_trx_details.master_pnl_id', '=','master_pnls.id')
            ->join('users', 'pnl_trx_headers.user_id', '=','users.id')
            ->where(['pnl_trx_headers.trx_code'=>($month.$year), 'users.id'=>$user_id])
            ->select(DB::raw('users.store_name, master_pnls.type, master_pnls.id as master_pnl_id,
                ROUND(pnl_trx_details.currency) as currency, pnl_trx_headers.created_by as created_by
            '))
            ->get();
        $pnl_detail = collect($pnl_detail);

        if($pnl_detail->count() <= 0){
            //return detail trx tidak di temukan
        }
        $pnl_header = $pnl_detail[0];

        $trx_header = TDBTrxHeader::join('trx_details', 'trx_headers.id','=','trx_details.trx_header_id')
            ->where('trx_headers.trx_date','like',($year.'-'.$month.'%'))
            ->where('trx_headers.user_id', '=', $user_id)
            ->select(DB::raw('SUM(trx_details.sub_total) as total_sales, 
                    SUM(trx_details.cost) as total_cost'))
            ->first();

        $pb = TDBPbHeader::where(['user_id'=>$user_id])
            ->where('po_date','like',($year.'-'.$month.'%'))
            ->select(DB::raw('SUM(shipping_amt) as shipping_amt'))
            ->groupBy('user_id')
            ->first();

        if(empty($pb)){
            //return error message
        }
        $rent_income = $pnl_detail->where('master_pnl_id', 1)->first()->currency;
        $rent_income = $rent_income==null ? 0 : $rent_income;

        $other_income = $pnl_detail->where('master_pnl_id', 2)->first()->currency;
        $other_income = $other_income == null ? 0 : $other_income;

        $total_income = $rent_income + $other_income + $trx_header->total_sales;

        $electric_fee = $pnl_detail->where('master_pnl_id', 3)->first()->currency;
        $electric_fee = $electric_fee == null ? 0 : $electric_fee;

        $df_fee = $pb->shipping_amt == null ? 0 : $pb->shipping_amt; // distribution_fee

        $other_fee = $pnl_detail->where('master_pnl_id', 4)->first()->currency;
        $other_fee = $other_fee == null ? 0 : $other_fee;

        $total_fee = $electric_fee + $other_fee + $df_fee + $trx_header->total_cost;

        $data = [
            'title' => 'Laporan Toko '.$pnl_header['store_name'],
            'store_name' => $pnl_header['store_name'],
            'monthyear' => $monthName.' '.$year,
            'total_sales' => $this->toRupiah($trx_header->total_sales),
            'total_cost' => $this->toRupiah($trx_header->total_cost),
            'rent_income' => $this->toRupiah($rent_income),
            'other_income' => $this->toRupiah($other_income),
            'total_income' => $this->toRupiah($total_income),
            'electric_fee' => $this->toRupiah($electric_fee),
            'df_fee' => $this->toRupiah($df_fee),
            'other_fee' => $this->toRupiah($other_fee),
            'total_fee' => $this->toRupiah($total_fee),
            'total_pnl' => $this->toRupiah($total_income - $total_fee),
            'created_by' => $pnl_detail[0]->created_by
            ];
        $customPaper = array(0,0,567.00,283.80);
        $customPaper = array(0,0,360,360);
        // $dompdf = new \Dompdf\Dompdf();
		// $dompdf->setPaper('A6', 'portrait');
        // $dompdf->set_option('isHtml5ParserEnabled', TRUE);
		// $dompdf->loadHtml('pnlviewpdf');
        //         // Page stops loading here
		// $dompdf->render();
		// $dompdf->stream('test.pdf', $data);
        // return $dompdf;
        $myPdf = PDF::loadview('pnlviewpdf', $data)->setPaper('A6', 'portrait');
//        return $myPdf->stream('data.pdf');

//        $pdf = PDF::loadview('BACKOFFICE/TRANSAKSI/PEMUSNAHAN.barangRusak-laporan', ['datas' => $datas]);
        // $myPdf->output();
        // $dompdf = $myPdf->getDomPDF()->set_option("enable_php", true);

        // $canvas = $dompdf ->get_canvas();
//        $canvas->page_text(514, 10, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));

        return $myPdf->stream();
//        return $pdf->stream('BACKOFFICE/TRANSAKSI/PEMUSNAHAN.BarangRusak-laporan');

    }

    private function toRupiah($money){
        $output = "Rp " . number_format($money, 0, ',','.');
        return $output;
    }

    public function getSalesDayDatatable(Request $request)
    {
        ini_set('max_execution_time', '300');

        $branch = $request->get('branch');
        $store = $request->get('store');
        $cashier = $request->get('cashier');
        $date = $request->get('date');

//        dd(env('app_env'));
//        dd(TDBBranch::ConnectToHeader($branch,$store,$cashier));
        $data = TDBBranch::ConnectToHeader($branch, $store, $cashier)
            ->where('trx_headers.trx_date', 'LIKE', $date)
//            ->toSql();
            ->get(['branches.name as branch', 'users.store_name as store', 'trx_headers.trx_no as invoice', 'trx_headers.trx_date as date', 'trx_headers.grand_total as total', 'trx_headers.margin as margin']);
//        printf($data);
//        dd($data);
//        return $data;
//        dd($data);
        //batas dev
//        printf($date);
//        printf($data);
//        dd($data);
        //batas dev
        $tabledata = [];
        $sales = [];
        $temp = 0;
        $temptotal = 0;
        $countActiveDay = 0;//[08-06-2020 #VANVAN]tambahan variable ini
        $countInvoice = 0;//[08-06-2020 #VANVAN]tambahan variable ini

        $sales["header"] = "sales";
        for ($i = 1; $i < 32; $i++) {
            $colname = "d" . $i;
            foreach ($data as $column) {
                if (date('d', strtotime($column->date)) == $i) {
                    $temp = $temp + $column->total;
                    if (!empty($column->invoice)) {//[08-06-2020 #VANVAN]tambahan variable ini
                        $countInvoice++;
                    }
                }
            }
            if ($countInvoice != 0) {
                $countActiveDay++;
            }
            $sales[$colname] = $temp;
            $temptotal = $temptotal + $temp;
            $temp = 0;
            $countInvoice = 0;
        }
        $sales["total"] = $temptotal;
        $temptotal = 0;
        $numdays = cal_days_in_month(CAL_GREGORIAN, substr($date, 5, 2), substr($date, 0, 4));
        $sales["rata"] = round($sales["total"] / $numdays);
        $sales["rata"] = round($sales["total"] / $countActiveDay);//[08-06-2020 #VANVAN]ganti dengan kodingan ini
        array_push($tabledata, $sales);

        $sales["header"] = "margin (rupiah)";
        for ($i = 1; $i < 32; $i++) {
            $colname = "d" . $i;
            foreach ($data as $column) {
                if (date('d', strtotime($column->date)) == $i) {
                    $temp = $temp + $column->margin;
                }
            }
            $sales[$colname] = $temp;
            $temptotal = $temptotal + $temp;
            $temp = 0;
        }
        $sales["total"] = $temptotal;
        $temptotal = 0;
        $numdays = cal_days_in_month(CAL_GREGORIAN, substr($date, 5, 2), substr($date, 0, 4));
        $sales["rata"] = round($sales["total"] / $numdays);
        $sales["rata"] = round($sales["total"] / $countActiveDay);//[08-06-2020 #VANVAN]ganti dengan kodingan ini
        array_push($tabledata, $sales);

        $sales["header"] = "presented";
        for ($i = 1; $i < 32; $i++) {
            $colname = "d" . $i;
            if ($tabledata[0][$colname] != 0) {
                $temp = ($tabledata[1][$colname] / $tabledata[0][$colname]) * 100;
                $sales[$colname] = $temp;
            }
            $temp = 0;
        }
        $sales["total"] = ($tabledata[1]["total"] / $tabledata[0]["total"]) * 100;
        $sales["rata"] = ($tabledata[1]["rata"] / $tabledata[0]["rata"]) * 100;
        array_push($tabledata, $sales);

        $sales["header"] = "transaksi";
        for ($i = 1; $i < 32; $i++) {
            $colname = "d" . $i;
            foreach ($data as $column) {
                if (date('d', strtotime($column->date)) == $i) {
                    $temp = $temp + 1;
                }
            }
            $sales[$colname] = $temp;
            $temptotal = $temptotal + $temp;
            $temp = 0;
        }
        $sales["total"] = $temptotal;
        $numdays = cal_days_in_month(CAL_GREGORIAN, substr($date, 5, 2), substr($date, 0, 4));
        $sales["rata"] = round($sales["total"] / $numdays);
        $sales["rata"] = round($sales["total"] / $countActiveDay);//[08-06-2020 #VANVAN]ganti dengan kodingan ini
        array_push($tabledata, $sales);
        $therows = "";

        foreach ($tabledata as $row => $innerArray) {
            $therows .= "<tr>";
            foreach ($innerArray as $innerRow => $value) {
                if (is_numeric($value)) {
                    if (is_float($value)) {
                        $therows .= "<td style='text-align: right'>" . number_format($value, 2, ".", ",") . "</td>";
                    } else {
                        $therows .= "<td style='text-align: right'>" . number_format($value, 0, ".", ",") . "</td>";
                    }
                } else {
                    $therows .= "<td>" . $value . "</td>";
                }
            }
            $therows .= "</tr>";
        }

        return $therows;
    }

    public function getSalesMonthDatatable(Request $request)
    {
        $branch = $request->get('branch');
        $store = $request->get('store');
        $date = $request->get('date');

        $data = TDBBranch::ConnectToHeaderWOCashier($branch, $store)
            ->where('trx_headers.trx_date', 'LIKE', $date)
            ->groupby(\DB::raw('MONTH(trx_headers.trx_date)'))
            ->get(['trx_headers.trx_date as date', \DB::raw('sum(trx_headers.grand_total) as sales'), \DB::raw('sum(trx_headers.margin) as margin'), \DB::raw('count(trx_headers.trx_no) as tcount')]);

        $tabledata = [];
        $sales = [];
        $temp = 0;
        $temptotal = 0;
        $totalActiveMonth = 0;//[08-06-2020 #VANVAN]tambahan variable ini

        $checktCount = 0;//[08-06-2020 #VANVAN]tambahan variable ini

        $sales["header"] = "TOTAL SALES";
        for ($i = 1; $i < 13; $i++) {
            $colname = "m" . $i;
            foreach ($data as $column) {
                if (date('m', strtotime($column->date)) == $i) {
                    $temp = $temp + $column->sales;
                    $checktCount = $column->tcount;
                }
            }
            if ($checktCount != 0) {
                $totalActiveMonth++;
            }
            $sales[$colname] = $temp;
            $temptotal = $temptotal + $temp;
            $temp = 0;
            $checktCount = 0;
        }
        $sales["total"] = $temptotal;
        $temptotal = 0;
        $sales["rata"] = round($sales["total"] / $totalActiveMonth);//[08-06-2020 #VANVAN]ubah angka 12 nya jadi $totalActiveMonth
        array_push($tabledata, $sales);

        $sales["header"] = "TOTAL MARGIN";
        for ($i = 1; $i < 13; $i++) {
            $colname = "m" . $i;
            foreach ($data as $column) {
                if (date('m', strtotime($column->date)) == $i) {
                    $temp = $temp + $column->margin;
                }
            }
            $sales[$colname] = $temp;
            $temptotal = $temptotal + $temp;
            $temp = 0;
        }
        $sales["total"] = $temptotal;
        $temptotal = 0;
        $sales["rata"] = round($sales["total"] / $totalActiveMonth);//[08-06-2020 #VANVAN]ubah angka 12 nya jadi $totalActiveMonth
        array_push($tabledata, $sales);

        $sales["header"] = "% MARGIN";
        for ($i = 1; $i < 13; $i++) {
            $colname = "m" . $i;
            if ($tabledata[0][$colname] != 0) {
                $temp = ($tabledata[1][$colname] / $tabledata[0][$colname]) * 100;
                $sales[$colname] = $temp;
            }
            $temp = 0;
        }
        $sales["total"] = ($tabledata[1]["total"] / $tabledata[0]["total"]) * 100;
        $sales["rata"] = ($tabledata[1]["rata"] / $tabledata[0]["rata"]) * 100;
        array_push($tabledata, $sales);

        $sales["header"] = "JUMLAH TRANSAKSI";
        for ($i = 1; $i < 13; $i++) {
            $colname = "m" . $i;
            foreach ($data as $column) {
                if (date('m', strtotime($column->date)) == $i) {
                    $temp = $temp + $column->tcount;
                }
            }
            $sales[$colname] = $temp;
            $temptotal = $temptotal + $temp;
            $temp = 0;
        }
        $sales["total"] = $temptotal;
        $sales["rata"] = round($sales["total"] / $totalActiveMonth);//[08-06-2020 #VANVAN]ubah angka 12 nya jadi $totalActiveMonth
        array_push($tabledata, $sales);
        $therows = "";

        foreach ($tabledata as $row => $innerArray) {
            $therows .= "<tr>";
            foreach ($innerArray as $innerRow => $value) {
                if (is_numeric($value)) {
                    if (is_float($value)) {
                        $therows .= "<td style='text-align: right'>" . number_format($value, 2, ".", ",") . "</td>";
                    } else {
                        $therows .= "<td style='text-align: right'>" . number_format($value, 0, ".", ",") . "</td>";
                    }
                } else {
                    $therows .= "<td>" . $value . "</td>";
                }
            }
            $therows .= "</tr>";
        }

        return $therows;
    }

    public function getSalesProductDatatable(Request $request)
    {
        $branch = $request->get('branch');
        $store = $request->get('store');
        $date = $request->get('date');

        if ($date === "daterange") {

            $later = new DateTime($request->get('enddate'));
            $numdays = ($later->diff(new DateTime($request->get('startdate')))->format("%a")) + 1;

            $startdate = $request->get('startdate') . " 00:00:00";
            $enddate = $request->get('enddate') . " 23:59:59";

            $data = TDBUserProduct::ConnectForFreeplu($store, $branch)
                ->whereBetween('trx_headers.trx_date', [$startdate, $enddate])
                ->groupBy('user_products.plu')
                ->get(['user_products.id as pid', 'user_products.plu as plu', 'categories.category as category', 'user_products.description as description', \DB::raw('sum(trx_details.qty) as qty'), \DB::raw('sum(trx_details.sub_total)-sum(trx_details.discount) as subtotal'), \DB::raw('sum(trx_details.margin) as margin'), \DB::raw('count(distinct trx_details.trx_header_id) as invcount')]);
        } else {
            if ($date === "%") {
                $data = TDBUserProduct::ConnectForFreeplu($store, $branch)
                    ->where('trx_headers.trx_date', 'LIKE', $date)
                    ->groupBy('user_products.plu')
                    ->get(['user_products.id as pid', 'user_products.plu as plu', 'categories.category as category', 'user_products.description as description', \DB::raw('min(trx_headers.trx_date) as mindate'), \DB::raw('max(trx_headers.trx_date) as maxdate'), \DB::raw('sum(trx_details.qty) as qty'), 
                    \DB::raw('sum(trx_details.sub_total)-sum(trx_details.discount) as subtotal'), \DB::raw('sum(trx_details.margin) as margin'), \DB::raw('count(distinct trx_details.trx_header_id) as invcount')]);

                $later = new DateTime(substr($data[0]->maxdate, 0, 10));
                $numdays = ($later->diff(new DateTime(substr($data[0]->mindate, 0, 10)))->format("%a")) + 1;
            } else {
                if (substr($date, 5, 1) === "%") {
                    $data = TDBUserProduct::ConnectForFreeplu($store, $branch)
                        ->where('trx_headers.trx_date', 'LIKE', $date)
                        ->groupBy('user_products.plu')
                        ->get(['user_products.id as pid', 'user_products.plu as plu', 'categories.category as category', 'user_products.description as description', \DB::raw('min(trx_headers.trx_date) as mindate'), \DB::raw('max(trx_headers.trx_date) as maxdate'), \DB::raw('sum(trx_details.qty) as qty'), 
                        \DB::raw('sum(trx_details.sub_total)-sum(trx_details.discount) as subtotal'), \DB::raw('sum(trx_details.margin) as margin'), \DB::raw('count(distinct trx_details.trx_header_id) as invcount')]);

                    $later = new DateTime(substr($data[0]->maxdate, 0, 10));
                    $numdays = ($later->diff(new DateTime(substr($data[0]->mindate, 0, 10)))->format("%a")) + 1;
                } else {
                    $numdays = cal_days_in_month(CAL_GREGORIAN, substr($date, 5, 2), substr($date, 0, 4));

                    $data = TDBUserProduct::ConnectForFreeplu($store, $branch)
                        ->where('trx_headers.trx_date', 'LIKE', $date)
                        ->groupBy('user_products.plu')
                        ->get(['user_products.id as pid', 'user_products.plu as plu', 'categories.category as category', 'user_products.description as description', \DB::raw('sum(trx_details.qty) as qty'), \DB::raw('sum(trx_details.sub_total) as subtotal'), \DB::raw('sum(trx_details.margin) as margin'), \DB::raw('count(distinct trx_details.trx_header_id) as invcount')]);
                }
            }
        }
        $div = [];
        $dep = [];
        $kat = [];
        foreach ($data as $row => $item) {
            if ($item->category != null) {
                $temp = Margin::join('divisi', 'master_margin.div', '=', 'divisi.DIV_KODEDIVISI')
                    ->join('department', function ($join) {
                        $join->on('master_margin.dep', '=', 'department.DEP_KODEDEPARTEMENT');
                        $join->on('divisi.DIV_KODEDIVISI', '=', 'department.DEP_KODEDIVISI');
                    })
                    ->join('category', function ($join) {
                        $join->on('master_margin.kat', '=', 'category.KAT_KODEKATEGORI');
                        $join->on('department.DEP_KODEDEPARTEMENT', '=', 'category.KAT_KODEDEPARTEMENT');
                    })
                    ->where('master_margin.id', '=', $item->category)
                    ->get(['divisi.DIV_KODEDIVISI as div', 'department.DEP_KODEDEPARTEMENT as dep', 'category.KAT_KODEKATEGORI as kat']);
                $div[] = $temp[0]->div;
                $dep[] = $temp[0]->dep;
                $kat[] = $temp[0]->kat;
            } else {
                $div[] = '-';
                $dep[] = '-';
                $kat[] = '-';
            }
        }
        $therows = "";
        $i = 0;
        foreach ($data as $row => $value) {
            $therows .= "<tr>";
            $therows .= "<td>" . $div[$i] . "</td>";
            $therows .= "<td>" . $dep[$i] . "</td>";
            $therows .= "<td>" . $kat[$i] . "</td>";
            $therows .= "<td>" . $value->plu . "</td>";
            $therows .= "<td>" . $value->description . "</td>";
            $therows .= "<td style='text-align: right'>" . number_format($value->qty, 0, ",", ",") . "</td>";
            $therows .= "<td style='text-align: right'>" . number_format($value->subtotal, 0, ",", ",") . "</td>";
            $therows .= "<td style='text-align: right'>" . number_format($value->margin, 0, ",", ",") . "</td>";
            $therows .= "<td style='text-align: right'>" . number_format(($value->margin / $value->subtotal) * 100, 2, ",", ",") . "</td>";
            if ($value->qty != null) {
                $therows .= "<td>" . $value->qty / $numdays . "</td>";
            } else {
                $therows .= "<td>0</td>";
            }
            $therows .= "<td>" . $value->invcount . "</td>";
            $therows .= "</tr>";
            $i++;
        }

        return $therows;
    }

    public function getSalesBranchDatatable(Request $request)
    {
        $branch = $request->get('branch');
        $store = $request->get('store');
        $date = $request->get('date');

//        dd(TDBBranch::join('users', 'users.id', '=','branches.id')->get());
        $footer_data = TDBBranch::join('users', 'users.branch_id', '=', 'branches.id')
            ->join('trx_headers', 'trx_headers.user_id', '=', 'users.id')
            ->where('trx_headers.trx_date', 'LIKE', $date)
            ->select(DB::raw('\'TOTAL\' as branch_name'), DB::raw('\'\' as member_code'),
                DB::raw('\'\' as store_name'),
                'trx_headers.trx_date', DB::raw('sum(trx_headers.grand_total) as sales'),
                DB::raw('sum(trx_headers.margin) as margin')
            )->groupBy(DB::raw('MONTH(trx_headers.trx_date)'));
//                        ->get();
//        dd($footer_data);
        $data = TDBBranch::ConnectToHeaderWOCashier($branch, $store)
            ->where('trx_headers.trx_date', 'LIKE', $date)
            ->groupBy('users.store_name', \DB::raw('MONTH(trx_headers.trx_date)'))
            ->union($footer_data)
            ->get(['branches.name as branch_name', 'users.member_code', 'users.store_name', 'trx_headers.trx_date',
                \DB::raw('sum(trx_headers.grand_total) as sales'), \DB::raw('sum(trx_headers.margin) as margin')]);
        $stores = $data->unique('store_name');

        $therows = "";
        $ts = 0;
        $tm = 0;
        foreach ($stores as $row) {
            $values = [];
            $therows .= "<tr>";
            $therows .= "<td>" . $row->branch_name . "</td>";
            $therows .= "<td>" . $row->member_code . "</td>";
            $therows .= "<td>" . $row->store_name . "</td>";
            foreach ($data as $cell) {
                if ($row->store_name == $cell->store_name) {
                    $month = (int)substr($cell->trx_date, 5, 2);
                    $values[$month - 1] = array($cell->sales, $cell->margin);
                }
            }
            for ($i = 0; $i < 12; $i++) {
                if ($values[$i] != null) {
                    $therows .= "<td>" . number_format($values[$i][0], 2, ",", ",") . "</td>";
                    $therows .= "<td>" . number_format($values[$i][1], 2, ",", ",") . "</td>";
                    $ts = $ts + $values[$i][0];
                    $tm = $tm + $values[$i][1];
                } else {
                    $therows .= "<td>0</td>";
                    $therows .= "<td>0</td>";
                }
            }
            $therows .= "<td>" . number_format($ts, 2, ",", ",") . "</td>";
            $therows .= "<td>" . number_format($tm, 2, ",", ",") . "</td>";
            $therows .= "</tr>";
            $ts = 0;
            $tm = 0;
            $values = null;
        }

        return $therows;
    }

    public function getSalesRecapDatatable(Request $request)
    {
        $branch = $request->get('branch');
        $store = $request->get('store');
        $date = $request->get('date');

        if ($date === "daterange") {
            $startdate = $request->get('startdate') . " 00:00:00";
            $enddate = $request->get('enddate') . " 23:59:59";

            $data = TDBUserProduct::ConnectForFreeplu($store, $branch)
                ->whereBetween('trx_headers.trx_date', [$startdate, $enddate])
                ->groupBy('users.id', 'products.id')
                ->get(['users.store_name as storename', 'products.id as pid', \DB::raw('sum(trx_details.sub_total) as sales'), \DB::raw('sum(trx_details.margin) as margin')]);

            $days = TDBUserProduct::ConnectForFreeplu($store, $branch)
                ->whereBetween('trx_headers.trx_date', [$startdate, $enddate])
                ->groupBy('users.id')
                ->get(['users.store_name as storename', \DB::raw('count(distinct day(trx_headers.trx_date)) as days')]);
        } else {
            $data = TDBUserProduct::ConnectForFreeplu($store, $branch)
                ->where('trx_headers.trx_date', 'LIKE', $date)
                ->groupBy('users.id', 'products.id')
                ->get(['users.store_name as storename', 'products.id as pid', \DB::raw('sum(trx_details.sub_total) as sales'), \DB::raw('sum(trx_details.margin) as margin')]);

            $days = TDBUserProduct::ConnectForFreeplu($store, $branch)
                ->where('trx_headers.trx_date', 'LIKE', $date)
                ->groupBy('users.id')
                ->get(['users.store_name as storename', \DB::raw('count(distinct day(trx_headers.trx_date)) as days')]);
        }

        $stores = $data->unique('storename');

        $therows = "";
        $ts = 0;
        foreach ($stores as $row) {
            $values = [];
            $therows .= "<tr>";
            $therows .= "<td>" . $row->storename . "</td>";
            foreach ($days as $cell) {
                if ($row->storename === $cell->storename) {
                    $therows .= "<td>" . $cell->days . "</td>";
                }
            }

            foreach ($data as $cell) {
                if ($row->storename === $cell->storename) {
                    if ($cell->pid != null) {
                        $values[0][0] = $values[0][0] + $cell->sales;
                        $values[1][0] = $values[1][0] + $cell->margin;
                    } else {
                        $values[0][1] = $values[0][1] + $cell->sales;
                        $values[1][1] = $values[1][1] + $cell->margin;
                    }
                }
            }
            for ($i = 0; $i < 2; $i++) {
                for ($j = 0; $j < 2; $j++) {
                    if ($values[$i][$j] != null) {
                        $therows .= "<td>" . number_format($values[$i][$j], 0, ",", ",") . "</td>";
                        $ts = $ts + $values[$i][$j];
                    } else {
                        $therows .= "<td>0</td>";
                    }
                }
                $therows .= "<td>" . number_format($ts, 0, ",", ",") . "</td>";
                $ts = 0;
            }
            $therows .= "<td>" . number_format((($values[1][0] / $values[0][0]) * 100), 2, ",", ",") . "</td>";
            $therows .= "<td>" . number_format((($values[1][0] / $values[0][0]) * 100), 2, ",", ",") . "</td>";
            $therows .= "<td>" . number_format(((($values[1][0] + $values[1][1]) / ($values[0][0] + $values[0][1])) * 100), 2, ",", ",") . "</td>";
            $therows .= "</tr>";
            $values = null;
        }
        return $therows;
    }

    public function getSalesBranchDateDatatable(Request $request)
    {
        $branch = $request->get('branch');
        $store = $request->get('store');
        $date = $request->get('date');

//        dd($branch, $store);
        if ($date === "daterange") {
            $startdate = $request->get('startdate') . " 00:00:00";
            $enddate = $request->get('enddate') . " 23:59:59";

            $data = TDBBranch::ConnectToHeaderWithTmiType($branch, $store)
                ->whereBetween('trx_headers.trx_date', [$startdate, $enddate])
                ->groupBy(\DB::raw('date(trx_headers.trx_date)'), 'users.id')
                ->get(['branches.name as branch', 'users.store_name as store', 'tmi_types.description as type',
                    \DB::raw("(select date(min(trx_headers.trx_date)) from branches join users on users.branch_id = branches.id join trx_headers on trx_headers.user_id = users.id where users.store_name = store) as opendate"),
                    \DB::raw('date(trx_headers.trx_date) as tdate'), \DB::raw('sum(trx_headers.grand_total) as sales'), \DB::raw('sum(trx_headers.margin) as margin'), \DB::raw('count(distinct trx_headers.id) as invcount')]);
        } else {
            $data = TDBBranch::ConnectToHeaderWithTmiType($branch, $store)
                ->where('trx_headers.trx_date', 'LIKE', $date)
                ->groupBy(\DB::raw('date(trx_headers.trx_date)'), 'users.id')
                ->get(['branches.name as branch', 'users.store_name as store', 'tmi_types.description as type',
                    \DB::raw("(select date(min(trx_headers.trx_date)) from branches join users on users.branch_id = branches.id join trx_headers on trx_headers.user_id = users.id where users.store_name = store) as opendate"),
                    \DB::raw('date(trx_headers.trx_date) as tdate'), \DB::raw('sum(trx_headers.grand_total) as sales'), \DB::raw('sum(trx_headers.margin) as margin'), \DB::raw('count(distinct trx_headers.id) as invcount')]);
        }

        $therows = "";
        foreach ($data as $row => $value) {
            $therows .= "<tr>";
            $therows .= "<td>" . $value->branch . "</td>";
            $therows .= "<td>" . $value->store . "</td>";
            $therows .= "<td>" . $value->type . "</td>";
            $therows .= "<td>" . $value->opendate . "</td>";
            $therows .= "<td>" . $value->tdate . "</td>";
            $therows .= "<td>" . number_format($value->sales, 0, ",", ",") . "</td>";
            $therows .= "<td>" . $value->invcount . "</td>";
            $therows .= "<td style='text-align: right'>" . number_format($value->sales / $value->invcount, 0, ",", ",") . "</td>";
            $therows .= "<td style='text-align: right'>" . number_format(($value->margin / $value->sales) * 100, 2, ",", ",") . "</td>";
            $therows .= "</tr>";
        }
        return $therows;
    }

    public function getParetoDatatable(Request $request)
    {
        ini_set('max_execution_time', (60*45));

        $branch = $request->get('branch');
        $store = $request->get('store');
        $type = $request->get('type');
        $date = $request->get('date');

        if ($type === "cnt") {
            $arr = ['branches.name as branch', 'users.store_name as store', 'user_products.description as product', \DB::raw('count(distinct(trx_details.trx_header_id)) as total')];
        } else if ($type === "qty") {
            $arr = ['branches.name as branch', 'users.store_name as store', 'user_products.description as product', \DB::raw('sum(trx_details.qty) as total')];
        } else if ($type === "prc") {
            $arr = ['branches.name as branch', 'users.store_name as store', 'user_products.description as product', \DB::raw('sum(trx_details.price*trx_details.qty) as total')];
        }

        if ($date === "daterange") {
            $startdate = $request->get('startdate') . " 00:00:00";
            $enddate = $request->get('enddate') . " 23:59:59";

            $data = TDBBranch::ConnectToDetail($branch, $store)
                ->whereBetween('trx_headers.trx_date', [$startdate, $enddate])
                ->groupBy('user_products.id')
                ->get($arr);
        } else {
            $data = TDBBranch::ConnectToDetail($branch, $store)
                ->where('trx_headers.trx_date', 'LIKE', $date)
                ->groupBy('user_products.id')
                ->get($arr);
        }

        return \Datatables::of($data)->make(true);
    }

    public function getPbDatatable(Request $request)
    {
        $branch = $request->get('branch');
        $store = $request->get('store');
        $date = $request->get('date');

        if ($date === "daterange") {
            $startdate = $request->get('startdate');
            $enddate = $request->get('enddate');

            $data = TDBBranch::ConnectToPb($branch, $store)
                ->whereBetween('pb_headers.po_date', [$startdate, $enddate])
                ->get(['branches.name as branch', 'users.store_name as store', 'pb_headers.po_number as po_no',
                    'pb_headers.po_date as po_date', 'pb_headers.name as name', 'pb_headers.email as email',
                    'pb_headers.phone_number as phone', 'pb_headers.address as address', 'pb_headers.qty_order as qtyo',
                    'pb_headers.qty_fulfilled as qtyf', 'pb_headers.price_order as priceo',
                    'pb_headers.price_fulfilled as pricef', 'pb_headers.flag_free_delivery as isfree',
                    'pb_statuses.title as status', 'pb_headers.flag_sent as issent']);
        } else {
            $data = TDBBranch::ConnectToPb($branch, $store)
                ->where('pb_headers.po_date', 'LIKE', $date)
                ->get(['branches.name as branch', 'users.store_name as store', 'pb_headers.po_number as po_no',
                    'pb_headers.po_date as po_date', 'pb_headers.name as name', 'pb_headers.email as email',
                    'pb_headers.phone_number as phone', 'pb_headers.address as address',
                    'pb_headers.total_items_order as itemo', 'pb_headers.total_items_fulfilled as itemf',
                    'pb_headers.qty_order as qtyo',
                    'pb_headers.qty_fulfilled as qtyf', 'pb_headers.price_order as priceo',
                    'pb_headers.price_fulfilled as pricef', 'pb_headers.flag_free_delivery as isfree',
                    'pb_statuses.title as status', 'pb_headers.flag_sent as issent']);
        }

//        quantity, plu, rupiah
//        $footer_data =
//        foreach ($data as $d => $index){
//            $data[$d]['abc'] = 'vanvan';
//        }

        return \Datatables::of($data)->make(true);
    }

    public function getPromoDatatable(Request $request)
    {
        $branch = $request->get('branch');
        $store = $request->get('store');
        $date = $request->get('date');

        $data = TDBBranch::ConnectToPromotion($branch, $store);
        if ($date === "daterange"){
            $startdate = $request->get('startdate');
            $enddate = $request->get('enddate');
           $data = $data->where('promotions.start_date', '>=', $startdate)
               ->where('promotions.end_date', '<=', $enddate);
        }
        $data = $data->get(['branches.name as branch', 'users.store_name as store', 'user_products.description as name',
            'promotions.mechanism as promo', 'promotions.discount as disc', 'promotions.min_purchase as min',
            'promotions.start_date as startdate', 'promotions.end_date as enddate',
            DB::raw('(CASE 
                            WHEN SUM(trx_details.qty) IS NULL THEN 0 
                            ELSE SUM(trx_details.qty) 
                            END) as sold_quantity'),
            DB::raw('(CASE 
                            WHEN (SUM(trx_details.qty) * promotions.discount) IS NULL THEN 0
                             ELSE (SUM(trx_details.qty) * promotions.discount)
                             END) as cost_promotion')
        ]);
//        dd($data[0]);
        return \Datatables::of($data)->make(true);


        if ($date === "daterange") {
            $startdate = $request->get('startdate');
            $enddate = $request->get('enddate');

            $data = TDBBranch::ConnectToPromotion($branch, $store)
                ->where('promotions.start_date', '>=', $startdate)
                ->where('promotions.end_date', '<=', $enddate)
                ->get(['branches.name as branch', 'users.store_name as store', 'user_products.description as name',
                    'promotions.mechanism as promo', 'promotions.discount as disc', 'promotions.min_purchase as min',
                    'promotions.start_date as startdate', 'promotions.end_date as enddate',
                    DB::raw('(CASE 
                            WHEN SUM(trx_details.qty) IS NULL THEN 0
                            ELSE SUM(trx_details.qty)
                            END) as sold_quantity'),
                    DB::raw('(CASE 
                            WHEN (SUM(trx_details.qty) * promotions.discount) IS NULL THEN 0 
                            ELSE (SUM(trx_details.qty) * promotions.discount) 
                            END) as cost_promotion')
                ]);
        } else {
            $data = TDBBranch::ConnectToPromotion($branch, $store)
                ->get(['branches.name as branch', 'users.store_name as store', 'user_products.description as name',
                    'promotions.mechanism as promo', 'promotions.discount as disc', 'promotions.min_purchase as min',
                    'promotions.start_date as startdate', 'promotions.end_date as enddate',
                    DB::raw('(CASE 
                            WHEN SUM(trx_details.qty) IS NULL THEN 0 
                            ELSE SUM(trx_details.qty) 
                            END) as sold_quantity'),
                    DB::raw('(CASE 
                            WHEN (SUM(trx_details.qty) * promotions.discount) IS NULL THEN 0
                             ELSE (SUM(trx_details.qty) * promotions.discount)
                             END) as cost_promotion')
                ]);
        }

        return \Datatables::of($data)->make(true);
    }

    public function getArchiveDatatable(Request $request)
    {
        $branch = $request->get('branch');
        $store = $request->get('store');

        $data = TDBBranch::ConnectToUserProducts($branch, $store)
            ->whereNotNull('user_products.deleted_at')
            ->get(['branches.name as branch', 'users.store_name as store', 'user_products.plu as plu', 'user_products.description as desc', 'user_products.unit as unit', 'user_products.fraction as frac', 'user_products.deleted_at as date']);

        return \Datatables::of($data)->make(true);
    }

    public function getStockDatatable(Request $request)
    {
        ini_set('max_execution_time', '3000');
//        return 'test';
        $branch = $request->get('branch');
        $store = $request->get('store');

        $data = TDBBranch::ConnectUserProductToDiv($branch, $store)
            ->whereNull('user_products.deleted_at')
            ->orderBy('user_products.plu', 'ASC')
//            ->toSql();
//        return $data;
//            ->first();
            ->get(['categories.category as category', 'user_products.plu as plu', 'user_products.id as pid', 'user_products.description as desc',
                \DB::raw('(select sum(trx_details.qty)/30
                    from users
                    join user_products on users.id = user_products.user_id
                    join trx_details on user_products.id = trx_details.user_product_id
                    join trx_headers on trx_headers.id = trx_details.trx_header_id
                    where users.id = ' . $store . ' and user_products.id = pid and date(trx_headers.trx_date) between (curdate() - interval 30 day) and curdate()) as avgsales'),
                'user_products.stock as stock', 'user_products.min_stock as minq', 'user_products.max_stock as maxq', 'user_products.deleted_at as date', 'user_products.price as price',
                'user_products.cost as cost']);
//return $data;
        $div = [];
        $dep = [];
        $kat = [];
        foreach ($data as $row => $item) {
            $temp = Margin::join('divisi', 'master_margin.div', '=', 'divisi.DIV_KODEDIVISI')
                ->join('department', function ($join) {
                    $join->on('master_margin.dep', '=', 'department.DEP_KODEDEPARTEMENT');
                    $join->on('divisi.DIV_KODEDIVISI', '=', 'department.DEP_KODEDIVISI');
                })
                ->join('category', function ($join) {
                    $join->on('master_margin.kat', '=', 'category.KAT_KODEKATEGORI');
                    $join->on('department.DEP_KODEDEPARTEMENT', '=', 'category.KAT_KODEDEPARTEMENT');
                })
                ->where('master_margin.id', '=', $item->category)
                ->get(['divisi.DIV_KODEDIVISI as div', 'department.DEP_KODEDEPARTEMENT as dep', 'category.KAT_KODEKATEGORI as kat']);
            $div[] = $temp[0]->div;
            $dep[] = $temp[0]->dep;
            $kat[] = $temp[0]->kat;
        }
//        return $data;
        $therows = "";

        $trxDetail = TDBTrxDetail::join('user_products', 'trx_details.user_product_id', '=', 'user_products.id')
                    ->select(DB::raw('CONCAT(SUBSTR(user_products.plu, 1, 6), 0) as plu'), 
                        DB::raw('SUM(trx_details.qty) as qty_sold')
                        )
                    ->where('user_products.user_id', '=', $store)
                    ->groupBy('user_products.id')
                    ->get();

        $collectDetail = collect($trxDetail);
        // return $collectDetail->where('plu', '1166390')->first()->qty_sold;
        // dd($collectDetail);
        foreach($data as $i => $value) {
            // dd($collectDetail,$value, $value->plu, $collectDetail->where('plu',  $value->plu), $collectDetail);
            $data[$i]['qty_sold'] = $collectDetail->where('plu', $value->plu)->first()->qty_sold;
            // foreach($trxDetail as $j => $detail) {
            //     if(substr($value->plu, 0, 6) == substr($detail->plu, 0, 6)) {
            //         $data[$i]['qty_sold'] = $detail->qty_sold;
            //         continue;
            //     }
            // }
        }

        foreach ($data as $index => $value) {
            $therows .= "<tr>";
            $therows .= "<td>" . $div[$index] . "</td>";
            $therows .= "<td>" . $dep[$index] . "</td>";
            $therows .= "<td>" . $kat[$index] . "</td>";
            $therows .= "<td>" . $value->plu . "</td>";
            $therows .= "<td>" . $value->desc . "</td>";
            $therows .= "<td style='text-align: right'>" . $value->stock . "</td>";
            if ($value->avgsales != null) {
                $therows .= "<td style='text-align: right'>" . $value->avgsales . "</td>";
            } else {
                $therows .= "<td style='text-align: right'>0</td>";
            }
            $therows .= "<td style='text-align: right'>".$value->qty_sold."</td>";
            $therows .= "<td style='text-align: right'>" . $value->minq . "</td>";
            $therows .= "<td style='text-align: right'>" . $value->maxq . "</td>";
            $therows .= "<td style='text-align: right'>" . $value->cost . "</td>";
            $therows .= "<td style='text-align: right'>" . $value->price . "</td>";
            $therows .= "</tr>";
        }

        return $therows;
    }

    public function exportSalesVersiYKN(Request $request)//TAGVANVAN
    {
        $branch = $request->get('efcabang');
        $store = $request->get('eftoko');
        $cashier = $request->get('efkasir');
        $date = $request->get('efhari');
        $type = $request->get('eftipe');
        $months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $numdays = cal_days_in_month(CAL_GREGORIAN, substr($date, 5, 2), substr($date, 0, 4));

        if ($type === "day") {
            // dd('tests');

            $data = TDBBranch::ConnectToHeader($branch, $store, $cashier)
                ->where('trx_headers.trx_date', 'LIKE', $date)
                ->get(['branches.name as branch', 'users.store_name as store', 'trx_headers.trx_no as invoice', 'trx_headers.trx_date as date', 'trx_headers.grand_total as total', 'trx_headers.margin as margin']);

            $branchname = $data[0]->branch;
            $storename = $data[0]->store;
            $tabledata = [];
            $sales = [];
            $temp = 0;
            $temptotal = 0;
            $countActiveDay = 0;
            $countInvoice = 0;


            $sales["header"] = "sales";
            for ($i = 1; $i < 32; $i++) {
                $colname = "d" . $i;
                foreach ($data as $column) {
                    if (date('d', strtotime($column->date)) == $i) {
                        $temp = $temp + $column->total;
                        if (!empty($column->invoice)) {
                            $countInvoice++;
                        }
                    }
                }
                if ($countInvoice != 0) {
                    $countActiveDay++;
                }
                $sales[$colname] = $temp;
                $temptotal = $temptotal + $temp;
                $temp = 0;
                $countInvoice = 0;
            }
            $numdays = $countActiveDay; //[10-06-2020 #VANVAN] tambahan variable ini
            $sales["total"] = $temptotal;
            $temptotal = 0;
            $sales["rata"] = round($sales["total"] / $numdays);
            array_push($tabledata, $sales);

            $sales["header"] = "margin (rupiah)";
            for ($i = 1; $i < 32; $i++) {
                $colname = "d" . $i;
                foreach ($data as $column) {
                    if (date('d', strtotime($column->date)) == $i) {
                        $temp = $temp + $column->margin;
                    }
                }
                $sales[$colname] = $temp;
                $temptotal = $temptotal + $temp;
                $temp = 0;
            }
            $sales["total"] = $temptotal;
            $temptotal = 0;
            $sales["rata"] = round($sales["total"] / $numdays);
            array_push($tabledata, $sales);

            $sales["header"] = "presented";
            for ($i = 1; $i < 32; $i++) {
                $colname = "d" . $i;
                if ($tabledata[0][$colname] != 0) {
                    $temp = round(($tabledata[1][$colname] / $tabledata[0][$colname]) * 100);
                    $sales[$colname] = $temp;
                }
                $temp = 0;
            }
            $sales["total"] = ($tabledata[1]["total"] / $tabledata[0]["total"]) * 100;
            $sales["rata"] = ($tabledata[1]["rata"] / $tabledata[0]["rata"]) * 100;
            array_push($tabledata, $sales);

            $sales["header"] = "transaksi";
            for ($i = 1; $i < 32; $i++) {
                $colname = "d" . $i;
                foreach ($data as $column) {
                    if (date('d', strtotime($column->date)) == $i) {
                        $temp = $temp + 1;
                    }
                }
                $sales[$colname] = $temp;
                $temptotal = $temptotal + $temp;
                $temp = 0;
            }
            $sales["total"] = $temptotal;
            $sales["rata"] = round($sales["total"] / $numdays);
            array_push($tabledata, $sales);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'LAPORAN SALES PERDAY');
            $sheet->setCellValue('A3', 'Cabang :');
            $sheet->setCellValue('B3', $branchname);
            $sheet->setCellValue('A4', 'TMI :');
            if ($store === "%") {
                $sheet->setCellValue('B4', 'SEMUA TOKO');
            } else {
                $sheet->setCellValue('B4', $storename);
            }
            $monthnow = (int)substr($date, 5, 2);
            $sheet->setCellValue('A5', 'PERIODE :');
            $sheet->setCellValue('B5', $months[$monthnow - 1] . ' ' . substr($date, 0, 4));
            $sheet->setCellValue('A7', 'DESKRIPSI');
            $sheet->setCellValue('B7', $months[$monthnow - 1] . ' ' . substr($date, 0, 4));
            $sheet->setCellValue('AG7', 'TOTAL');
            $sheet->setCellValue('AH7', 'RATA-RATA');
            $sheet->mergeCells("A7:A8");
            $sheet->mergeCells("AG7:AG8");
            $sheet->mergeCells("AH7:AH8");
            $sheet->mergeCells("B7:AF7");

            $sheet->fromArray($tabledata, NULL, 'A9');

            $i = 1;
            $lastColumn = 'AF';
            $lastColumn++;
            for ($currentColumn = 'B'; $currentColumn != $lastColumn; $currentColumn++) {
                $sheet->setCellValue($currentColumn . '8', $i);
                $sheet->getStyle($currentColumn . '8')->getAlignment()->setHorizontal('center');
                $i++;
            }

            for ($currentColumn = 'B'; $currentColumn != $lastColumn; $currentColumn++) {
                $sheet->setCellValue($currentColumn . '11', '=(' . $currentColumn . '10/' . $currentColumn . '9)*100');
            }

            $sheet->setCellValue('AG11', '=SUM(B11:AF11)');
            $sheet->setCellValue('AH11', '=AVERAGE(B11:AF11)');
            $sheet->setCellValue('AG11', '=(AG10/AG9)*100');//[10-06-2020 #VANVAN] tambahan variable ini
            $sheet->setCellValue('AH11', '=(AH10/AH9)*100');//[10-06-2020 #VANVAN] tambahan variable ini


            $sheet->getStyle('B7')->getAlignment()->setHorizontal('center');

            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('AG')->setAutoSize(true);
            $sheet->getColumnDimension('AH')->setAutoSize(true);
            $sheet->getDefaultRowDimension()->setRowHeight(-1);

            $sheet->setTitle("sales per day");

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="salesperday.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');

        } else if ($type === "month") {
            $branch = $request->get('efcabang');
            $store = $request->get('eftoko');
            $date = $request->get('efhari');

            $data = TDBBranch::ConnectToHeaderWOCashier($branch, $store)
                ->where('trx_headers.trx_date', 'LIKE', $date)
                ->groupby(\DB::raw('MONTH(trx_headers.trx_date)'))
                ->get(['branches.name as branch', 'users.store_name as store', 'trx_headers.trx_date as date', \DB::raw('sum(trx_headers.grand_total) as sales'), \DB::raw('sum(trx_headers.margin) as margin'), \DB::raw('count(trx_headers.trx_no) as tcount')]);

            $branchname = $data[0]->branch;
            $storename = $data[0]->store;
            $tabledata = [];
            $sales = [];
            $temp = "0";
            $temptotal = "0";
            $totalActiveMonth = 0;//[08-06-2020 #VANVAN]tambahan variable ini

            $checktCount = 0;//[08-06-2020 #VANVAN]tambahan variable ini

            $sales["header"] = "TOTAL SALES";
            for ($i = 1; $i < 13; $i++) {
                $colname = "m" . $i;
                foreach ($data as $column) {
                    if (date('m', strtotime($column->date)) == $i) {
                        $temp = $temp + $column->sales;
                        $checktCount = $column->tcount;
                    }
                }
                if ($checktCount != 0) {
                    $totalActiveMonth++;
                }
                $sales[$colname] = $temp;
                $temptotal = $temptotal + $temp;
                $temp = 0;
                $checktCount = 0;
            }
            $sales["total"] = $temptotal;
            $temptotal = 0;
            $sales["rata"] = round($sales["total"] / $totalActiveMonth);
            array_push($tabledata, $sales);

            $sales["header"] = "TOTAL MARGIN";
            for ($i = 1; $i < 13; $i++) {
                $colname = "m" . $i;
                foreach ($data as $column) {
                    if (date('m', strtotime($column->date)) == $i) {
                        $temp = $temp + $column->margin;
                    }
                }
                $sales[$colname] = $temp;
                $temptotal = $temptotal + $temp;
                $temp = 0;
            }
            $sales["total"] = $temptotal;
            $temptotal = 0;
            $sales["rata"] = round($sales["total"] / $totalActiveMonth);
            array_push($tabledata, $sales);

            $sales["header"] = "% MARGIN";
            for ($i = 1; $i < 13; $i++) {
                $colname = "m" . $i;
                if ($tabledata[0][$colname] != 0) {
                    $temp = ($tabledata[1][$colname] / $tabledata[0][$colname]) * 100;
                    $sales[$colname] = $temp;
                }
                $temp = 0;
            }
            $sales["total"] = ($tabledata[1]["total"] / $tabledata[0]["total"]) * 100;
            $sales["rata"] = ($tabledata[1]["rata"] / $tabledata[0]["rata"]) * 100;
            array_push($tabledata, $sales);

            $sales["header"] = "JUMLAH TRANSAKSI";
            for ($i = 1; $i < 13; $i++) {
                $colname = "m" . $i;
                foreach ($data as $column) {
                    if (date('m', strtotime($column->date)) == $i) {
                        $temp = $temp + $column->tcount;
                    }
                }
                $sales[$colname] = $temp;
                $temptotal = $temptotal + $temp;
                $temp = 0;
            }
            $sales["total"] = $temptotal;
            $sales["rata"] = round($sales["total"] / $totalActiveMonth);
            array_push($tabledata, $sales);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'REKAP SALES DETAIL');
            $sheet->setCellValue('A3', 'Cabang :');
            $sheet->setCellValue('B3', $branchname);
            $sheet->setCellValue('A4', 'TMI :');
            if ($store === "%") {
                $sheet->setCellValue('B4', 'SEMUA TOKO');
            } else {
                $sheet->setCellValue('B4', $storename);
            }
            $sheet->setCellValue('A5', 'PERIODE :');
            $sheet->setCellValue('B5', 'TAHUN ' . substr($date, 0, 4));

            $sheet->setCellValue('A7', 'TANGGAL');
            $sheet->setCellValue('B7', 'JANUARI');
            $sheet->setCellValue('C7', 'FEBRUARI');
            $sheet->setCellValue('D7', 'MARET');
            $sheet->setCellValue('E7', 'APRIL');
            $sheet->setCellValue('F7', 'MEI');
            $sheet->setCellValue('G7', 'JUNI');
            $sheet->setCellValue('H7', 'JULI');
            $sheet->setCellValue('I7', 'AGUSTUS');
            $sheet->setCellValue('J7', 'SEPTEMBER');
            $sheet->setCellValue('K7', 'OKTOBER');
            $sheet->setCellValue('L7', 'NOVEMBER');
            $sheet->setCellValue('M7', 'DESEMBER');
            $sheet->setCellValue('N7', 'TOTAL');
            $sheet->setCellValue('O7', 'RATA-RATA');

            $sheet->fromArray($tabledata, NULL, 'A8');

            $lastColumn = 'P';
            for ($currentColumn = 'A'; $currentColumn != $lastColumn; $currentColumn++) {
                $sheet->getColumnDimension($currentColumn)->setAutoSize(true);
            }
            // $sheet->setCellValue('N10', '=SUM(B10:M10)');
            // $sheet->setCellValue('O10', '=AVERAGE(B10:M10)');
//=CEILING((AG10/AG9)*100;0.01)
            $sheet->setCellValue('N10', '=(N9/N8)*100');//[10-06-2020 #VANVAN] cara baru
            $sheet->setCellValue('O10', '=(O9/O8)*100');//[10-06-2020 #VANVAN] cara baru

            $sheet->getStyle('B7')->getAlignment()->setHorizontal('center');


            $sheet->getDefaultRowDimension()->setRowHeight(-1);

            $sheet->setTitle("sales per month");

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="salespermonth.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');

        } else if ($type === "product") {
            $countday = 0;
            $branch = $request->get('efcabang');
            $store = $request->get('eftoko');
            $date = $request->get('efhari');

            if ($date === "daterange") {

                $later = new DateTime($request->get('efend'));
                $numdays = ($later->diff(new DateTime($request->get('efstart')))->format("%a")) + 1;

                $startdate = $request->get('efstart') . " 00:00:00";
                $enddate = $request->get('efend') . " 23:59:59";

                $countday = TDBTrxHeader::join('users', 'trx_headers.user_id', '=', 'users.id')
                ->where('trx_headers.user_id', '=', $store)
                        ->whereBetween('trx_headers.trx_date', [$startdate, $enddate])
                        ->groupBy(DB::raw('DATE(trx_headers.trx_date)'))
                        ->get();
                $data = TDBUserProduct::ConnectForFreeplu($store, $branch)
                    ->whereBetween('trx_headers.trx_date', [$startdate, $enddate])
                    ->groupBy('user_products.plu')
                    ->get(['user_products.id as pid', 'user_products.plu as plu', 'categories.category as category', 'user_products.description as description', 
                    \DB::raw('sum(trx_details.qty) as qty'), \DB::raw('(sum(trx_details.sub_total)-sum(trx_details.discount)) as subtotal'), \DB::raw('sum(trx_details.margin) as margin'), \DB::raw('count(distinct trx_details.trx_header_id) as invcount')
                    // \DB::raw('(select sum(trx_details.qty)/30
                    // from users
                    // join user_products on users.id = user_products.user_id
                    // join trx_details on user_products.id = trx_details.user_product_id
                    // join trx_headers on trx_headers.id = trx_details.trx_header_id
                    // where users.id = ' . $store . ' and user_products.id = pid and date(trx_headers.trx_date) between (curdate() - interval 30 day) and curdate()) as avgsales')
                    ]);
            } else {
                if ($date === "%") {
                    $countday = TDBTrxHeader::join('users', 'trx_headers.user_id', '=', 'users.id')
                        ->where('trx_headers.trx_date', 'LIKE', $date)
                        ->where('trx_headers.user_id', '=', $store)
                        ->groupBy(DB::raw('DATE(trx_headers.trx_date)'))
                        ->get();
                    $data = TDBUserProduct::ConnectForFreeplu($store, $branch)
                        ->where('trx_headers.trx_date', 'LIKE', $date)
                        ->groupBy('user_products.plu')
                        ->get(['user_products.id as pid', 'user_products.plu as plu', 'categories.category as category', 'user_products.description as description', \DB::raw('min(trx_headers.trx_date) as mindate'), \DB::raw('max(trx_headers.trx_date) as maxdate'),
                        \DB::raw('sum(trx_details.qty) as qty'), \DB::raw('(sum(trx_details.sub_total)-sum(trx_details.discount)) as subtotal'), \DB::raw('sum(trx_details.margin) as margin'), \DB::raw('count(distinct trx_details.trx_header_id) as invcount') 
                    //     \DB::raw('(select sum(trx_details.qty)/30
                    // from users
                    // join user_products on users.id = user_products.user_id
                    // join trx_details on user_products.id = trx_details.user_product_id
                    // join trx_headers on trx_headers.id = trx_details.trx_header_id
                    // where users.id = ' . $store . ' and user_products.id = pid and date(trx_headers.trx_date) between (curdate() - interval 30 day) and curdate()) as avgsales')
                    ]);

                    $later = new DateTime(substr($data[0]->maxdate, 0, 10));
                    $numdays = ($later->diff(new DateTime(substr($data[0]->mindate, 0, 10)))->format("%a")) + 1;
                } else {
                    // return $date;
                    $countday = TDBTrxHeader::select('trx_date')->where('trx_headers.trx_date', 'LIKE', $date)
                            ->where('trx_headers.user_id', '=', $store)
                            ->groupBy(DB::raw('DATE(trx_headers.trx_date)'))
                            ->get();
                    if (substr($date, 5, 1) === "%") {
                        $data = TDBUserProduct::ConnectForFreeplu($store, $branch)
                            ->where('trx_headers.trx_date', 'LIKE', $date)
                            ->groupBy('user_products.plu')
                            ->get(['user_products.id as pid', 'user_products.plu as plu', 'categories.category as category', 'user_products.description as description', \DB::raw('min(trx_headers.trx_date) as mindate'), \DB::raw('max(trx_headers.trx_date) as maxdate'), \DB::raw('sum(trx_details.qty) as qty'), \DB::raw('sum(trx_details.sub_total) as subtotal'), \DB::raw('sum(trx_details.margin) as margin'), \DB::raw('count(distinct trx_details.trx_header_id) as invcount') 
                    //         \DB::raw('(select sum(trx_details.qty)/30
                    // from users
                    // join user_products on users.id = user_products.user_id
                    // join trx_details on user_products.id = trx_details.user_product_id
                    // join trx_headers on trx_headers.id = trx_details.trx_header_id
                    // where users.id = ' . $store . ' and user_products.id = pid and date(trx_headers.trx_date) between (curdate() - interval 30 day) and curdate()) as avgsales')
                    ]);

                        $later = new DateTime(substr($data[0]->maxdate, 0, 10));
                        $numdays = ($later->diff(new DateTime(substr($data[0]->mindate, 0, 10)))->format("%a")) + 1;
                    } else {
                        $numdays = cal_days_in_month(CAL_GREGORIAN, substr($date, 5, 2), substr($date, 0, 4));

                        $data = TDBUserProduct::ConnectForFreeplu($store, $branch)
                            ->where('trx_headers.trx_date', 'LIKE', $date)
                            ->groupBy('user_products.plu')
                            ->get(['user_products.id as pid', 'user_products.plu as plu', 'categories.category as category', 
                            'user_products.description as description', \DB::raw('sum(trx_details.qty) as qty'), \DB::raw('sum(trx_details.sub_total) as subtotal'), 
                            \DB::raw('sum(trx_details.margin) as margin'), \DB::raw('count(distinct trx_details.trx_header_id) as invcount')
                            //     \DB::raw('(select sum(trx_details.qty)/30
                            // from users
                            // join user_products on users.id = user_products.user_id
                            // join trx_details on user_products.id = trx_details.user_product_id
                            // join trx_headers on trx_headers.id = trx_details.trx_header_id
                            // where users.id = ' . $store . ' and user_products.id = pid and date(trx_headers.trx_date) between (curdate() - interval 30 day) and curdate()) as avgsales')
                            ]);
                            }
                }
            }
            $div = [];
            $dep = [];
            $kat = [];
            foreach ($data as $row => $item) {
                if ($item->category != null) {
                    $temp = Margin::join('divisi', 'master_margin.div', '=', 'divisi.DIV_KODEDIVISI')
                        ->join('department', function ($join) {
                            $join->on('master_margin.dep', '=', 'department.DEP_KODEDEPARTEMENT');
                            $join->on('divisi.DIV_KODEDIVISI', '=', 'department.DEP_KODEDIVISI');
                        })
                        ->join('category', function ($join) {
                            $join->on('master_margin.kat', '=', 'category.KAT_KODEKATEGORI');
                            $join->on('department.DEP_KODEDEPARTEMENT', '=', 'category.KAT_KODEDEPARTEMENT');
                        })
                        ->where('master_margin.id', '=', $item->category)
                        ->get(['divisi.DIV_KODEDIVISI as div', 'department.DEP_KODEDEPARTEMENT as dep', 'category.KAT_KODEKATEGORI as kat']);
                    $div[] = $temp[0]->div;
                    $dep[] = $temp[0]->dep;
                    $kat[] = $temp[0]->kat;
                } else {
                    $div[] = '-';
                    $dep[] = '-';
                    $kat[] = '-';
                }
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'LAPORAN SALES PER PRODUK');
            $sheet->setCellValue('A2', 'CABANG');
            $sheet->setCellValue('A3', 'TOKO');
            $sheet->setCellValue('A4', 'PERIODE');

            $branchname = TDBBranch::where('id', '=', $branch)->get(['name']);
            $sheet->setCellValue('B2', $branchname[0]->name);

            if ($store === '%') {
                $sheet->setCellValue('B3', 'SEMUA TOKO');
            } else {
                $storename = TDBUser::where('id', '=', $store)->get(['store_name']);
                $sheet->setCellValue('B3', $storename[0]->store_name);
            }

            if ($date === 'daterange') {
                $sheet->setCellValue('B4', $request->get('efstart') . ' s/d ' . $request->get('efend'));
            } else if ($date === '%') {
                $sheet->setCellValue('B4', 'ALL TIME');
            } else {
                $sheet->setCellValue('B4', substr($date, 0, 4) . ' ' . $months[((int)substr($date, 5, 2) - 1)]);
            }

            $sheet->setCellValue('A6', 'DIV');
            $sheet->setCellValue('B6', 'DEP');
            $sheet->setCellValue('C6', 'KAT');
            $sheet->setCellValue('D6', 'PLU');
            $sheet->setCellValue('E6', 'DESKRIPSI');
            $sheet->setCellValue('F6', 'SALES QTY');
            $sheet->setCellValue('G6', 'SALES RPH');
            $sheet->setCellValue('H6', 'MGN RPH');
            $sheet->setCellValue('I6', 'MGN %');
            $sheet->setCellValue('J6', 'SPD');
            $sheet->setCellValue('K6', 'JML TRANSAKSI');
            $sheet->getStyle('A6:K6')->getAlignment()->setHorizontal('center');

            $no = 7;
            $countday = count($countday);//[04-01-2022] alasannya dibikin begini karena kalau pakai eloquent count,  output nya ngaco! Kalau di get dulu, kemudian di count di akhir, hasilnya bener!
            foreach ($data as $row) {
                $sheet->setCellValue('A' . $no, $div[$no - 7]);
                $sheet->setCellValue('B' . $no, $dep[$no - 7]);
                $sheet->getStyle('B' . $no)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('C' . $no, $kat[$no - 7]);
                $sheet->getStyle('C' . $no)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('D' . $no, $row->plu);
                $sheet->getStyle('D' . $no)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('E' . $no, $row->description);
                $sheet->setCellValue('F' . $no, $row->qty);
                $sheet->setCellValue('G' . $no, $row->subtotal);
                $sheet->setCellValue('H' . $no, $row->margin);
                $sheet->setCellValue('I' . $no, '=(H' . $no . '/G' . $no . ')*100');
                if ($row->qty != null) {
                    //vanvan avgsales di ganti!
                    // $sheet->setCellValue('J' . $no, round($row->avgsales, 2));
                
                    $newSpd = $row->qty/$countday;
                    $sheet->setCellValue('J' . $no, $newSpd);
                } else {
                    $sheet->setCellValue('J' . $no, '0');
                }
                $sheet->setCellValue('K' . $no, $row->invcount);
                $no++;
            }

            $lastColumn = 'L';
            for ($currentColumn = 'A'; $currentColumn != $lastColumn; $currentColumn++) {
                $sheet->getColumnDimension($currentColumn)->setAutoSize(true);
            }
            $sheet->getDefaultRowDimension()->setRowHeight(-1);

            $sheet->setTitle("sales per item");

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="salesperitem.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        } else if ($type === "branch") {
            $branch = $request->get('efcabang');
            $store = $request->get('eftoko');
            $date = $request->get('efhari');

            // dd($date);
            $year = explode('-', $date);

            // dd($year[0]);
            // ->where(DB::raw('SUBSTR(plu, 1, 6)'), substr($old_plu, 0, 6))

            $footer_data = TDBBranch::join('users', 'users.branch_id', '=', 'branches.id')
                ->join('trx_headers', 'trx_headers.user_id', '=', 'users.id')
                ->where('trx_headers.trx_date', 'LIKE', $date)
                ->select(DB::raw('\'TOTAL\' as branch_name'), DB::raw('\'\' as member_code'),
                    DB::raw('\'\' as store_name'),
                    'trx_headers.trx_date', DB::raw('sum(trx_headers.grand_total) as sales'),
                    DB::raw('sum(trx_headers.margin) as margin')
                )->groupBy(DB::raw('MONTH(trx_headers.trx_date)'));

            $data = TDBBranch::ConnectToHeaderWOCashier($branch, $store)
                // ->where('trx_headers.trx_date','LIKE',$date)
                ->where(DB::raw('YEAR(trx_headers.trx_date)'), '=', $year[0])
                ->groupBy('users.store_name', DB::raw('MONTH(trx_headers.trx_date)'))
                ->union($footer_data)
                ->get(['branches.name as branch_name', 'users.member_code', 'users.store_name', 'trx_headers.trx_date',
                    DB::raw('SUM(trx_headers.grand_total) as sales'),
                    DB::raw('SUM(trx_headers.margin) as margin')]);
            // dd($data[0]);
            $stores = $data->unique('store_name');

            $tabledata = [];
            $ts = 0;
            $tm = 0;
            foreach ($stores as $row) {
                $values = [];
                $storerow = [];
                $storerow[] = $row->branch_name;
                $storerow[] = $row->member_code;
                $storerow[] = $row->store_name;
                foreach ($data as $cell) {
                    if ($row->store_name === $cell->store_name) {
                        $month = (int)substr($cell->trx_date, 5, 2);
                        $values[$month - 1] = array($cell->sales, $cell->margin);
                    }
                }
                for ($i = 0; $i < 12; $i++) {
                    if ($values[$i] != null) {
                        $storerow[] = number_format($values[$i][0], 2);
                        $storerow[] = number_format($values[$i][1], 2);
                        $ts = $ts + $values[$i][0];
                        $tm = $tm + $values[$i][1];
                    } else {
                        $storerow[] = "0";
                        $storerow[] = "0";
                    }
                }
                $storerow[] = number_format($ts, 2);
                $storerow[] = number_format($tm, 2);
                array_push($tabledata, $storerow);
                $ts = 0;
                $tm = 0;
                $values = null;
                $storerow = null;
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'LAPORAN SALES PER CABANG');
            $sheet->setCellValue('A3', 'CABANG');
            $sheet->setCellValue('A4', 'PERIODE');

            if ($branch === '%') {
                $sheet->setCellValue('B3', 'SEMUA CABANG');
            } else {
                $branchname = TDBBranch::where('id', '=', $branch)->get(['name']);
                $sheet->setCellValue('B3', $branchname[0]->name);
            }

            $sheet->setCellValue('B4', 'TAHUN ' . substr($date, 0, 4));

            $sheet->setCellValue('A6', 'Nama Cabang');
            $sheet->setCellValue('B6', 'Kode Member');
            /////////////////////////////////////////////////////////////
            $sheet->setCellValue('C6', 'TMI');
            $sheet->setCellValue('D6', 'Januari');
            $sheet->setCellValue('F6', 'Februari');
            $sheet->setCellValue('H6', 'Maret');
            $sheet->setCellValue('J6', 'April');
            $sheet->setCellValue('L6', 'Mei');
            $sheet->setCellValue('N6', 'Juni');
            $sheet->setCellValue('P6', 'Juli');
            $sheet->setCellValue('R6', 'Agustus');
            $sheet->setCellValue('T6', 'September');
            $sheet->setCellValue('V6', 'Oktober');
            $sheet->setCellValue('X6', 'November');
            $sheet->setCellValue('Z6', 'Desember');
            $sheet->setCellValue('AB6', 'Total');
            $lastColumn = 'AD';
            $helper = 2;
            for ($currentColumn = 'D'; $currentColumn != $lastColumn; $currentColumn++) {
                if ($helper % 2 == 0) {
                    $sheet->mergeCells($currentColumn . '6:' . ($this->incrementLetter($currentColumn, 1)) . '6');
                    $sheet->setCellValue($currentColumn . '7', 'sales');
                    $sheet->setCellValue(($this->incrementLetter($currentColumn, 1)) . '7', 'margin');
                    $sheet->getStyle($currentColumn . '7')->getAlignment()->setHorizontal('center');
                    $sheet->getStyle(($this->incrementLetter($currentColumn, 1)) . '7')->getAlignment()->setHorizontal('center');
                }
                $helper++;
            }
            $sheet->getStyle('A6:AA7')->getAlignment()->setHorizontal('center');

            $sheet->fromArray($tabledata, NULL, 'A8');

            $lastColumn = 'AD';
            for ($currentColumn = 'A'; $currentColumn != $lastColumn; $currentColumn++) {
                $sheet->getColumnDimension($currentColumn)->setAutoSize(true);
            }
            $sheet->getDefaultRowDimension()->setRowHeight(-1);

            $sheet->setTitle("sales per branch");

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="salesperbranch.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        } else if ($type === "recap") {
            $store = $request->get('eftoko');
            $date = $request->get('efhari');

            if ($date === "daterange") {
                $startdate = $request->get('startdate') . " 00:00:00";
                $enddate = $request->get('enddate') . " 23:59:59";

                $data = TDBUserProduct::ConnectForFreeplu($store, $branch)
                    ->whereBetween('trx_headers.trx_date', [$startdate, $enddate])
                    ->groupBy('users.id', 'products.id')
                    ->get(['users.store_name as storename', 'products.id as pid', \DB::raw('sum(trx_headers.grand_total) as sales'), \DB::raw('sum(trx_headers.margin) as margin')]);

                $days = TDBUserProduct::ConnectForFreeplu($store, $branch)
                    ->whereBetween('trx_headers.trx_date', [$startdate, $enddate])
                    ->groupBy('users.id')
                    ->get(['users.store_name as storename', \DB::raw('count(distinct day(trx_headers.trx_date)) as days')]);
            } else {
                $data = TDBUserProduct::ConnectForFreeplu($store, $branch)
                    ->where('trx_headers.trx_date', 'LIKE', $date)
                    ->groupBy('users.id', 'products.id')
                    ->get(['users.store_name as storename', 'products.id as pid', \DB::raw('sum(trx_headers.grand_total) as sales'), \DB::raw('sum(trx_headers.margin) as margin')]);

                $days = TDBUserProduct::ConnectForFreeplu($store, $branch)
                    ->where('trx_headers.trx_date', 'LIKE', $date)
                    ->groupBy('users.id')
                    ->get(['users.store_name as storename', \DB::raw('count(distinct day(trx_headers.trx_date)) as days')]);
            }

            $stores = $data->unique('storename');
            $ts = 0;

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'REKAP SALES DETAIL');
            $sheet->setCellValue('A3', 'CABANG');
            $sheet->setCellValue('A4', 'PERIODE');

            if ($branch === '%') {
                $sheet->setCellValue('B3', 'SEMUA CABANG');
            } else {
                $branchname = TDBBranch::where('id', '=', $branch)->get(['name']);
                $sheet->setCellValue('B3', $branchname[0]->name);
            }

            $sheet->setCellValue('B4', 'TAHUN ' . substr($date, 0, 4));

            $sheet->setCellValue('A6', 'NAMA TOKO');
            $sheet->mergeCells("A6:A7");
            $sheet->setCellValue('B6', 'HARI BUKA');
            $sheet->mergeCells("B6:B7");
            $sheet->setCellValue('C6', 'SALES');
            $sheet->mergeCells("C6:E6");
            $sheet->setCellValue('F6', 'MARGIN RUPIAH');
            $sheet->mergeCells("F6:H6");
            $sheet->setCellValue('I6', 'MARGIN %');
            $sheet->mergeCells("I6:K6");
            $sheet->setCellValue('C7', 'REGULER');
            $sheet->setCellValue('D7', 'FREE PLU');
            $sheet->setCellValue('E7', 'TOTAL');
            $sheet->setCellValue('F7', 'REGULER');
            $sheet->setCellValue('G7', 'FREE PLU');
            $sheet->setCellValue('H7', 'TOTAL');
            $sheet->setCellValue('I7', 'REGULER');
            $sheet->setCellValue('J7', 'FREE PLU');
            $sheet->setCellValue('K7', 'TOTAL');

            $sheet->getStyle('A6:K7')->getAlignment()->setHorizontal('center');

            $highestRow = 8;
            foreach ($stores as $row) {
                $values = [];
                $sheet->setCellValue('A' . $highestRow, $row->storename);

                foreach ($days as $cell) {
                    if ($row->storename === $cell->storename) {
                        $sheet->setCellValue('B' . $highestRow, $cell->days);
                    }
                }

                foreach ($data as $cell) {
                    if ($row->storename === $cell->storename) {
                        if ($cell->pid != null) {
                            $values[0][0] = $values[0][0] + $cell->sales;
                            $values[1][0] = $values[1][0] + $cell->margin;
                        } else {
                            $values[0][1] = $values[0][1] + $cell->sales;
                            $values[1][1] = $values[1][1] + $cell->margin;
                        }
                    }
                }

                for ($i = 0; $i < 2; $i++) {
                    for ($j = 0; $j < 2; $j++) {
                        if ($values[$i][$j] == null) {
                            $values[$i][$j] = "0";
                        }
                    }
                }

                $sheet->setCellValue('C' . $highestRow, $values[0][0]);
                $sheet->setCellValue('D' . $highestRow, $values[0][1]);
                $sheet->setCellValue('E' . $highestRow, '=SUM(C' . $highestRow . ':D' . $highestRow . ')');
                $sheet->setCellValue('F' . $highestRow, $values[1][0]);
                $sheet->setCellValue('G' . $highestRow, $values[1][1]);
                $sheet->setCellValue('H' . $highestRow, '=SUM(F' . $highestRow . ':G' . $highestRow . ')');

                if ($sheet->getCellByColumnAndRow(3, $highestRow)->getValue() != 0) {
                    $sheet->setCellValue('I' . $highestRow, '=(F' . $highestRow . '/C' . $highestRow . ')*100');
                } else {
                    $sheet->setCellValue('I' . $highestRow, 0);
                }
                if ($sheet->getCellByColumnAndRow(4, $highestRow)->getValue() != 0) {
                    $sheet->setCellValue('J' . $highestRow, '=(G' . $highestRow . '/D' . $highestRow . ')*100');
                } else {
                    $sheet->setCellValue('J' . $highestRow, 0);
                }
                if ($sheet->getCellByColumnAndRow(5, $highestRow)->getCalculatedValue() != 0) {
                    $sheet->setCellValue('K' . $highestRow, '=(H' . $highestRow . '/E' . $highestRow . ')*100');
                } else {
                    $sheet->setCellValue('K' . $highestRow, 0);
                }
                $values = null;
                $highestRow += 1;
            }

            $lastColumn = 'L';
            for ($currentColumn = 'A'; $currentColumn != $lastColumn; $currentColumn++) {
                $sheet->getColumnDimension($currentColumn)->setAutoSize(true);
            }
            $sheet->getDefaultRowDimension()->setRowHeight(-1);

            $sheet->setTitle("rekap sales detail");

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="rekapsalesdetail.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        } else if ($type === "branchdate") {
            $branch = $request->get('efcabang');
            $store = $request->get('eftoko');
            $date = $request->get('efhari');

            if ($date === "daterange") {
                $startdate = $request->get('startdate') . " 00:00:00";
                $enddate = $request->get('enddate') . " 23:59:59";

                $data = TDBBranch::ConnectToHeaderWithTmiType($branch, $store)
                    ->whereBetween('trx_headers.trx_date', [$startdate, $enddate])
                    ->groupBy(\DB::raw('date(trx_headers.trx_date)'), 'users.id')
                    ->get(['branches.name as branch', 'users.store_name as store', 'tmi_types.description as type',
                        \DB::raw("(select date(min(trx_headers.trx_date)) from branches join users on users.branch_id = branches.id join trx_headers on trx_headers.user_id = users.id where branches.id = 15 and users.store_name = store) as opendate"),
                        \DB::raw('date(trx_headers.trx_date) as tdate'), \DB::raw('sum(trx_headers.grand_total) as sales'), \DB::raw('sum(trx_headers.margin) as margin'), \DB::raw('count(distinct trx_headers.id) as invcount'), \DB::raw('(sum(trx_headers.margin)/sum(trx_headers.grand_total))*100')])
                    ->toArray();
            } else {
                $data = TDBBranch::ConnectToHeaderWithTmiType($branch, $store)
                    ->where('trx_headers.trx_date', 'LIKE', $date)
                    ->groupBy(\DB::raw('date(trx_headers.trx_date)'), 'users.id')
                    ->get(['branches.name as branch', 'users.store_name as store', 'tmi_types.description as type',
                        \DB::raw("(select date(min(trx_headers.trx_date)) from branches join users on users.branch_id = branches.id join trx_headers on trx_headers.user_id = users.id where branches.id = 15 and users.store_name = store) as opendate"),
                        \DB::raw('date(trx_headers.trx_date) as tdate'), \DB::raw('sum(trx_headers.grand_total) as sales'), \DB::raw('sum(trx_headers.margin) as margin'), \DB::raw('count(distinct trx_headers.id) as invcount'), \DB::raw('(sum(trx_headers.margin)/sum(trx_headers.grand_total))*100')])
                    ->toArray();
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'REKAP SPD, STRUK, APC TMI');
            $sheet->setCellValue('A2', 'PERIODE');
            $sheet->setCellValue('A3', 'IGR');

            $branchname = TDBBranch::where('id', '=', $branch)->get(['name']);
            $sheet->setCellValue('B3', $branchname[0]->name);

            if ($date === 'daterange') {
                $sheet->setCellValue('B2', $request->get('startdate') . ' s/d ' . $request->get('enddate'));
            } else if ($date === '%') {
                $sheet->setCellValue('B2', 'ALL TIME');
            } else {
                $sheet->setCellValue('B2', substr($date, 0, 4) . ' ' . $months[(int)substr($date, 5, 2)]);
            }

            $sheet->setCellValue('A5', 'IGR');
            $sheet->setCellValue('B5', 'NAMA TMI');
            $sheet->setCellValue('C5', 'TYPE');
            $sheet->setCellValue('D5', 'TANGGAL GO');
            $sheet->setCellValue('E5', 'TANGGAL');
            $sheet->setCellValue('F5', 'SPD');
            $sheet->setCellValue('G5', 'STRUK');
            $sheet->setCellValue('H5', 'APC');
            $sheet->setCellValue('I5', 'MARGIN(%)');
            $sheet->getStyle('A5:I5')->getAlignment()->setHorizontal('center');

            $sheet->fromArray($data, NULL, 'A6');

            $lastColumn = 'K';
            for ($currentColumn = 'A'; $currentColumn != $lastColumn; $currentColumn++) {
                $sheet->getColumnDimension($currentColumn)->setAutoSize(true);
            }
            $sheet->getDefaultRowDimension()->setRowHeight(-1);

            $sheet->setTitle("sales percab per date");

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="salespercabperdate.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        } else {
            if ($date === "daterange") {
                $startdate = $request->get('efstart') . " 00:00:00";
                $enddate = $request->get('efend') . " 23:59:59";

                $data = TDBBranch::ConnectToHeader($branch, $store, $cashier)
                    ->whereBetween('trx_headers.trx_date', [$startdate, $enddate])
                    ->get(['branches.name as branch', 'users.store_name as store', 'operators.name as cashier', 'trx_headers.trx_no as invoice', 'trx_headers.trx_date as date', 'trx_headers.grand_total as total', 'trx_headers.margin as margin']);
            } else {
                $data = TDBBranch::ConnectToHeader($branch, $store, $cashier)
                    ->where('trx_headers.trx_date', 'LIKE', $date)
                    ->get(['branches.name as branch', 'users.store_name as store', 'operators.name as cashier', 'trx_headers.trx_no as invoice', 'trx_headers.trx_date as date', 'trx_headers.grand_total as total', 'trx_headers.margin as margin']);
            }

            $totalt = $data->sum('total');
            $totalm = $data->sum('margin');

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'CABANG');
            $sheet->setCellValue('B1', 'TOKO');
            $sheet->setCellValue('C1', 'KASIR');
            $sheet->setCellValue('D1', 'INVOICE');
            $sheet->setCellValue('E1', 'TANGGAL');
            $sheet->setCellValue('F1', 'TOTAL');
            $sheet->setCellValue('G1', 'MARGIN');
            $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal('center');

            $no = 2;
            foreach ($data as $row) {
                $sheet->setCellValue('A' . $no, $row->branch);
                $sheet->setCellValue('B' . $no, $row->store);
                $sheet->setCellValue('C' . $no, $row->cashier);
                $sheet->setCellValue('D' . $no, $row->invoice);
                $sheet->setCellValue('E' . $no, $row->date);
                $sheet->setCellValue('F' . $no, $row->total);
                $sheet->setCellValue('G' . $no, $row->margin);
                $no++;
            }

            $highestRow = $sheet->getHighestRow() + 1;

            $sheet->setCellValue('F' . $highestRow, $totalt);
            $sheet->setCellValue('G' . $highestRow, $totalm);

            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->getColumnDimension('F')->setAutoSize(true);
            $sheet->getColumnDimension('G')->setAutoSize(true);
            $sheet->getDefaultRowDimension()->setRowHeight(-1);

            $sheet->setTitle("sales");

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="sales.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }
    }

    public function exportSyncReport(Request $request)
    {
        ini_set('max_execution_time', 300);
        $cabang = $request->get('cabang');
        $toko = $request->get('toko');
        $date_start = Carbon::parse($request->get('month_start'))->toDateString();
        $date_end = Carbon::parse($request->get('month_end'))->modify('+1 day')->toDateString();
//        dd($cabang);
//        dd(date_diff(Carbon::parse($date_start), Carbon::parse($date_end)));
        $period_date = new DatePeriod(Carbon::parse($date_start), CarbonInterval::day(), Carbon::parse($date_end));
        $date_list = array();
        foreach ($period_date as $p){
            array_push($date_list, $p->toDateString());
        }
//        dd($date_list);
        //ini buat laporan apakah member sudah sync atau belum
        $get_data = TDBUser::select('users.id as user_id','branches.name as cabang', 'users.member_code as member_code', 'users.store_name as store_name',
                        DB::raw('DATE(log_syncs.datetime) as sync_date')
                    )
                    ->LeftJoin('log_syncs', function ($join) use ($date_start, $date_end){
                        $join->on('users.id', '=','log_syncs.user_id');
                        $join->on(DB::Raw('DATE(log_syncs.datetime)'), '>=', DB::Raw('\''.$date_start.'\''));
                        $join->on(DB::Raw('DATE(log_syncs.datetime)'), '<=', DB::Raw('\''.$date_end.'\''));
                    })
                    ->join('branches', 'users.branch_id', '=', 'branches.id');
                    if($cabang !== '%'){
                        $get_data = $get_data->where('branches.id','=',$cabang);
                    }
//                    ->whereIn(DB::Raw('DATE(log_syncs.datetime)'), $date_list)
                    $get_data = $get_data->groupBy('users.id', DB::raw('DATE(log_syncs.datetime)'))
                    ->orderBy('branches.name', 'ASC')
                    ->orderBy('users.store_name', 'ASC')
                    ->orderBy('log_syncs.datetime', 'ASC')
                    ->get();

//        dd($toko);
            //ini buat laporan apakah ada transaksi pada toko di tanggal tersebut
//        $get_data = TDBUser::select('users.id as user_id','branches.name as cabang', 'users.member_code as member_code', 'users.store_name as store_name',
//                        DB::raw('DATE(trx_headers.trx_date) as sync_date')
//                    )
//                    ->LeftJoin('trx_headers', function($join) use ($date_start, $date_end){
//                        $join->on('users.id','=','trx_headers.user_id');
//                        $join->on(DB::Raw('DATE(trx_headers.trx_date)'), '>=', DB::Raw('\''.$date_start.'\''));
//                        $join->on(DB::Raw('DATE(trx_headers.trx_date)'), '<=', DB::Raw('\''.$date_end.'\''));
//                    })
//                    ->join('branches', 'users.branch_id', '=', 'branches.id');
//                    if($cabang !== '%'){
//                        $get_data = $get_data->where('branches.id', '=', $cabang);
//                    }
//                    if($toko !== '%'){
//                        $get_data = $get_data->where('users.id', '=', $toko);
//                    }
//                    $get_data = $get_data->groupBy('users.id', DB::raw('DATE(trx_headers.trx_date)'))
//                    ->orderBy('branches.name', 'ASC')
//                    ->orderBy('users.store_name', 'ASC')
//                    ->orderBy(DB::raw('trx_headers.trx_date'), 'ASC')
//                    ->get();
//        dd($get_data);
//        return response()->json($get_data);
        $output[] = array();
        $start_col = 0;
        $start_row = 0;
        $flag = 0;
        $last_user_id = null;
        $header_name = ["Cabang", "Kode Member", 'Nama Toko'];
        foreach ($header_name as $header){
            $output[$start_row][$header] = $header;
            $start_col++;
        }
        foreach ($date_list as $date){
            $output[$start_row][$date] = $date;
            $start_col++;
        }
        $start_row++;
        foreach ($get_data as $data){
            $flag++;
            $start_col = 0;

            if($last_user_id == null){
                $last_user_id = $data->user_id;
            } else if($last_user_id == $data->user_id){
                continue;
            } else{
                $last_user_id = $data->user_id;
            }

            $output[$start_row]['Cabang'] = $data->cabang;
            $start_col++;
            $output[$start_row]['Kode Member'] = $data->member_code;
            $start_col++;
            $output[$start_row]['Nama Toko'] = $data->store_name;
            $start_col++;
            foreach ($date_list as $date){
                $temp = collect($get_data)
                    ->where('user_id',$data->user_id)
                    ->where('sync_date',$date);

                $output[$start_row][$date] = $temp->count()==0?'N':'Y';
                $start_col++;
            }
            $start_row++;
        }
        $fName = str_replace(" ","", Carbon::now()->toDateTimeString());
        $fName = str_replace(":","", $fName);
        $fName = str_replace("-","", $fName);
//        $fName = explode(' ', $fName);
//        dd($fName);
//        dd($output);
        $fName = 'LaporanSync'.$fName;
//        dd('asdasd');
//        dd($output);
        $myExcel = Excel::create($fName, function ($excel) use ($output, $start_col){
           $excel->sheet('Sheet 1', function ($sheet) use ($output, $start_col){
//               dd(count($output[0]));
               $sheet->fromArray($output);
               $sheet->mergeCells('A1:A2');
               $sheet->mergeCells('B1:B2');
               $sheet->mergeCells('C1:C2');
               $cell_headers = ['A1', 'B1', 'C1', 'D1'];

               foreach ($cell_headers as $c) {
                   $sheet->Cells($c, function ($cell) use ($c){
                       if($c == 'D1'){
                           $cell->setValue('Tanggal');
                       }
                       //todo tujuan code looping ini adalah agar text nya dapat rata tengah
//                       $cell->setAlignment('center');
//                       $cell->setVAlignment('center');
                   });
               }
//               dd($start_col);
               $sheet->mergeCells('D1:'.$this->convertIntToColumnExcel(count($output[0])).'1');

           });
        });
        $myExcel = $myExcel->string('xlsx');
        $response = array(
            'name' => $fName, //no extention needed
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($myExcel) //mime type of used format
        );
        return response()->json($response);
    }

    public function exportIsakuPayment(Request $request)
    {
        $cabang = $request->get('cabang');
        $toko = $request->get('toko');
        $month_start = $request->get('month_start');
        $month_end = $request->get('month_end');

        //            ->leftJoin('trx_details', function($join){
        //////                $join->on('master_margin.dep', '=', 'department.DEP_KODEDEPARTEMENT');
        //////                $join->on('divisi.DIV_KODEDIVISI', '=', 'department.DEP_KODEDIVISI');
        //                $join->on('trx_details.user_product_id', '=', 'user_products.id');
        //                $join->on('trx_details.promotion_id', '=', 'promotions.id');

        //'users', 'trx_headers.user_id', 'users.id'
//        $data_trx = TDBTrxHeader::Rightjoin('users', function ($join) use ($month_start, $month_end){
//            $join->on('users.id', '=', 'trx_headers.user_id');
//            $join->on(DB::raw('CAST(trx_headers.payment_method_id AS INTEGER)'), '=', '2');
////            $join->on(DB::raw('DATE(trx_headers.trx_date)'), '>=', $month_start);
////            $join->on(DB::raw('DATE(trx_headers.trx_date)'), '<=', $month_end);
//        });

        /*
          ->LeftJoin('log_syncs', function ($join) use ($date_start, $date_end){
                        $join->on('users.id', '=','log_syncs.user_id');
                        $join->on(DB::Raw('DATE(log_syncs.datetime)'), '>=', DB::Raw('\''.$date_start.'\''));
                        $join->on(DB::Raw('DATE(log_syncs.datetime)'), '<=', DB::Raw('\''.$date_end.'\''));
                    })
         * */

        $temp_trx_total = 0;
        $start_pos = 0;
        $data_trx = TDBUser::select('users.member_code',
                'users.store_name',
                DB::Raw('users.name as username'),
                DB::Raw(
                        '(
                            CASE WHEN (SUM(trx_headers.grand_total)) 
                                IS NULL THEN 0 
                            ELSE SUM(trx_headers.grand_total) 
                            END
                        )  as trx_total'
                ))
            ->join('trx_headers', function ($join) use ($month_start, $month_end){
                $join->on('users.id', '=', 'trx_headers.user_id');
                $join->on(DB::Raw('DATE(trx_headers.trx_date)'), '>=', DB::Raw('\''.$month_start.'\''));
                $join->on(DB::Raw('DATE(trx_headers.trx_date)'), '<=', DB::Raw('\''.$month_end.'\''));
                $join->on('trx_headers.payment_method_id', '=', DB::Raw('\'2\''));
            });
//            ->where(DB::raw('DATE(trx_headers.trx_date)'), '>=', $month_start)
//            ->where(DB::raw('DATE(trx_headers.trx_date)'), '<=', $month_end)
//            ->where('trx_headers.payment_method_id', '=',2);
        if(!empty($cabang) && $cabang != '%'){
            $data_trx = $data_trx->where('users.branch_id', '=', $cabang);
        }
        if(!empty($toko) && $toko != '%'){
            $data_trx = $data_trx->where('users.id', '=', $toko);
        }
        $data_trx = $data_trx->groupBy('users.id')
                    ->get();

//        dd($data_trx->get());
        $data_sheet = array(array("PT. INTI CAKRAWALA CITRA"));
        $start_pos++;
        array_push($data_sheet, array("Special Project HO"));
        $start_pos++;
        array_push($data_sheet, array(""));
        $start_pos++;
        array_push($data_sheet, array("Tgl. Cetak", Carbon::now()->toDateString()));
        $start_pos++;
        array_push($data_sheet, array("Pkl. Cetak", Carbon::now()->toTimeString()));
        $start_pos++;
        array_push($data_sheet, array("User ID", "??"));
        $start_pos++;
        array_push($data_sheet, array("Halaman", "1"));
        $start_pos++;

        array_push($data_sheet, array(""));
        $start_pos++;

        array_push($data_sheet, array("LAPORAN PEMBAYARAN I.SAKU DI TMI"));
        $start_pos++;

//        if(!empty($cabang) && $cabang != '%'){
        array_push($data_sheet, array("", "TOKO IGR",
            empty($cabang)?"TIDAK ADA CABANG" :
                $cabang == '%' ? "SEMUA CABANG" : TDBBranch::find($cabang)->name
        ));
        $start_pos++;
        array_push($data_sheet, array("", "PERIODE", $month_start.' s/d '.$month_end));
        $start_pos++;
        array_push($data_sheet, array(""));
        $start_pos++;
        array_push($data_sheet, array("No", "TMI", "TMI", "TMI", "TOTAL NILAI TRANSAKSI (Rp)"));
        $start_pos++;
        array_push($data_sheet, array("", "Kode Member", "Nama Member", "Nama Toko", ""));
        $start_pos++;
        for($i=0; $i<$data_trx->count(); $i++){
            $data = $data_trx[$i];
            //        $data_trx = TDBUser::select('users.member_code',
            //                'users.store_name',
            //                DB::Raw('users.name as username'),
            //                DB::Raw('SUM(trx_headers.grand_total) as total_trx'))
//            dd($data);
            array_push($data_sheet, array(
                ($i+1), $data->member_code, $data->username, $data->store_name, $data->trx_total
            ));
            $temp_trx_total += $data->trx_total;
            $start_pos++;
        }
        array_push($data_sheet, array("JUMLAH","","","",$temp_trx_total));
        $start_pos++;
        $start_pos++;

        /*
         FORMAT EXCEL
        PT. Inti Cakrawala Citra
        Special Project HO

        Tgl Cetak :
        Pkl. Cetak :
        User ID :
        Halaman :

            LAPORAN PEMBAYARAN DENGAN I.SALI DI TMI
                Toko Igr :
                Periode : ... s/d ...

        No|	        TMI			           |Total Nilai Transaksi (Rp)
          | Kode | Nama Member | Nama Toko |

         */
        $myExcel = Excel::create("Laporan Transaksi Isaku", function ($excel) use ($data_sheet, $start_pos){
            $excel->sheet('Sheet 1', function ($sheet) use ($data_sheet, $start_pos){
                $sheet->fromArray($data_sheet);
                $sheet->mergeCells('A14:A15');
                $sheet->mergeCells('B14:D14');
                $sheet->mergeCells('E14:E15');
                $sheet->mergeCells('A'.$start_pos.':D'.$start_pos);
//                $cell_headers = ['A1', 'B1', 'C1', 'D1'];
//
//                foreach ($cell_headers as $c) {
//                    $sheet->Cells($c, function ($cell) use ($c){
//                        if($c == 'D1'){
//                            $cell->setValue('Tanggal');
//                        }
//                        //todo tujuan code looping ini adalah agar text nya dapat rata tengah
////                       $cell->setAlignment('center');
////                       $cell->setVAlignment('center');
//                    });
//                }
////               dd($start_col);
//                $sheet->mergeCells('D1:'.$this->convertIntToColumnExcel(count($output[0])).'1');
            });
        });
        $myExcel = $myExcel->string('xlsx');
        $response = array(
            'name' => "LAPORAN TRANSAKSI ISAKU", //no extention needed
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($myExcel) //mime type of used format
        );
        return response()->json($response);
    }

    public function exportLapTrxPerPlu(Request $request)
    {
        ini_set('max_execution_time', 300);

        $cabang = $request->get('cabang');
        $toko = $request->get('toko');
        $month_start = $request->get('month_start');
        $month_end = $request->get('month_end');
        $plu = $request->get('plu');

        $user = array();
        if($toko == '%'){
            $user = '%';
        } else{
            $user = TDBUser::find($toko);
        }
        $prd = TDBProduct::where(DB::raw('SUBSTRING(plu,1,6)'), substr($plu, 0,6))
                ->first();

        if(is_null($prd)){
            //todo return error plu tidak di temukan
        }
        $start_pos = 1;
        $data_sheet = array();
        $start_pos++;
        array_push($data_sheet, array('PLU', $prd->plu));
        $start_pos++;

        array_push($data_sheet, array('DESKRIPSI', $prd->description));
        $start_pos++;

        array_push($data_sheet, array('PERIODE', $month_start . ' - ' . $month_end));
        $start_pos++;

        array_push($data_sheet, array());
        $start_pos++;

        array_push($data_sheet, array('CABANG', 'KODE MEMBER', 'NAMA TOKO', 'JML TRANSAKSI',
            'QTY SALES', 'RP SALES', 'RP MARGIN', '%MARGIN'));
        $start_pos++;

        $data = TDBUser::select(
            DB::Raw('branches.name as branch_name'), 'users.member_code as member_code',
            'users.store_name', DB::Raw('COUNT(*) as count_sales'),
            DB::Raw('SUM(trx_details.qty) as total_qty'),
            DB::Raw('SUM(trx_details.sub_total) total_sales'),
            DB::Raw('SUM(trx_details.margin) total_margin')
        )
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->join('user_products', 'users.id', '=', 'user_products.user_id')
            ->join('trx_headers', function ($join) use ($month_start, $month_end){
                $join->on(DB::Raw('DATE(trx_headers.trx_date)'), '>=', DB::Raw('\''.$month_start.'\''));
                $join->on(DB::Raw('DATE(trx_headers.trx_date)'), '<=', DB::Raw('\''.$month_end.'\''));
            })
            ->join('trx_details', function ($join){
                $join->on('trx_details.trx_header_id', '=', 'trx_headers.id');
                $join->on('trx_details.user_product_id', '=', 'user_products.id');
            })
            ->where('user_products.plu', 'like', substr($plu, 0,6).'%');

        if($user != '%'){
            $data = $data->where('users.id', $user->id);
        }

        $data = $data->groupBy('users.id')->get();
//        dd($data->count(), count($data));
//                $data->chunk(100, function($d) use ($start_pos){
//                    array_push($data_sheet, array(
//                        $d->branch_name, $d->member_code, $d->store_name, $d->count_sales, $d->total_qty,
//                        $d->total_sales, $d->total_margin, '=(G'.$start_pos.'/F'.$start_pos.')*100'
//                    ));
//                    $start_pos++;
//                });
        // A              B          C          D                 E         F           G           H
        //CABANG	KODE MEMBER	NAMA TOKO	JML TRANSAKSI     QTY SALES	RP SALES	RP MARGIN	%MARGIN
        foreach ($data as $d){
            array_push($data_sheet, array(
                $d->branch_name, $d->member_code, $d->store_name, $d->count_sales, $d->total_qty,
                $d->total_sales, $d->total_margin, '=(G'.$start_pos.'/F'.$start_pos.')*100'
            ));
            $start_pos++;
        }
        $start_pos++;

        ///////////////////////////////////////////////////////////////////////////////////////////////

//        $temp_trx_total = 0;
//        $start_pos = 0;
//        $data_trx = TDBUser::select('users.member_code',
//                'users.store_name',
//                DB::Raw('users.name as username'),
//                DB::Raw(
//                        '(
//                            CASE WHEN (SUM(trx_headers.grand_total))
//                                IS NULL THEN 0
//                            ELSE SUM(trx_headers.grand_total)
//                            END
//                        )  as trx_total'
//                ))
//            ->join('trx_headers', function ($join) use ($month_start, $month_end){
//                $join->on('users.id', '=', 'trx_headers.user_id');
//                $join->on(DB::Raw('DATE(trx_headers.trx_date)'), '>=', DB::Raw('\''.$month_start.'\''));
//                $join->on(DB::Raw('DATE(trx_headers.trx_date)'), '<=', DB::Raw('\''.$month_end.'\''));
//                $join->on('trx_headers.payment_method_id', '=', DB::Raw('\'2\''));
//            });
////            ->where(DB::raw('DATE(trx_headers.trx_date)'), '>=', $month_start)
////            ->where(DB::raw('DATE(trx_headers.trx_date)'), '<=', $month_end)
////            ->where('trx_headers.payment_method_id', '=',2);
//        if(!empty($cabang) && $cabang != '%'){
//            $data_trx = $data_trx->where('users.branch_id', '=', $cabang);
//        }
//        if(!empty($toko) && $toko != '%'){
//            $data_trx = $data_trx->where('users.id', '=', $toko);
//        }
//        $data_trx = $data_trx->groupBy('users.id')
//                    ->get();
//
////        dd($data_trx->get());
//        $data_sheet = array(array("PT. INTI CAKRAWALA CITRA"));
//        $start_pos++;
//        array_push($data_sheet, array("Special Project HO"));
//        $start_pos++;
//        array_push($data_sheet, array(""));
//        $start_pos++;
//        array_push($data_sheet, array("Tgl. Cetak", Carbon::now()->toDateString()));
//        $start_pos++;
//        array_push($data_sheet, array("Pkl. Cetak", Carbon::now()->toTimeString()));
//        $start_pos++;
//        array_push($data_sheet, array("User ID", "??"));
//        $start_pos++;
//        array_push($data_sheet, array("Halaman", "1"));
//        $start_pos++;
//
//        array_push($data_sheet, array(""));
//        $start_pos++;
//
//        array_push($data_sheet, array("LAPORAN PEMBAYARAN I.SAKU DI TMI"));
//        $start_pos++;
//
////        if(!empty($cabang) && $cabang != '%'){
//        array_push($data_sheet, array("", "TOKO IGR",
//            empty($cabang)?"TIDAK ADA CABANG" :
//                $cabang == '%' ? "SEMUA CABANG" : TDBBranch::find($cabang)->name
//        ));
//        $start_pos++;
//        array_push($data_sheet, array("", "PERIODE", $month_start.' s/d '.$month_end));
//        $start_pos++;
//        array_push($data_sheet, array(""));
//        $start_pos++;
//        array_push($data_sheet, array("No", "TMI", "TMI", "TMI", "TOTAL NILAI TRANSAKSI (Rp)"));
//        $start_pos++;
//        array_push($data_sheet, array("", "Kode Member", "Nama Member", "Nama Toko", ""));
//        $start_pos++;
//        for($i=0; $i<$data_trx->count(); $i++){
//            $data = $data_trx[$i];
//            //        $data_trx = TDBUser::select('users.member_code',
//            //                'users.store_name',
//            //                DB::Raw('users.name as username'),
//            //                DB::Raw('SUM(trx_headers.grand_total) as total_trx'))
////            dd($data);
//            array_push($data_sheet, array(
//                ($i+1), $data->member_code, $data->username, $data->store_name, $data->trx_total
//            ));
//            $temp_trx_total += $data->trx_total;
//            $start_pos++;
//        }
//        array_push($data_sheet, array("JUMLAH","","","",$temp_trx_total));
//        $start_pos++;
//        $start_pos++;
//
//        /*
//         FORMAT EXCEL
//        PT. Inti Cakrawala Citra
//        Special Project HO
//
//        Tgl Cetak :
//        Pkl. Cetak :
//        User ID :
//        Halaman :
//
//            LAPORAN PEMBAYARAN DENGAN I.SALI DI TMI
//                Toko Igr :
//                Periode : ... s/d ...
//
//        No|	        TMI			           |Total Nilai Transaksi (Rp)
//          | Kode | Nama Member | Nama Toko |
//
//         */
        $myExcel = Excel::create("Laporan Transaksi Per Produk", function ($excel) use ($data_sheet, $start_pos){
            $excel->sheet('Sheet 1', function ($sheet) use ($data_sheet, $start_pos){
                $sheet->fromArray($data_sheet);
//                $sheet->mergeCells('A14:A15');
//                $sheet->mergeCells('B14:D14');
//                $sheet->mergeCells('E14:E15');
//                $sheet->mergeCells('A'.$start_pos.':D'.$start_pos);
//                $cell_headers = ['A1', 'B1', 'C1', 'D1'];
//
//                foreach ($cell_headers as $c) {
//                    $sheet->Cells($c, function ($cell) use ($c){
//                        if($c == 'D1'){
//                            $cell->setValue('Tanggal');
//                        }
//                        //todo tujuan code looping ini adalah agar text nya dapat rata tengah
////                       $cell->setAlignment('center');
////                       $cell->setVAlignment('center');
//                    });
//                }
////               dd($start_col);
//                $sheet->mergeCells('D1:'.$this->convertIntToColumnExcel(count($output[0])).'1');
            });
        });
        $myExcel = $myExcel->string('xlsx');
        $response = array(
            'name' => "LAPORAN TRANSAKSI PER PRODUK", //no extention needed
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($myExcel) //mime type of used format
        );
        return response()->json($response);
    }

    public function exportSalesVersiDKN(Request $request)
    {
ini_set('max_execution_time', (15*60));
        $cabang = $request->get('cabang');
        $toko = $request->get('toko');
        $carbon_month_start = Carbon::parse($request->get('month_start'));
        $month_start = $carbon_month_start->toDateString();
        $month_end = Carbon::parse($request->get('month_end'))->toDateString();
        $txt_month_end = Carbon::parse($request->get('month_end'))->toDateString();
//        dd(Carbon::parse('2020-09')->lastOfMonth(), 'van');
//        return $month_end;
//        dd($toko, $cabang, $month_start, $month_end, $request->all());
        //sample data waroenk trs
//        $cabang = 20;//branch_id
//        $toko = 1; //user_id
//        $month_start = '2020-01-01';
//        $month_end = '2020-09-01';

//        return $request->month_start;

//        dd($month_start, $month_end, $cabang, $toko);
        $period_month = new \DatePeriod(Carbon::parse($month_start), CarbonInterval::month(), Carbon::parse($month_end));
        $period_year = new \DatePeriod(Carbon::parse($month_start), CarbonInterval::year(), Carbon::parse($month_end));
//        dd(Carbon::parse()$month_start->format('m'));
//        $month_start .= '-%';
//        $month_end .= '-%';

//        dd($period_month, $period_year);
// return array(
//     'month' => $period_month,
//     'year' => $period_year
// );
// dd($period_month, $period_year);
        $list_month = array();
        $list_year = array();
        foreach ($period_month as $month) {
            $do = DateTime::createFromFormat('!m', date("m", strtotime($month)));
            array_push($list_month, $do->format('F'));
        }
        foreach ($period_year as $year) {
            array_push($list_year, date("Y", strtotime($year)));
        }
        if($month_start == $month_end) {
            if(count($list_month) <= 0) {
                array_push($list_month, $carbon_month_start->format('F'));
            }
            if(count($list_year) <= 0) {
                array_push($list_year, $carbon_month_start->year);
            }
        }

//        $months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
//        $countActiveDay = 0;

        //'product'->PER PRODUK
        //'day'->PER HARI
        //'month'->PER BULAN
        //'branch'->PER CABANG
        //'branchdate'->PER CABANG PER TANGGAL
        //'recap'->REKAP DETAIL
        $months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $data = array();


        $temp_cabang = TDBBranch::find($cabang);
        $data[0][0] = 'PT. INTI CAKRAWALA CITRA - TMI';
        $data[1][0] = 'PERIODE';
        $data[1][1] = $month_start.' - '.$month_end;
        $data[2][0] = 'IGR';
        if($cabang == '%'){
            $data[2][1] = 'ALL CABANG';
        } else{
            $data[2][1] = $temp_cabang->name;
        }
        $data[3][0] = '';

        $row_start = 4;
        $col_start = 6;

        $merge_header_open_date = array();
        $merge_header_total = array();
        $merge_header_spd = array();
        $merge_header_std = array();
        $merge_header_apc = array();

        $arr_merge_header = array();

        for ($i = 0; $i < 2; $i++) {
            $data[$row_start][0] = 'CABANG';
            $data[$row_start][1] = 'KODE MEMBER';
            $data[$row_start][2] = 'NAMA TOKO';
            $data[$row_start][3] = 'NAMA MEMBER';
            $data[$row_start][4] = 'TIPE';
            $data[$row_start][5] = 'TGL.GO';
            //untuk hari buka
            foreach ($list_year as $year) {
                foreach ($list_month as $month) {
                    if ($i == 0) {
                        $data[$row_start][$col_start] = 'HARI BUKA';
                    } else {
                        $data[$row_start][$col_start] = $month . ' ' . $year;
                    }
                    $col_start++;
                }
            }
            //untuk TOTAL SALES
            foreach ($list_year as $year) {
                foreach ($list_month as $month) {
                    if ($i == 0) {
                        $data[$row_start][$col_start] = 'TOTAL SALES';
                    } else {
                        $data[$row_start][$col_start] = $month . ' ' . $year;
                    }
                    $col_start++;
                }
            }
            //untuk TOTAL STRUK
            foreach ($list_year as $year) {
                foreach ($list_month as $month) {
                    if ($i == 0) {
                        $data[$row_start][$col_start] = 'TOTAL STRUK';
                    } else {
                        $data[$row_start][$col_start] = $month . ' ' . $year;
                    }
                    $col_start++;
                }
            }
            //untuk SPD
            foreach ($list_year as $year) {
                foreach ($list_month as $month) {
                    if ($i == 0) {
                        $data[$row_start][$col_start] = 'SPD';
                    } else {
                        $data[$row_start][$col_start] = $month . ' ' . $year;
                    }
                    $col_start++;
                }
            }
            //untuk STD
            foreach ($list_year as $year) {
                foreach ($list_month as $month) {
                    if ($i == 0) {
                        $data[$row_start][$col_start] = 'STD';
                    } else {
                        $data[$row_start][$col_start] = $month . ' ' . $year;
                    }
                    $col_start++;
                }
            }
            //untuk APC
            foreach ($list_year as $year) {
                foreach ($list_month as $month) {
                    if ($i == 0) {
                        $data[$row_start][$col_start] = 'APC';
                    } else {
                        $data[$row_start][$col_start] = $month . ' ' . $year;
                    }
                    $col_start++;
                }
            }
            //untuk MARGIN
            foreach ($list_year as $year) {
                foreach ($list_month as $month) {
                    if ($i == 0) {
                        $data[$row_start][$col_start] = 'MARGIN';
                    } else {
                        $data[$row_start][$col_start] = $month . ' ' . $year;
                    }
                    $col_start++;
                }
            }
            //untuk MRG (%)
            foreach ($list_year as $year) {
                foreach ($list_month as $month) {
                    if ($i == 0) {
                        $data[$row_start][$col_start] = 'MRG (%)';
                    } else {
                        $data[$row_start][$col_start] = $month . ' ' . $year;
                    }
                    $col_start++;
                }
            }
            $row_start++;
        }
        $min_id = TDBTrxHeader::select(DB::raw('MIN(trx_headers.id) as id'))
                ->join('users', 'trx_headers.user_id', '=', 'users.id')
                // ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->whereDate('trx_headers.trx_date', '=', $month_start);
        $max_id = TDBTrxHeader::select(DB::raw('MAX(trx_headers.id) as id'))
                ->join('users', 'trx_headers.user_id', '=', 'users.id')
                // ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->whereDate('trx_headers.trx_date', '=', $month_end);
        if ($cabang != '%') {
            // $min_id = $min_id->where('branches.id', '=', $cabang);
            $min_id = $min_id->whereIn('users.id', function($query) use ($cabang){
                $query->select('id')
                    ->from('users')
                    ->where('branch_id', '=', $cabang);
            });
            // $max_id = $max_id->where('branches.id', '=', $cabang);
            $max_id = $max_id->whereIn('users.id', function($query) use ($cabang){
                $query->select('id')
                    ->from('users')
                    ->where('branch_id', '=', $cabang);
            });
        }
        if ($toko != '%'){
            $min_id = $min_id->where('users.id', '=', $toko);
            $max_id = $max_id->where('users.id', '=', $toko);
        }
        $min_id = $min_id->first()->id;
        $max_id = $max_id->first()->id;


        $trx_header = TDBTrxHeader::select('id', 'user_id', 'trx_date', 'grand_total', 'margin')
                    ->wherein('id', function($query) use ($month_start, $month_end){
                        $query->select('id')
                            ->from('trx_headers')
                            ->whereBetween(DB::raw('DATE(trx_date)'), [$month_start, $month_end]);
                    });
                    
        $data_rekap = TDBUser::select(
            'branches.name as branch_name', 'users.member_code', 'users.name as username',
            'tmi_types.description as tmi_name',
            'tmi_types.id as tmi_id', 'trx_headers.trx_date', 'users.id as user_id', 'users.store_name',
            DB::raw('DATE_FORMAT(IFNULL(users.open_date,  users.created_at),\'%d-%m-%Y\') as go_date'),
            DB::raw('SUM(trx_headers.grand_total) as grand_total'),
            DB::raw('SUM(trx_headers.margin) as margin_transaction'),
            DB::raw('COUNT(trx_headers.id) as count_invoice'),
            DB::raw('MONTHNAME(trx_headers.trx_date) as month_name'),
            DB::raw('YEAR(trx_headers.trx_date) as trx_year')
        )
            ->join('tmi_types', 'users.tmi_type_id', '=', 'tmi_types.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->leftJoin(DB::raw('('.$trx_header->toSql().') as trx_headers'), 'users.id', '=', 'trx_headers.user_id')
            ->mergeBindings($trx_header->getQuery());
            // ->LeftJoin('trx_headers', function($join) use ($month_start, $month_end, $min_id, $max_id){
            //     $join->on('users.id','=','trx_headers.user_id');
            //     //$join->on('trx_headers.trx_date', '>=', DB::Raw('\''.$month_start.'\''));
            //     //$join->on('trx_headers.trx_date', '<=', DB::Raw('\''.$month_end.'\''));
            //     $join->on('trx_headers.id', '>=', DB::raw('\''.$min_id.'\''));
            //     $join->on('trx_headers.id', '<=', DB::raw('\''.$max_id.'\''));
            // });

        if ($cabang != '%') {
            $data_rekap = $data_rekap->where('branches.id', '=', $cabang);
        }
        if ($toko != '%'){
            $data_rekap = $data_rekap->where('users.id', '=', $toko);
        }
        
        $data_rekap = $data_rekap
            ->orderBy('branch_name', 'ASC')
            ->orderBy('users.store_name', 'ASC')
            ->groupBy('users.id', DB::raw('DATE(trx_headers.trx_date)'))
            ->get();

        $fName = str_replace(" ","", Carbon::now()->toDateTimeString());
        $fName = str_replace(":","", $fName);
        $fName = str_replace("-","", $fName);
        $fName = $fName.'LaporanSPD_STD_APC';
        $myExcel = \Maatwebsite\Excel\Facades\Excel::create($fName, function ($excel) use ($data, $data_rekap, $list_month, $list_year, $month_start, $month_end) {
            $excel->sheet('Sheetname', function ($sheet) use ($data, $data_rekap, $list_month, $list_year, $month_start, $month_end) {
                $sheet->fromArray($data);

                $start_row = 8;
                $temp_user_id = -1;
                $count_users = 0;
                for ($i = 0; $i < count($data_rekap); $i++) {
                    $d = $data_rekap[$i];

                    if (($temp_user_id == -1) || ($temp_user_id != $d->user_id)) {
                        $temp_user_id = $d->user_id;
                        $count_users++;
                    } else {
                        //kondisi jika sama
                        continue;
                    }
                    //$data[$row_start][0] = 'IGR';
                    //            $data[$row_start][1] = 'KDTK';
                    //            $data[$row_start][2] = 'NAMA TOKO';
                    //            $data[$row_start][3] = 'NAMA TMI';
                    //            $data[$row_start][4] = 'KLASIFIKASI';
                    //            $data[$row_start][5] = 'TGL.GO';
                    $data_row[0] = $d->branch_name;
                    $data_row[1] = $d->member_code;
                    $data_row[2] = $d->store_name;
                    $data_row[3] = $d->username;
                    $data_row[4] = $d->tmi_name;
                    $data_row[5] = $d->go_date;

                    //'branches.name as branch_name', 'users.member_code', 'users.name as username',
                    //                'users.created_at as go_date', 'tmi_types.description as tmi_name',
                    //                'tmi_types.id as tmi_id', 'trx_headers.trx_date', 'users.id as user_id',
                    //                DB::raw('SUM(trx_headers.grand_total) as grand_total'),
                    //                DB::raw('COUNT(trx_headers.id) as count_invoice'),
                    //                DB::raw('MONTHNAME(trx_headers.trx_date) as month_name'),
                    //                DB::raw('YEAR(trx_headers.trx_date) as trx_year')
                    $temp_data = collect($data_rekap);
                    $temp_data = $temp_data->where('user_id', $d->user_id);
                    //todo my journey start here!
//                    dd($temp_data);

                    $arr_spd = array();

                    //todo Buat Kolom HARI BUKA (CLEARED)
                    $index_next_row = 6;
                    $index_arr = 0;
                    $arr_merge_header[0][0] = $month_start != $month_end ? $index_next_row + 1 : $index_next_row + 1;
                    foreach ($list_year as $year) {
                        foreach ($list_month as $month) {
                            $count_trx = $temp_data->where('month_name', $month)
                                ->where('trx_year', intval($year));
                            $counts = $count_trx->count();
                            $data_row[$index_next_row] = $counts;
                            $index_next_row++;

                            $arr_spd[$index_arr]['count_sign'] = $counts;
                            $index_arr++;
                        }
                    }
                    $arr_merge_header[0][1] = $index_next_row;

                    $arr_merge_header[1][0] = $month_start != $month_end ? $index_next_row + 1 : $index_next_row + 1;
                    $index_arr = 0;
                    //todo Buat Kolom TOTAL SALES (CLEARED)
                    foreach ($list_year as $year) {
                        foreach ($list_month as $month) {
                            $sum_sales = $temp_data->where('month_name', $month)
                                ->where('trx_year', intval($year));
                            $grand_totals = 0;
                            $count_invoice = 0;
                            $margin_totals = 0;
                            foreach ($sum_sales as $sum) {
                                $grand_totals = $grand_totals + $sum->grand_total;
                                $count_invoice = $count_invoice + $sum->count_invoice;
                                $margin_totals = $margin_totals + $sum->margin_transaction;
                            }
                            $data_row[$index_next_row] = $grand_totals;
                            $index_next_row++;

//                            $arr_spd[$index_arr][1] = $grand_totals ;
//                            $arr_spd[$index_arr][2] = $count_invoice;
                            $arr_spd[$index_arr]['grand_totals'] = $grand_totals;
                            $arr_spd[$index_arr]['count_invoice'] = $count_invoice;
                            $arr_spd[$index_arr]['margin_totals'] = $margin_totals;
                            $index_arr++;
                        }
                    }
                    $arr_merge_header[1][1] = $index_next_row;

                    $arr_merge_header[2][0] = $month_start != $month_end ? $index_next_row + 1 : $index_next_row + 1;
                    $index_arr = 0;
                    //todo Buat Kolom TOTAL STRUK (CLEARED)
                    foreach ($list_year as $year) {
                        foreach ($list_month as $month) {
                            $sum_sales = $temp_data->where('month_name', $month)
                                ->where('trx_year', intval($year));
                            $grand_totals = 0;
                            $count_invoice = 0;
                            $margin_totals = 0;
                            foreach ($sum_sales as $sum) {
                                $grand_totals = $grand_totals + $sum->grand_total;
                                $count_invoice = $count_invoice + $sum->count_invoice;
                                $margin_totals = $margin_totals + $sum->margin_transaction;
                            }
                            $data_row[$index_next_row] = $count_invoice;
                            $index_next_row++;

//                            $arr_spd[$index_arr][1] = $grand_totals ;
//                            $arr_spd[$index_arr][2] = $count_invoice;
                            $arr_spd[$index_arr]['grand_totals'] = $grand_totals;
                            $arr_spd[$index_arr]['count_invoice'] = $count_invoice;
                            $arr_spd[$index_arr]['margin_totals'] = $margin_totals;
                            $index_arr++;
                        }
                    }
                    $arr_merge_header[2][1] = $index_next_row;
                    $index_arr = 0;
                    // 0 = jumlah hari masuk
                    // 1 = total sales
                    // 2 = total_invoice
                    // 3 = spd
                    // 4 = std
                    // 5 = apc
                    //todo BUAT KOLOM SPD (TINGGAL VALIDASI NOL DI PEMBAGI)
                    $arr_merge_header[3][0] = $month_start != $month_end ? $index_next_row + 1 : $index_next_row + 1;
                    foreach ($arr_spd as $spd) {
                        if ($spd['count_invoice'] == 0) {
                            $temp_spd = 0;
                        } else {
                            $temp_spd = round(($spd['grand_totals'] / $spd['count_sign']), 2);
                        }
                        $data_row[$index_next_row] = $temp_spd;
                        $index_next_row++;

                        $arr_spd[$index_arr]['spd'] = $temp_spd;
                        $index_arr++;
                    }
                    $index_arr = 0;
                    $arr_merge_header[3][1] = $index_next_row;

                    $arr_merge_header[4][0] = $month_start != $month_end ? $index_next_row + 1 : $index_next_row + 1;
                    //todo BUAT KOLOM STD (CLEARED)
                    foreach ($arr_spd as $spd) {
                        //                            $temp_margin = round(($spd['margin_totals'] / $spd['grand_totals']) * 100, 2);
                        if($spd['count_sign'] == 0){
                            $data_row[$index_next_row] = 0;
                        } else {
                            $data_row[$index_next_row] = round($spd['count_invoice'] / $spd['count_sign'], 2);
                        }
                        $index_next_row++;

                        if($spd['count_sign'] == 0) {
                            $arr_spd[$index_arr]['std'] = 0;
                        } else{
                            $arr_spd[$index_arr]['std'] = round($spd['count_invoice'] / $spd['count_sign'], 2);
                        }
                        $index_arr++;

                    }
                    $arr_merge_header[4][1] = $index_next_row;

                    $arr_merge_header[5][0] = $month_start != $month_end ? $index_next_row + 1 : $index_next_row + 1;
                    $index_arr = 0;
                    //todo BUAT KOLOM APC (CLEARED)
                    foreach ($arr_spd as $spd) {
                        if ($spd['std'] == 0) {
                            $temp_apc = 0;
                        } else {
                            $temp_apc = round(($spd['spd'] / $spd['std']), 2);
                        }
                        $data_row[$index_next_row] = $temp_apc;
                        $index_next_row++;

                        $arr_spd[$index_arr]['apc'] = $temp_apc;
                        $index_arr++;
                    }
                    $arr_merge_header[5][1] = $index_next_row;
                    $index_arr = 0;
//                    dd($arr_spd);

//                    $this->convertIntToColumnExcel('27'); /// error di 27

                    $arr_merge_header[6][0] = $month_start != $month_end ? $index_next_row + 1 : $index_next_row + 1;
                    foreach ($arr_spd as $spd){
                        if($spd['margin_totals'] == 0){
                            $temp_margin = '0';
                        } else{
                            $temp_margin = round($spd['margin_totals'], 2);
                        }

                        $data_row[$index_next_row] = $temp_margin;
                        $index_next_row++;
                        $arr_spd[$index_arr]['margin'] = $temp_margin;
                        $index_arr++;
                    }
                    $arr_merge_header[6][1] = $index_next_row;
                    $index_arr = 0;

                    $arr_merge_header[7][0] = $month_start != $month_end ? $index_next_row + 1 : $index_next_row + 1;

                    //todo BUAT KOLOM MRG (%) (CLEARED)
                    foreach ($arr_spd as $spd) {
                        if ($spd['grand_totals'] == 0) {
                            $temp_margin = '0';
                        } else {
                            $temp_margin = round(($spd['margin_totals'] / $spd['grand_totals']) * 100, 2);
                        }
                        $data_row[$index_next_row] = $temp_margin;
                        $index_next_row++;

                        $arr_spd[$index_arr]['mrg'] = $temp_margin;
                        $index_arr++;
                    }
                    $arr_merge_header[7][1] = $index_next_row;
                    $index_arr = 0;

                    $sheet->row($start_row, $data_row);
                    //TODO MERGING HERE
                    foreach ($arr_merge_header as $merge) {
                        $sheet->mergeCells($this->convertIntToColumnExcel($merge[0]) . '6:' . $this->convertIntToColumnExcel($merge[1]) . '6');
                    }
                    $sheet->mergeCells('A6:A7');
                    $sheet->mergeCells('B6:B7');
                    $sheet->mergeCells('C6:C7');
                    $sheet->mergeCells('D6:D7');
                    $sheet->mergeCells('E6:E7');
                    $sheet->mergeCells('F6:F7');

                    $start_row++;
                }
                $last_pos = $start_row - 1;
                $row_total = array();
                $row_total[0] = 'JUMLAH ( ' . $count_users . ' TOKO )';
                $row_total[1] = 'JUMLAH ( ' . $count_users . ' TOKO )';
                $row_total[2] = 'JUMLAH ( ' . $count_users . ' TOKO )';
                $row_total[3] = 'JUMLAH ( ' . $count_users . ' TOKO )';
                $row_total[4] = 'JUMLAH ( ' . $count_users . ' TOKO )';
                $row_total[5] = 'JUMLAH ( ' . $count_users . ' TOKO )';
                $loop = 0;
//                dd($arr_merge_header);
                foreach ($arr_merge_header as $merge) {
                    for ($i = $merge[0]; $i <= $merge[1]; $i++) {
                        $selisih_jarak = $merge[1]-$merge[0]+1;

                        if($loop==0 || $loop==1 || $loop==2){
                            // 0 = Hari Buka
                            // 1 = Total Sales
                            $row_total[$i] = '=SUM(' . $this->convertIntToColumnExcel($i) . '8' . ':' . $this->convertIntToColumnExcel($i) . $last_pos . ')';
                        } else if($loop==3){
                            //todo
                            // 2 = SPD -> TOTAL / HARI BUKA
                            $row_total[$i] = '='.$this->convertIntToColumnExcel(($i-$selisih_jarak-$selisih_jarak)).($last_pos+1)."/".$this->convertIntToColumnExcel($i-$selisih_jarak-$selisih_jarak-$selisih_jarak).($last_pos+1);
                        } else if($loop==4){
                            // 3 = STD ->AVERAGE
                            $row_total[$i] = '='.$this->convertIntToColumnExcel(($i-$selisih_jarak-$selisih_jarak)).($last_pos+1)."/".$this->convertIntToColumnExcel($i-$selisih_jarak-$selisih_jarak-$selisih_jarak-$selisih_jarak).($last_pos+1);
                        } else if($loop==5){
                            // 4 = APC -> SPD/STD
                            $row_total[$i] = '='.$this->convertIntToColumnExcel(($i-$selisih_jarak-$selisih_jarak)).($last_pos+1)."/".$this->convertIntToColumnExcel($i-$selisih_jarak).($last_pos+1);

                        } else if($loop==6){
//                             5 = Margin
                            $row_total[$i] = '=SUM(' . $this->convertIntToColumnExcel($i) . '8' . ':' . $this->convertIntToColumnExcel($i) . $last_pos . ')';
                        } else if($loop==7){
                            $row_total[$i] = '=('.$this->convertIntToColumnExcel(($i-$selisih_jarak)).($last_pos+1)."/".$this->convertIntToColumnExcel($i-$selisih_jarak-$selisih_jarak-$selisih_jarak-$selisih_jarak-$selisih_jarak-$selisih_jarak).($last_pos+1).')*100';
                        }
                    }
                    $loop++;
                }
                $sheet->row($start_row, $row_total);
                $sheet->mergeCells('A' . $start_row . ':F' . $start_row);
//                $sheet->row($start_row, array(
//                    '','','','','','=SUM(F8:F'.$last_pos.')'
//                ));

//                $sheet->cells('A6:ZZ6', function($cells){
//                    $cells->setAlignment('center');
//                });
            });
        });

        $myExcel = $myExcel->string('xlsx');
        $response = array(
            'status' => 1,
            'name' => $fName, //no extention needed
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($myExcel) //mime type of used format
        );
        return response()->json($response);        
    }

    public function exportSalesVersiAJY(Request $request) {
        // https://www.codewall.co.uk/increase-php-script-max-execution-time-limit-using-ini_set-function/
        // https://stackoverflow.com/questions/4306605/is-ini-setmax-execution-time-0-a-bad-idea
        ini_set('max_execution_time', 0);

        if(empty($request->month_start) && empty($request->month_end)) {
            return response()->json(['status'=>0,'message'=>'Bulan mulai dan bulan akhir harus di isi']);
        }
        if(empty($request->month_start)) {
            return response()->json(['status'=>0,'message'=>'Bulan mulai harus di isi']);
        }
        if(empty($request->month_end)) {
            return response()->json(['status'=>0,'message'=>'Bulan akhir harus di isi']);
        }
        $month_start = $request->month_start;
        $month_end = $request->month_end;

        $month_start = Carbon::parse($request->month_start)->firstOfMonth()->toDateString();
        $month_end = Carbon::parse($request->month_end)->lastOfMonth()->modify('+1 day')->toDateString();

        $period_month = new \DatePeriod(Carbon::parse($month_start), CarbonInterval::month(), Carbon::parse($month_end));
        
        $list_month = array();
        $last_year = 0;
        foreach ($period_month as $month) {
            $title = $month->format('F_Y');
            $year = $month->format('Y');
            if($last_year == 0 || $last_year != $year) {
                $arr_y = array();
                $last_year = $year;
                $arr_y['title'] = 'Sales_'.$year;
                $arr_y['year'] = $year;
                $arr_y['date_range'] = null;
                $arr_y['annual_year'] = 1;
                array_push($list_month, $arr_y);
            }
            $m['title'] = $title;
            $m['year'] = $year;
            $m['date_range'] = [
                $month->firstOfMonth()->toDateString(),
                $month->lastOfMonth()->toDateString()
            ];
            array_push($list_month, $m);
            $arr_y['annual_year'] = 0;
            $last_year = $year;
        }
        $custAddress = new CustomerAddress();
        $custAddressDbName = $custAddress['table'];
        
        $masterKodepos = new MasterKodepos();
        $masterKodeposDbName = $masterKodepos['table'];

        $select = TDBUser::withTrashed()->join($custAddressDbName, 'users.member_code', '=', $custAddressDbName.'.kode_member')
                ->join($masterKodeposDbName, $custAddressDbName.'.kode_pos', '=', $masterKodeposDbName.'.pos_kode');

        $listColumnName = '';
        $temp = 1;
        $selectedData = array();
        $totalMonth = count($list_month);
        foreach($list_month as $month) {
            $colName = $month['title'];
            $queryJoin = 'SELECT user_id, SUM(grand_total) as grand_total FROM ';
            $tableName = app(TDBTrxHeader::class)->getTable();
            $queryJoin .= $tableName;
            $isAnnual = $month['annual_year'];
            if($isAnnual == 1) {
                $queryJoin .= ' WHERE YEAR(trx_date) = \'' . $month['year'].'\'';
            } else {
                $range_date = $month['date_range'];
                if($range_date == null) {
                    dd('ada yang salah!');
                } else if(count($range_date) < 2) {
                    dd('jumlah tidak sesuai!');
                }
                $queryJoin .= ' WHERE DATE(trx_date) BETWEEN DATE(\''.$range_date[0].'\') AND DATE(\''.$range_date[1].'\') ';
            }
            
            $queryJoin .= ' GROUP BY user_id';

            $select = $select->leftJoin(DB::raw('('.$queryJoin.') as '.$colName), 
                        'users.id','=',$colName.'.user_id');
            $select_raw_name = ' IFNULL('.$colName.'.grand_total,0) as \''.$colName.'\'';
            $listColumnName .= $select_raw_name;
            array_push($selectedData, $colName);
            if(($temp) < $totalMonth) {
                $listColumnName .= ',';
            }

            $temp++;
        }
                /*
                left join(
                select user_id, sum(grand_total) as tot from trx_headers
                where year(trx_date) = 2020
                group by user_id
                ) as s2020 on u.id = s2020.user_id
                left join(
                */

        $select = $select->select(
                $masterKodeposDbName.'.pos_propinsi as provinsi', $masterKodeposDbName.'.pos_kabupaten as kabupaten',
                $masterKodeposDbName.'.pos_kecamatan as kecamatan', $masterKodeposDbName.'.pos_kelurahan as kelurahan',
                'users.member_code as member_code', 'users.store_name', DB::raw($listColumnName)
                )->orderBy(DB::raw('UPPER('.$masterKodeposDbName.'.pos_propinsi'.')')    , 'ASC')
                ->orderBy(DB::raw('UPPER('.$masterKodeposDbName.'.pos_kabupaten'.')')   , 'ASC')
                ->orderBy(DB::raw('UPPER('.$masterKodeposDbName.'.pos_kecamatan'.')')   , 'ASC')
                ->orderBy(DB::raw('UPPER('.$masterKodeposDbName.'.pos_kelurahan'.')')   , 'ASC')
                ->orderBy(DB::raw('UPPER(users.store_name)'), 'ASC')
                ->groupBy('users.member_code')
                ->get();
        $select = collect($select);

        // return $select;

        $arr_excel = array();

        /*
        {
		"provinsi": "BALI",
		"kabupaten": "KOTA DENPASAR",
		"kecamatan": "DENPASAR TIMUR",
		"kelurahan": "SUMERTA KAUH",
		"member_code": "AM2339",
		"store_name": "KPBMT MART",
		"Sales_2021": "108533400",
		"November_2021": null,
		"December_2021": null,
		"Sales_2022": null,
		"January_2022": null
	    }
        */

        $index_start = 6;

        $header = array();
        $header[0] = 'POS_PROPINSI'; // provinsi
        $header[1] = 'POS_KABUPATEN'; // kabupaten
        $header[2] = 'POS_KECAMATAN'; // kecamatan
        $header[3] = 'POS_KELURAHAN'; // kelurahan
        $header[4] = 'KODEMEMBER'; // member_code
        $header[5] = 'NAMATOKO'; // store_name
        foreach($selectedData as $index => $selected) {
            $header[$index + $index_start] = $selected; //$selected
        }

        array_push($arr_excel, $header);

        $last_provinsi = '';
        $last_kabupaten = '';
        $last_kecamatan = '';
        $last_kelurahan = '';

        $tail = array();

        $tail[0] = "";//provinsi
        $tail[1] = "";//kabupaten
        $tail[2] = "";//kecamatan
        $tail[3] = "";//kelurahan
        $tail[4] = '';//member_code
        $tail[5] = '';//store_name
        foreach($selectedData as $index => $selected) {
            $tail[$index + $index_start] = $selected;
        }
        array_push($select, $tail);

        $totalRow = count($select);
        
        foreach($select as $index => $s){
            $current_provinsi = $s['provinsi'];
            $current_kabupaten = $s['kabupaten'];
            $current_kelurahan = $s['kelurahan'];
            $current_kecamatan = $s['kecamatan'];

            if($last_provinsi == '') {
                $last_provinsi = $current_provinsi;
            }
            if($last_kabupaten == '') {
                $last_kabupaten = $current_kabupaten;
            }
            if($last_kelurahan == '') {
                $last_kelurahan = $current_kelurahan;
            }
            if($last_kecamatan == '') {
                $last_kecamatan = $current_kecamatan;
            }
        
            if($last_kelurahan != $current_kelurahan) {
                $kel = array();
                $kel[0] = $last_provinsi;//provinsi
                $kel[1] = $last_kabupaten;//kabupaten
                $kel[2] = $last_kecamatan;//kecamatan
                $kel[3] = $last_kelurahan;//kelurahan
                $kel[4] = '';//member_code
                $kel[5] = '';//store_name
                foreach($selectedData as $index => $selected) {
                    $kel[$index + $index_start] = $select
                                        ->where('provinsi', $last_provinsi)
                                        ->where('kabupaten', $last_kabupaten)
                                        ->where('kecamatan', $last_kecamatan)
                                        ->where('kelurahan', $last_kelurahan)
                                        ->sum($selected);
                }
                array_push($arr_excel, $kel);
                $last_kelurahan = $current_kelurahan;
            }
            if($last_kecamatan != $current_kecamatan) {
                $kecamatan = array();
                $kecamatan[0] = $last_provinsi;//provinsi
                $kecamatan[1] = $last_kabupaten;//kabupaten
                $kecamatan[2] = $last_kecamatan;//kecamatan
                $kecamatan[3] = '';//kelurahan
                $kecamatan[4] = '';//member_code
                $kecamatan[5] = '';//store_name
                foreach($selectedData as $index => $selected) {
                    $kecamatan[$index + $index_start] = $select
                                        ->where('provinsi', $last_provinsi)
                                        ->where('kabupaten', $last_kabupaten)
                                        ->where('kecamatan', $last_kecamatan)
                                        ->sum($selected);
                }
                array_push($arr_excel, $kecamatan);
                $last_kecamatan = $current_kecamatan;
            }
            if($last_kabupaten != $current_kabupaten) {
                $kabupaten = array();
                $kabupaten[0] = $last_provinsi;//provinsi
                $kabupaten[1] = $last_kabupaten;//kabupaten
                $kabupaten[2] = '';//kecamatan
                $kabupaten[3] = '';//kelurahan
                $kabupaten[4] = '';//member_code
                $kabupaten[5] = '';//store_name
                foreach($selectedData as $index => $selected) {
                    $kabupaten[$index + $index_start] = $select
                                        ->where('provinsi', $last_provinsi)
                                        ->where('kabupaten', $last_kabupaten)
                                        ->sum($selected);
                }
                array_push($arr_excel, $kabupaten);
                $last_kabupaten = $current_kabupaten;
            }
            if($last_provinsi != $current_provinsi) {
                $provinsi = array();
                $provinsi[0] = $last_provinsi;//provinsi
                $provinsi[1] = '';//kabupaten
                $provinsi[2] = '';//kecamatan
                $provinsi[3] = '';//kelurahan
                $provinsi[4] = '';//member_code
                $provinsi[5] = '';//store_name
                foreach($selectedData as $index => $selected) {
                    $provinsi[$index + $index_start] = $select->where('provinsi', $last_provinsi)
                                        ->sum($selected);
                }
                array_push($arr_excel, $provinsi);
                $last_provinsi = $current_provinsi;
            }
            // array_push($arr_excel, $s);

            $data = array();
            $data[0] = $s['provinsi'];//provinsi
            $data[1] = $s['kabupaten'];//kabupaten
            $data[2] = $s['kecamatan'];//kecamatan
            $data[3] = $s['kelurahan'];//kelurahan
            $data[4] = $s['member_code'];//member_code
            $data[5] = $s['store_name'];//store_name
            foreach($selectedData as $index => $selected) {
                $data[$index + $index_start] = (double)$select
                                    ->where('member_code', $s['member_code'])
                                    ->first()
                                    ->$selected;
            }
            array_push($arr_excel, $data);

            // if($current_provinsi == "" && $current_kabupaten == "" && 
            //     $current_kecamatan == "" && $current_kelurahan == ""    
            // ) {
            // if( ($totalRow - 1) == $index) {
                
            // }
        }

        $tail[0] = 'GRAND TOTAL';//provinsi
                foreach($selectedData as $index => $selected) {
                    $tail[$index + $index_start] = $select->sum($selected);
                }
        array_push($arr_excel, $tail);

        // foreach($arr_excel as $index => $value) {
        //     // $arr_excel[$index] = $value->toArray();
        //     dd($value, $value[0]);
        // }

        // return $arr_excel;//todo test lagi besok!!

        $fName = str_replace(":","", $fName);
        $fName = str_replace("-","", $fName);
        $fName = $fName.'Laporan_SALES_PAK_ANJAY';

        $fName = str_replace(" ","", Carbon::now()->toDateTimeString());

        $excelFile = \Maatwebsite\Excel\Facades\Excel::create($fName, function ($excel) use ($arr_excel){
            $excel->sheet('sheet 1', function ($sheet) use ($arr_excel) {
                //todo habis ini harus di loop dulu!
                $sheet->fromArray($arr_excel);
            });
        });
        $excelFile = $excelFile->string('xlsx');

        $response = array(
            'name' => $fName, //no extention needed
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($excelFile) //mime type of used format
        );
        return response()->json($response);
    }

    public function exportEPPFile(Request $request)
    {

        $cabang = $request->get('cabang');
        $date = $request->get('date');
        $len = strlen($date);
        if($len == 0){
            //return error
        }
        $epp_data = array();

        try{
        $epp_data = TDBProduct::leftJoin('user_products as up', 'products.id', '=', 'up.product_id')
                    ->leftJoin('trx_details as d', 'up.id', '=', 'd.user_product_id')
                    ->leftJoin('trx_headers as h', function($join) use($date){
                        $join->on('d.trx_header_id', '=', 'h.id');
                        // $join->on('h.trx_date', '>=', DB::raw($date));
                        // $last_date = date("Y-m-t", strtotime($date));
                        // $join->on('h.trx_date', '<=', 
                        //     DB::raw($last_date));
                    });
                    if($cabang != '%'){
                        $epp_data = $epp_data
                            ->where('products.branch_id', '=', $cabang);
                    }
                    $epp_data = $epp_data->where('h.trx_date', 'like', $date)
                    ->select(DB::raw('products.plu as plu,
                    products.description,
                    count(distinct up.id) toko_disp,
                    count(distinct h.user_id) toko_jual,
                    count(distinct d.trx_header_id) jml_struk,
                    IFNULL(sum(d.qty),0) jml_qty,
                    IFNULL(sum(d.qty) / count(distinct up.id) / 30, 0) spd_toko_disp,
                    IFNULL(sum(d.qty) / count(distinct h.user_id) / 30, 0) spd_toko_jual,
                    IFNULL(sum(d.sub_total), 0) sales,
                    IFNULL(truncate(sum(d.cost) / sum(d.qty), 2), 0) hpp,
                    IFNULL(avg(d.price), 0) hargajual,
                    IFNULL(sum(d.margin), 0) margin,
                    IFNULL(truncate((sum(d.margin) / sum(d.sub_total)) * 100, 2), 0) percent_margin'))
                    ->groupBy('products.plu')
                    ->get();
                    // return response()->json(['status'=>0, 'message'=>$epp_data]);
                    // DB::raw('last_day(STR_TO_DATE(\"'.$transaction_date.'\", \"%Y-%m-%d\"))')
        }catch(Exception $e){
            dd($e);
            return response()->json(['status'=>0, 'message'=>json_encode($e->getMessage())]);
        }

        $myExcel = \Maatwebsite\Excel\Facades\Excel::create('testfile.xls', function ($excel) use ($epp_data) {
            $excel->sheet('Sheetname', function ($sheet) use ($epp_data) {
                $sheet->fromArray($epp_data);

            });
        });

        

        $myExcel = $myExcel->string('xlsx');
        $now = date_format(Carbon::now(), 'YmdHis');
        $branches = TDBBranch::where('id','=',$cabang)->first();
        $fName = (empty($branches->name)?'DATA':($branches->name.$branches->code)).'_'.$now;
        $response = array(
            'name' => $fName, //no extention needed
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($myExcel) //mime type of used format
        );
        return response()->json($response);

        return response()->json(['status'=>0, 'message'=>json_encode($epp_data)]);

//        dd(Carbon::parse('2020-09')->lastOfMonth(), 'van');
//        return $month_end;
//        dd($toko, $cabang, $month_start, $month_end, $request->all());
        //sample data waroenk trs
//        $cabang = 20;//branch_id
//        $toko = 1; //user_id
//        $month_start = '2020-01-01';
//        $month_end = '2020-09-01';

//        return $request->month_start;

//        dd($month_start, $month_end, $cabang, $toko);
        $period_month = new \DatePeriod(Carbon::parse($month_start), CarbonInterval::month(), Carbon::parse($month_end));
        $period_year = new \DatePeriod(Carbon::parse($month_start), CarbonInterval::year(), Carbon::parse($month_end));
//        dd(Carbon::parse()$month_start->format('m'));
//        $month_start .= '-%';
//        $month_end .= '-%';

//        dd($period_month, $period_year);
        $list_month = array();
        $list_year = array();
        foreach ($period_month as $month) {
            $do = DateTime::createFromFormat('!m', date("m", strtotime($month)));
            array_push($list_month, $do->format('F'));
        }
        foreach ($period_year as $year) {
            array_push($list_year, date("Y", strtotime($year)));
        }

//        $months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
//        $countActiveDay = 0;

        //'product'->PER PRODUK
        //'day'->PER HARI
        //'month'->PER BULAN
        //'branch'->PER CABANG
        //'branchdate'->PER CABANG PER TANGGAL
        //'recap'->REKAP DETAIL
        $months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $data = array();


        $temp_cabang = TDBBranch::find($cabang);
        $data[0][0] = 'PT. INTI CAKRAWALA CITRA - TMI';
        $data[1][0] = 'PERIODE';
        $data[1][1] = $month_start.' - '.$month_end;
        $data[2][0] = 'IGR';
        if($cabang == '%'){
            $data[2][1] = 'ALL CABANG';
        } else{
            $data[2][1] = $temp_cabang->name;
        }
        $data[3][0] = '';

        $row_start = 4;
        $col_start = 6;

        $merge_header_open_date = array();
        $merge_header_total = array();
        $merge_header_spd = array();
        $merge_header_std = array();
        $merge_header_apc = array();

        $arr_merge_header = array();

        for ($i = 0; $i < 2; $i++) {
            $data[$row_start][0] = 'CABANG';
            $data[$row_start][1] = 'KODE MEMBER';
            $data[$row_start][2] = 'NAMA TOKO';
            $data[$row_start][3] = 'NAMA MEMBER';
            $data[$row_start][4] = 'TIPE';
            $data[$row_start][5] = 'TGL.GO';
            //untuk hari buka
            foreach ($list_year as $year) {
                foreach ($list_month as $month) {
                    if ($i == 0) {
                        $data[$row_start][$col_start] = 'HARI BUKA';
                    } else {
                        $data[$row_start][$col_start] = $month . ' ' . $year;
                    }
                    $col_start++;
                }
            }
            //untuk TOTAL SALES
            foreach ($list_year as $year) {
                foreach ($list_month as $month) {
                    if ($i == 0) {
                        $data[$row_start][$col_start] = 'TOTAL SALES';
                    } else {
                        $data[$row_start][$col_start] = $month . ' ' . $year;
                    }
                    $col_start++;
                }
            }
            //untuk TOTAL STRUK
            foreach ($list_year as $year) {
                foreach ($list_month as $month) {
                    if ($i == 0) {
                        $data[$row_start][$col_start] = 'TOTAL STRUK';
                    } else {
                        $data[$row_start][$col_start] = $month . ' ' . $year;
                    }
                    $col_start++;
                }
            }
            //untuk SPD
            foreach ($list_year as $year) {
                foreach ($list_month as $month) {
                    if ($i == 0) {
                        $data[$row_start][$col_start] = 'SPD';
                    } else {
                        $data[$row_start][$col_start] = $month . ' ' . $year;
                    }
                    $col_start++;
                }
            }
            //untuk STD
            foreach ($list_year as $year) {
                foreach ($list_month as $month) {
                    if ($i == 0) {
                        $data[$row_start][$col_start] = 'STD';
                    } else {
                        $data[$row_start][$col_start] = $month . ' ' . $year;
                    }
                    $col_start++;
                }
            }
            //untuk APC
            foreach ($list_year as $year) {
                foreach ($list_month as $month) {
                    if ($i == 0) {
                        $data[$row_start][$col_start] = 'APC';
                    } else {
                        $data[$row_start][$col_start] = $month . ' ' . $year;
                    }
                    $col_start++;
                }
            }
            //untuk MARGIN
            foreach ($list_year as $year) {
                foreach ($list_month as $month) {
                    if ($i == 0) {
                        $data[$row_start][$col_start] = 'MARGIN';
                    } else {
                        $data[$row_start][$col_start] = $month . ' ' . $year;
                    }
                    $col_start++;
                }
            }
            //untuk MRG (%)
            foreach ($list_year as $year) {
                foreach ($list_month as $month) {
                    if ($i == 0) {
                        $data[$row_start][$col_start] = 'MRG (%)';
                    } else {
                        $data[$row_start][$col_start] = $month . ' ' . $year;
                    }
                    $col_start++;
                }
            }
            $row_start++;
        }

        //        $cabang = $request->get('cabang');
        //        $toko = $request->get('toko');
        //        $month_start = $request->get('month_start');
        //        $month_end = $request->get('month_end');
        $data_rekap = TDBUser::select(
            'branches.name as branch_name', 'users.member_code', 'users.name as username',
            'tmi_types.description as tmi_name',
            'tmi_types.id as tmi_id', 'trx_headers.trx_date', 'users.id as user_id', 'users.store_name',
            DB::raw('DATE_FORMAT(users.created_at,\'%d-%m-%Y\') as go_date'),
            DB::raw('SUM(trx_headers.grand_total) as grand_total'),
            DB::raw('SUM(trx_headers.margin) as margin_transaction'),
            DB::raw('COUNT(trx_headers.id) as count_invoice'),
            DB::raw('MONTHNAME(trx_headers.trx_date) as month_name'),
            DB::raw('YEAR(trx_headers.trx_date) as trx_year')
        )
            ->join('tmi_types', 'users.tmi_type_id', '=', 'tmi_types.id')
            ->join('branches', 'users.branch_id', '=', 'branches.id')
            ->LeftJoin('trx_headers', function($join) use ($month_start, $month_end){
                $join->on('users.id','=','trx_headers.user_id');
                $join->on('trx_headers.trx_date', '>=', DB::Raw('\''.$month_start.'\''));
                $join->on('trx_headers.trx_date', '<=', DB::Raw('\''.$month_end.'\''));
            });
        if ($cabang != '%') {
            $data_rekap = $data_rekap->where('branches.id', '=', $cabang);
        }
        if ($toko != '%'){
            $data_rekap = $data_rekap->where('users.id', '=', $toko);
        }
//        dd($month_start, $month_end);
        $data_rekap = $data_rekap//->whereBetween('trx_headers.trx_date', [$month_start, $month_end])
//            ->orderBy('tmi_types.id', 'ASC')
//            ->orderBy('users.id', 'ASC')
            ->orderBy('branch_name', 'ASC')
            ->orderBy('users.store_name', 'ASC')
            ->groupBy('users.id', DB::raw('DATE(trx_headers.trx_date)'))
            ->get();
//        dd($data_rekap);
//        return $data_rekap;
//            $tmi_id = -1;
//            $count_tmi_type = 0;


//        } else if($type ==="product"){
//
//        }

        $fName = str_replace(" ","", Carbon::now()->toDateTimeString());
        $fName = str_replace(":","", $fName);
        $fName = str_replace("-","", $fName);
        $fName = $fName.'LaporanSPD_STD_APC';

        $myExcel = \Maatwebsite\Excel\Facades\Excel::create($fName, function ($excel) use ($data, $data_rekap, $list_month, $list_year) {
            $excel->sheet('Sheetname', function ($sheet) use ($data, $data_rekap, $list_month, $list_year) {
                $sheet->fromArray($data);

                $start_row = 8;
                $temp_user_id = -1;
                $count_users = 0;
                for ($i = 0; $i < count($data_rekap); $i++) {
                    $d = $data_rekap[$i];

                    if (($temp_user_id == -1) || ($temp_user_id != $d->user_id)) {
                        $temp_user_id = $d->user_id;
                        $count_users++;
                    } else {
                        //kondisi jika sama
                        continue;
                    }
                    //$data[$row_start][0] = 'IGR';
                    //            $data[$row_start][1] = 'KDTK';
                    //            $data[$row_start][2] = 'NAMA TOKO';
                    //            $data[$row_start][3] = 'NAMA TMI';
                    //            $data[$row_start][4] = 'KLASIFIKASI';
                    //            $data[$row_start][5] = 'TGL.GO';
                    $data_row[0] = $d->branch_name;
                    $data_row[1] = $d->member_code;
                    $data_row[2] = $d->store_name;
                    $data_row[3] = $d->username;
                    $data_row[4] = $d->tmi_name;
                    $data_row[5] = $d->go_date;

                    //'branches.name as branch_name', 'users.member_code', 'users.name as username',
                    //                'users.created_at as go_date', 'tmi_types.description as tmi_name',
                    //                'tmi_types.id as tmi_id', 'trx_headers.trx_date', 'users.id as user_id',
                    //                DB::raw('SUM(trx_headers.grand_total) as grand_total'),
                    //                DB::raw('COUNT(trx_headers.id) as count_invoice'),
                    //                DB::raw('MONTHNAME(trx_headers.trx_date) as month_name'),
                    //                DB::raw('YEAR(trx_headers.trx_date) as trx_year')
                    $temp_data = collect($data_rekap);
                    $temp_data = $temp_data->where('user_id', $d->user_id);
                    //todo my journey start here!
//                    dd($temp_data);

                    $arr_spd = array();

                    //todo Buat Kolom HARI BUKA (CLEARED)
                    $index_next_row = 6;
                    $index_arr = 0;
                    $arr_merge_header[0][0] = $month_start != $month_end ? $index_next_row + 1 : $index_next_row;
                    foreach ($list_year as $year) {
                        foreach ($list_month as $month) {
                            $count_trx = $temp_data->where('month_name', $month)
                                ->where('trx_year', intval($year));
                            $counts = $count_trx->count();
                            $data_row[$index_next_row] = $counts;
                            $index_next_row++;

                            $arr_spd[$index_arr]['count_sign'] = $counts;
                            $index_arr++;
                        }
                    }
                    $arr_merge_header[0][1] = $index_next_row;

                    $arr_merge_header[1][0] = $month_start != $month_end ? $index_next_row + 1 : $index_next_row;
                    $index_arr = 0;
                    //todo Buat Kolom TOTAL SALES (CLEARED)
                    foreach ($list_year as $year) {
                        foreach ($list_month as $month) {
                            $sum_sales = $temp_data->where('month_name', $month)
                                ->where('trx_year', intval($year));
                            $grand_totals = 0;
                            $count_invoice = 0;
                            $margin_totals = 0;
                            foreach ($sum_sales as $sum) {
                                $grand_totals = $grand_totals + $sum->grand_total;
                                $count_invoice = $count_invoice + $sum->count_invoice;
                                $margin_totals = $margin_totals + $sum->margin_transaction;
                            }
                            $data_row[$index_next_row] = $grand_totals;
                            $index_next_row++;

//                            $arr_spd[$index_arr][1] = $grand_totals ;
//                            $arr_spd[$index_arr][2] = $count_invoice;
                            $arr_spd[$index_arr]['grand_totals'] = $grand_totals;
                            $arr_spd[$index_arr]['count_invoice'] = $count_invoice;
                            $arr_spd[$index_arr]['margin_totals'] = $margin_totals;
                            $index_arr++;
                        }
                    }
                    $arr_merge_header[1][1] = $index_next_row;

                    $arr_merge_header[2][0] = $month_start != $month_end ? $index_next_row + 1 : $index_next_row;
                    $index_arr = 0;
                    //todo Buat Kolom TOTAL STRUK (CLEARED)
                    foreach ($list_year as $year) {
                        foreach ($list_month as $month) {
                            $sum_sales = $temp_data->where('month_name', $month)
                                ->where('trx_year', intval($year));
                            $grand_totals = 0;
                            $count_invoice = 0;
                            $margin_totals = 0;
                            foreach ($sum_sales as $sum) {
                                $grand_totals = $grand_totals + $sum->grand_total;
                                $count_invoice = $count_invoice + $sum->count_invoice;
                                $margin_totals = $margin_totals + $sum->margin_transaction;
                            }
                            $data_row[$index_next_row] = $count_invoice;
                            $index_next_row++;

//                            $arr_spd[$index_arr][1] = $grand_totals ;
//                            $arr_spd[$index_arr][2] = $count_invoice;
                            $arr_spd[$index_arr]['grand_totals'] = $grand_totals;
                            $arr_spd[$index_arr]['count_invoice'] = $count_invoice;
                            $arr_spd[$index_arr]['margin_totals'] = $margin_totals;
                            $index_arr++;
                        }
                    }
                    $arr_merge_header[2][1] = $index_next_row;
                    $index_arr = 0;
                    // 0 = jumlah hari masuk
                    // 1 = total sales
                    // 2 = total_invoice
                    // 3 = spd
                    // 4 = std
                    // 5 = apc
                    //todo BUAT KOLOM SPD (TINGGAL VALIDASI NOL DI PEMBAGI)
                    $arr_merge_header[3][0] = $month_start != $month_end ? $index_next_row + 1 : $index_next_row;
                    foreach ($arr_spd as $spd) {
                        if ($spd['count_invoice'] == 0) {
                            $temp_spd = 0;
                        } else {
                            $temp_spd = round(($spd['grand_totals'] / $spd['count_sign']), 2);
                        }
                        $data_row[$index_next_row] = $temp_spd;
                        $index_next_row++;

                        $arr_spd[$index_arr]['spd'] = $temp_spd;
                        $index_arr++;
                    }
                    $index_arr = 0;
                    $arr_merge_header[3][1] = $index_next_row;

                    $arr_merge_header[4][0] = $month_start != $month_end ? $index_next_row + 1 : $index_next_row;
                    //todo BUAT KOLOM STD (CLEARED)
                    foreach ($arr_spd as $spd) {
                        //                            $temp_margin = round(($spd['margin_totals'] / $spd['grand_totals']) * 100, 2);
                        if($spd['count_sign'] == 0){
                            $data_row[$index_next_row] = 0;
                        } else {
                            $data_row[$index_next_row] = round($spd['count_invoice'] / $spd['count_sign'], 2);
                        }
                        $index_next_row++;

                        if($spd['count_sign'] == 0) {
                            $arr_spd[$index_arr]['std'] = 0;
                        } else{
                            $arr_spd[$index_arr]['std'] = round($spd['count_invoice'] / $spd['count_sign'], 2);
                        }
                        $index_arr++;

                    }
                    $arr_merge_header[4][1] = $index_next_row;

                    $arr_merge_header[5][0] = $month_start != $month_end ? $index_next_row + 1 : $index_next_row;
                    $index_arr = 0;
                    //todo BUAT KOLOM APC (CLEARED)
                    foreach ($arr_spd as $spd) {
                        if ($spd['std'] == 0) {
                            $temp_apc = 0;
                        } else {
                            $temp_apc = round(($spd['spd'] / $spd['std']), 2);
                        }
                        $data_row[$index_next_row] = $temp_apc;
                        $index_next_row++;

                        $arr_spd[$index_arr]['apc'] = $temp_apc;
                        $index_arr++;
                    }
                    $arr_merge_header[5][1] = $index_next_row;
                    $index_arr = 0;
//                    dd($arr_spd);

//                    $this->convertIntToColumnExcel('27'); /// error di 27

                    $arr_merge_header[6][0] = $month_start != $month_end ? $index_next_row + 1 : $index_next_row;
                    foreach ($arr_spd as $spd){
                        if($spd['margin_totals'] == 0){
                            $temp_margin = '0';
                        } else{
                            $temp_margin = round($spd['margin_totals'], 2);
                        }

                        $data_row[$index_next_row] = $temp_margin;
                        $index_next_row++;
                        $arr_spd[$index_arr]['margin'] = $temp_margin;
                        $index_arr++;
                    }
                    $arr_merge_header[6][1] = $index_next_row;
                    $index_arr = 0;

                    $arr_merge_header[7][0] = $month_start != $month_end ? $index_next_row + 1 : $index_next_row;

                    //todo BUAT KOLOM MRG (%) (CLEARED)
                    foreach ($arr_spd as $spd) {
                        if ($spd['grand_totals'] == 0) {
                            $temp_margin = '0';
                        } else {
                            $temp_margin = round(($spd['margin_totals'] / $spd['grand_totals']) * 100, 2);
                        }
                        $data_row[$index_next_row] = $temp_margin;
                        $index_next_row++;

                        $arr_spd[$index_arr]['mrg'] = $temp_margin;
                        $index_arr++;
                    }
                    $arr_merge_header[7][1] = $index_next_row;
                    $index_arr = 0;

                    $sheet->row($start_row, $data_row);
                    //TODO MERGING HERE
//                    $sheet->mergeCells('A1:E1');
                    foreach ($arr_merge_header as $merge) {
                        $sheet->mergeCells($this->convertIntToColumnExcel($merge[0]) . '6:' . $this->convertIntToColumnExcel($merge[1]) . '6');
                    }
                    $sheet->mergeCells('A6:A7');
                    $sheet->mergeCells('B6:B7');
                    $sheet->mergeCells('C6:C7');
                    $sheet->mergeCells('D6:D7');
                    $sheet->mergeCells('E6:E7');
                    $sheet->mergeCells('F6:F7');

                    $start_row++;
                }
                $last_pos = $start_row - 1;
                $row_total = array();
                $row_total[0] = 'JUMLAH ( ' . $count_users . ' TOKO )';
                $row_total[1] = 'JUMLAH ( ' . $count_users . ' TOKO )';
                $row_total[2] = 'JUMLAH ( ' . $count_users . ' TOKO )';
                $row_total[3] = 'JUMLAH ( ' . $count_users . ' TOKO )';
                $row_total[4] = 'JUMLAH ( ' . $count_users . ' TOKO )';
                $row_total[5] = 'JUMLAH ( ' . $count_users . ' TOKO )';
                $loop = 0;
//                dd($arr_merge_header);
                foreach ($arr_merge_header as $merge) {
                    for ($i = $merge[0]; $i <= $merge[1]; $i++) {
                        $selisih_jarak = $merge[1]-$merge[0]+1;

                        if($loop==0 || $loop==1 || $loop==2){
                            // 0 = Hari Buka
                            // 1 = Total Sales
                            $row_total[$i] = '=SUM(' . $this->convertIntToColumnExcel($i) . '8' . ':' . $this->convertIntToColumnExcel($i) . $last_pos . ')';
                        } else if($loop==3){
                            //todo
                            // 2 = SPD -> TOTAL / HARI BUKA
                            $row_total[$i] = '='.$this->convertIntToColumnExcel(($i-$selisih_jarak-$selisih_jarak)).($last_pos+1)."/".$this->convertIntToColumnExcel($i-$selisih_jarak-$selisih_jarak-$selisih_jarak).($last_pos+1);
                        } else if($loop==4){
                            // 3 = STD ->AVERAGE
                            $row_total[$i] = '='.$this->convertIntToColumnExcel(($i-$selisih_jarak-$selisih_jarak)).($last_pos+1)."/".$this->convertIntToColumnExcel($i-$selisih_jarak-$selisih_jarak-$selisih_jarak-$selisih_jarak).($last_pos+1);
                        } else if($loop==5){
                            // 4 = APC -> SPD/STD
                            $row_total[$i] = '='.$this->convertIntToColumnExcel(($i-$selisih_jarak-$selisih_jarak)).($last_pos+1)."/".$this->convertIntToColumnExcel($i-$selisih_jarak).($last_pos+1);

                        } else if($loop==6){
//                             5 = Margin
                            $row_total[$i] = '=SUM(' . $this->convertIntToColumnExcel($i) . '8' . ':' . $this->convertIntToColumnExcel($i) . $last_pos . ')';
                        } else if($loop==7){
                            $row_total[$i] = '=('.$this->convertIntToColumnExcel(($i-$selisih_jarak)).($last_pos+1)."/".$this->convertIntToColumnExcel($i-$selisih_jarak-$selisih_jarak-$selisih_jarak-$selisih_jarak-$selisih_jarak-$selisih_jarak).($last_pos+1).')*100';
                        }
                    }
                    $loop++;
                }
                $sheet->row($start_row, $row_total);
                $sheet->mergeCells('A' . $start_row . ':F' . $start_row);
//                $sheet->row($start_row, array(
//                    '','','','','','=SUM(F8:F'.$last_pos.')'
//                ));

//                $sheet->cells('A6:ZZ6', function($cells){
//                    $cells->setAlignment('center');
//                });
            });
        });

        $myExcel = $myExcel->string('xlsx');
        $response = array(
            'name' => $fName, //no extention needed
            'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($myExcel) //mime type of used format
        );
        return response()->json($response);
    }

    public function exportPareto(Request $request)
    {
        ini_set('max_execution_time', 3600);

        $branch = $request->get('efcabang');
        $store = $request->get('eftoko');
        $type = $request->get('eftipe');
        $date = $request->get('efhari');

        if ($type === "cnt") {
            $arr = ['branches.name as branch', 'user_products.plu as plu', 'users.store_name as store', 'user_products.description as product', \DB::raw('count(distinct(trx_details.trx_header_id)) as total')];
        } else if ($type === "qty") {
            $arr = ['branches.name as branch', 'user_products.plu as plu', 'users.store_name as store', 'user_products.description as product', \DB::raw('sum(trx_details.qty) as total')];
        } else if ($type === "prc") {
            $arr = ['branches.name as branch', 'user_products.plu as plu', 'users.store_name as store', 'user_products.description as product', \DB::raw('sum(trx_details.price*trx_details.qty) as total')];
        }

        if ($date === "daterange") {
            $startdate = $request->get('startdate') . " 00:00:00";
            $enddate = $request->get('enddate') . " 23:59:59";

            $data = TDBBranch::ConnectToDetail($branch, $store)
                ->whereBetween('trx_headers.trx_date', [$startdate, $enddate])
                ->groupBy('user_products.id')
                ->orderBy('total', 'desc')
                ->get($arr);
        } else {
            $data = TDBBranch::ConnectToDetail($branch, $store)
                ->where('trx_headers.trx_date', 'LIKE', $date)
                ->groupBy('user_products.id')
                ->orderBy('total', 'desc')
                ->get($arr);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'CABANG');
        $sheet->setCellValue('B1', 'TOKO');
        $sheet->setCellValue('C1', 'PLU'); 
        $sheet->setCellValue('D1', 'PRODUK');
        $sheet->setCellValue('E1', 'TOTAL');
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal('center');

        $no = 2;
        foreach ($data as $row) {
            $sheet->setCellValue('A' . $no, $row->branch);
            $sheet->setCellValue('B' . $no, $row->store);
            $sheet->setCellValue('C' . $no, '\''.$row->plu);
            $sheet->setCellValue('D' . $no, $row->product);
            $sheet->setCellValue('E' . $no, $row->total);
            $no++;
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        $sheet->setTitle("pareto");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="pareto.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function exportPb(Request $request)
    {
        $branch = $request->get('efcabang');
        $store = $request->get('eftoko');
        $date = $request->get('efhari');

        if ($date === "daterange") {
            $startdate = $request->get('start');
            $enddate = $request->get('end');

            $data = TDBBranch::ConnectToPb($branch, $store)
                ->whereBetween('pb_headers.po_date', [$startdate, $enddate])
                ->get(['pb_headers.id as id', 'branches.name as branch', 'users.member_code as member_code', 'users.store_name as store', 'pb_headers.po_number as po_no', 'pb_headers.po_date as po_date', 'pb_headers.name as name', 'pb_headers.email as email', 'pb_headers.phone_number as phone', 'pb_headers.address as address', 'pb_headers.qty_order as qtyo', 'pb_headers.qty_fulfilled as qtyf', 'pb_headers.price_order as priceo', 'pb_headers.price_fulfilled as pricef', 'pb_headers.flag_free_delivery as isfree', 'pb_statuses.title as status', 'pb_headers.flag_sent as issent']);
        } else {
            $data = TDBBranch::ConnectToPb($branch, $store)
                ->where('pb_headers.po_date', 'LIKE', $date)
                ->get(['pb_headers.id as id', 'branches.name as branch', 'users.member_code as member_code', 'users.store_name as store', 'pb_headers.po_number as po_no', 'pb_headers.po_date as po_date', 'pb_headers.name as name', 'pb_headers.email as email', 'pb_headers.phone_number as phone', 'pb_headers.address as address', 'pb_headers.qty_order as qtyo', 'pb_headers.qty_fulfilled as qtyf', 'pb_headers.price_order as priceo', 'pb_headers.price_fulfilled as pricef', 'pb_headers.flag_free_delivery as isfree', 'pb_statuses.title as status', 'pb_headers.flag_sent as issent']);
        }

        $mindate = $data->min('po_date');
        $maxdate = $data->max('po_date');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LAPORAN DAN REALISASI PB');
        $sheet->mergeCells("A1:G1");

        $sheet->setCellValue('A2', 'PERIODE ' . $mindate . ' s/d ' . $maxdate);
        $sheet->mergeCells("A2:G2");
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

        $highestRow = 4;
        $no = 1;
        foreach ($data as $row) {
            $sheet->setCellValue('A' . $highestRow, 'No. PO');
            $sheet->setCellValue('A' . ($highestRow + 1), 'Tanggal Pesan');
            $sheet->setCellValue('A' . ($highestRow + 2), 'Kode Member');
            $sheet->setCellValue('A' . ($highestRow + 3), 'Nama Toko');
            $sheet->setCellValue('A' . ($highestRow + 4), 'Pemesan');
            $sheet->setCellValue('A' . ($highestRow + 5), 'Email');
            $sheet->setCellValue('A' . ($highestRow + 6), 'Alamat');
            $sheet->setCellValue('A' . ($highestRow + 7), 'No. Telp');
            $sheet->setCellValue('A' . ($highestRow + 8), 'Status');
//            $sheet->setCellValue('A'.($highestRow+9), 'Gratis Biaya Pengiriman');
//            $sheet->setCellValue('A'.($highestRow+10), 'Terkirim');
            $sheet->getStyle("A" . $highestRow . ":A" . ($highestRow + 10))->getFont()->setBold(true);

            if ($row->isfree == 1) {
                $isfree = 'Gratis';
            } else {
                $isfree = 'Tidak';
            }

            if ($row->issent == 1) {
                $issent = 'Terkirim';
            } else {
                $issent = 'Belum Terkirim';
            }

            $sheet->setCellValue('B' . $highestRow, $row->po_no);
            $sheet->setCellValue('B' . ($highestRow + 1), $row->po_date);
            $sheet->setCellValue('B' . ($highestRow + 2), $row->member_code);
            $sheet->setCellValue('B' . ($highestRow + 3), $row->store);
            $sheet->setCellValue('B' . ($highestRow + 4), $row->name);
            $sheet->setCellValue('B' . ($highestRow + 5), $row->email);
            $sheet->setCellValue('B' . ($highestRow + 6), $row->address);
            $sheet->setCellValue('B' . ($highestRow + 7), $row->phone);
            $sheet->setCellValue('B' . ($highestRow + 8), $row->status);
//            $sheet->setCellValue('B'.($highestRow+9), $isfree);
//            $sheet->setCellValue('B'.($highestRow+10), $issent);

            $highestRow = $sheet->getHighestRow() + 1;
            $sheet->setCellValue('A' . $highestRow, 'No.');
            $sheet->setCellValue('B' . $highestRow, 'PLU');
            $sheet->setCellValue('C' . $highestRow, 'Nama Produk');
            $sheet->setCellValue('D' . $highestRow, 'Jumlah Pesanan');
            $sheet->setCellValue('E' . $highestRow, 'Jumlah Realisasi');
            $sheet->setCellValue('F' . $highestRow, 'SL QTY (%)');
            $sheet->setCellValue('G' . $highestRow, 'Harga Pesanan');
            $sheet->setCellValue('H' . $highestRow, 'Harga Realisasi');
            $sheet->setCellValue('I' . $highestRow, 'SL Rph (%)');

            $sheet->getStyle("A" . $highestRow . ":G" . $highestRow)->getFont()->setBold(true);
            $highestRow = $sheet->getHighestRow() + 1;

            $details = TDBPbDetail::with('products')
                ->where('pb_header_id', '=', $row->id)
                ->get();
            /*
             * products->plu
                products->description
                qty_order
                qty_fulfilled
                price_order
                price_fulfilled
             * */
            $jumlah_pesanan = 0;
            $jumlah_realisasi = 0;
            $harga_pesanan = 0;
            $harga_realisasi = 0;
            foreach ($details as $detail) {
                $sheet->setCellValue('A' . $highestRow, $no);
                $sheet->getStyle('A' . $highestRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('B' . $highestRow, $detail->products->plu);
                $sheet->getStyle('B' . $highestRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('C' . $highestRow, $detail->products->description);
                $sheet->setCellValue('D' . $highestRow, $detail->qty_order);
                $sheet->setCellValue('E' . $highestRow, $detail->qty_fulfilled);
                $sheet->setCellValue('F' . $highestRow, number_format((($detail->qty_fulfilled / $detail->qty_order) * 100), 2) . '%');

                $sheet->setCellValue('G' . $highestRow, ($detail->price_order * $detail->qty_order));
                $sheet->setCellValue('H' . $highestRow, ($detail->price_fulfilled * $detail->qty_fulfilled));
                $sheet->setCellValue('I' . $highestRow, number_format((($detail->price_fulfilled / $detail->price_order) * 100), 2) . '%');
                $highestRow++;
                $no++;
                $harga_pesanan += $detail->price_order;
                $harga_realisasi += $detail->price_fulfilled;
                $jumlah_pesanan += $detail->qty_order;
                $jumlah_realisasi += $detail->qty_fulfilled;
            }
            //TAMBAHAN di FOOTER
//            $sheet->setCellValue('A'.$highestRow, $no);
            $sheet->getStyle('A' . $highestRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
//            $sheet->setCellValue('B'.$highestRow, $detail->products->plu);
            $sheet->getStyle('B' . $highestRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            $sheet->setCellValue('C' . $highestRow, "SUMMARY");
            $sheet->setCellValue('D' . $highestRow, $jumlah_pesanan);
            $sheet->setCellValue('E' . $highestRow, $jumlah_realisasi);
            $sheet->setCellValue('F' . $highestRow, (($jumlah_realisasi / $jumlah_pesanan) * 100) . '%');
            $sheet->setCellValue('G' . $highestRow, $harga_pesanan);
            $sheet->setCellValue('H' . $highestRow, $harga_realisasi);
            $sheet->setCellValue('I' . $highestRow, (($harga_realisasi / $harga_pesanan) * 100) . '%');
            $highestRow++;

            $highestRow = $sheet->getHighestRow() + 2;

            $no = 1;
            $harga_pesanan += 0;
            $harga_realisasi += 0;
            $jumlah_pesanan += 0;
            $jumlah_realisasi += 0;
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        $sheet->setTitle("pb");
//        dd('test2923');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="pb.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function exportArchive(Request $request)
    {
        $branch = $request->get('branch');
        $store = $request->get('store');

        $data = TDBBranch::ConnectToUserProducts($branch, $store)
            ->whereNotNull('user_products.deleted_at')
            ->get(['branches.name as branch', 'users.store_name as store', 'user_products.plu as plu', 'user_products.description as desc', 'user_products.unit as unit', 'user_products.fraction as frac', 'user_products.deleted_at as date']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LAPORAN ARSIP BARANG');
        $sheet->mergeCells("A1:F1");
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $highestRow = 3;
        $no = 1;
        foreach ($data->unique('store') as $row) {
            $storetemp = $row->store;
            $sheet->setCellValue('A' . $highestRow, 'Cabang');
            $sheet->setCellValue('A' . ($highestRow + 1), 'Toko');
            $sheet->setCellValue('B' . $highestRow, $row->branch);
            $sheet->setCellValue('B' . ($highestRow + 1), $row->store);
            $sheet->getStyle('A' . $highestRow . ':A' . ($highestRow + 1))->getFont()->setBold(true);

            $sheet->setCellValue('A' . ($highestRow + 2), 'No.');
            $sheet->setCellValue('B' . ($highestRow + 2), 'PLU');
            $sheet->setCellValue('C' . ($highestRow + 2), 'Deskripsi');
            $sheet->setCellValue('D' . ($highestRow + 2), 'Satuan');
            $sheet->setCellValue('E' . ($highestRow + 2), 'Fraksi');
            $sheet->setCellValue('F' . ($highestRow + 2), 'Tanggal Diarsipkan');
            $sheet->getStyle('A' . ($highestRow + 2) . ':F' . ($highestRow + 2))->getFont()->setBold(true);

            $highestRow = $sheet->getHighestRow() + 1;
            foreach ($data as $detail) {
                if ($detail->store === $storetemp) {
                    $sheet->setCellValue('A' . $highestRow, $no);
                    $sheet->setCellValue('B' . $highestRow, $detail->plu);
                    $sheet->getStyle('B' . $highestRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    $sheet->setCellValue('C' . $highestRow, $detail->desc);
                    $sheet->setCellValue('D' . $highestRow, $detail->unit);
                    $sheet->setCellValue('E' . $highestRow, $detail->frac);
                    $sheet->setCellValue('F' . $highestRow, $detail->date);
                    $no++;
                    $highestRow++;
                }
            }
            $highestRow = $sheet->getHighestRow() + 2;
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        $sheet->setTitle("arsip");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="arsip.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function exportStock(Request $request)
    {
        //prep memory for large amount of data
        ini_set('memory_limit', '218M');
        $branch = $request->get('efcabang');
        $store = $request->get('eftoko');

        $data = TDBBranch::ConnectUserProductToDiv($branch, $store)
            ->whereNull('user_products.deleted_at')
            ->get(['categories.category as category', 'user_products.plu as plu', 'user_products.id as pid', 'user_products.description as desc',
                \DB::raw('(select sum(trx_details.qty)/30
                    from users
                    join user_products on users.id = user_products.user_id
                    join trx_details on user_products.id = trx_details.user_product_id
                    join trx_headers on trx_headers.id = trx_details.trx_header_id
                    where users.id = ' . $store . ' and user_products.id = pid and date(trx_headers.trx_date) between (curdate() - interval 30 day) and curdate()) as avgsales'),
                'user_products.stock as stock', 'user_products.min_stock as minq', 'user_products.max_stock as maxq', 'user_products.deleted_at as date', 'user_products.price as price',
                'user_products.cost as cost']);

        $trxDetail = TDBTrxDetail::join('user_products', 'trx_details.user_product_id', '=', 'user_products.id')
            ->select(DB::raw('CONCAT(SUBSTR(user_products.plu, 1, 6), 0) as plu'), 
                DB::raw('SUM(trx_details.qty) as qty_sold')
                )
            ->where('user_products.user_id', '=', $store)
            ->groupBy('user_products.id')
            ->get();
        $collectDetail = collect($trxDetail);
        foreach($data as $i => $value) {
            $data[$i]['qty_sold'] = $collectDetail->where('plu', $value->plu)->first()->qty_sold;
        }

        $div = [];
        $dep = [];
        $kat = [];
        foreach ($data as $row => $item) {
            $temp = Margin::join('divisi', 'master_margin.div', '=', 'divisi.DIV_KODEDIVISI')
                ->join('department', function ($join) {
                    $join->on('master_margin.dep', '=', 'department.DEP_KODEDEPARTEMENT');
                    $join->on('divisi.DIV_KODEDIVISI', '=', 'department.DEP_KODEDIVISI');
                })
                ->join('category', function ($join) {
                    $join->on('master_margin.kat', '=', 'category.KAT_KODEKATEGORI');
                    $join->on('department.DEP_KODEDEPARTEMENT', '=', 'category.KAT_KODEDEPARTEMENT');
                })
                ->where('master_margin.id', '=', $item->category)
                ->get(['divisi.DIV_KODEDIVISI as div', 'department.DEP_KODEDEPARTEMENT as dep', 'category.KAT_KODEKATEGORI as kat']);
            $div[] = $temp[0]->div;
            $dep[] = $temp[0]->dep;
            $kat[] = $temp[0]->kat;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LAPORAN STOK BARANG');
        $sheet->mergeCells("A1:F1");
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $highestRow = 3;
        $no = 0;
        foreach ($data->unique('store') as $row) {
            $storetemp = $row->store;
            $sheet->setCellValue('A' . $highestRow, 'Cabang');
            $sheet->setCellValue('A' . ($highestRow + 1), 'Toko');
            $sheet->setCellValue('B' . $highestRow, $row->branch);
            $sheet->setCellValue('B' . ($highestRow + 1), $row->store);
            $sheet->getStyle('A' . $highestRow . ':A' . ($highestRow + 1))->getFont()->setBold(true);

            $sheet->setCellValue('A' . ($highestRow + 2), 'DIV');
            $sheet->setCellValue('B' . ($highestRow + 2), 'DEPT');
            $sheet->setCellValue('C' . ($highestRow + 2), 'KAT');
            $sheet->setCellValue('D' . ($highestRow + 2), 'PLU');
            $sheet->setCellValue('E' . ($highestRow + 2), 'DESKRIPSI');
            $sheet->setCellValue('F' . ($highestRow + 2), 'STOK QTY');
            $sheet->setCellValue('G' . ($highestRow + 2), 'AVG SALES');
            $sheet->setCellValue('H' . ($highestRow + 2), 'QTY TERJUAL');
            $sheet->setCellValue('I' . ($highestRow + 2), 'MIN QTY');
            $sheet->setCellValue('J' . ($highestRow + 2), 'MAX QTY');
            $sheet->setCellValue('K' . ($highestRow + 2), 'HRG POKOK');
            $sheet->setCellValue('L' . ($highestRow + 2), 'HRG JUAL');
            $sheet->getStyle('A' . ($highestRow + 2) . ':J' . ($highestRow + 2))->getFont()->setBold(true);

            $highestRow = $sheet->getHighestRow() + 1;
            foreach ($data as $detail) {
                if ($detail->store === $storetemp) {
                    $sheet->setCellValue('A' . $highestRow, $div[$no]);
                    $sheet->setCellValue('B' . $highestRow, $dep[$no]);
                    $sheet->getStyle('B' . $highestRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    $sheet->setCellValue('C' . $highestRow, $kat[$no]);
                    $sheet->getStyle('C' . $highestRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    $sheet->setCellValue('D' . $highestRow, $detail->plu);
                    $sheet->getStyle('D' . $highestRow)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    $sheet->setCellValue('E' . $highestRow, $detail->desc);
                    $sheet->setCellValue('F' . $highestRow, $detail->stock);
                    if ($detail->avgsales != null) {
                        $sheet->setCellValue('G' . $highestRow, round($detail->avgsales, 2));
                    } else {
                        $sheet->setCellValue('G' . $highestRow, '0');
                    }
                    $sheet->setCellValue('H' . $highestRow, $detail->qty_sold);
                    $sheet->setCellValue('I' . $highestRow, $detail->minq);
                    $sheet->setCellValue('J' . $highestRow, $detail->maxq);
                    $sheet->setCellValue('K' . $highestRow, $detail->cost);
                    $sheet->setCellValue('L' . $highestRow, $detail->price);
                    $no++;
                    $highestRow++;
                }
            }
            $highestRow = $sheet->getHighestRow() + 2;
        }

        $lastColumn = 'K';
        for ($currentColumn = 'A'; $currentColumn != $lastColumn; $currentColumn++) {
            $sheet->getColumnDimension($currentColumn)->setAutoSize(true);
        }
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        $sheet->setTitle("stok");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="stok.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function deletePlu(Request $Request)
    {
        $toBeDeleted = MasterPlu::find($Request->get('id'));
        $toBeDeleted->delete();

        return redirect('listmember');
    }

    public function getDevAccessToken(Request $request)
    {
        $ch = curl_init(config('urls.basedevapi') . config('urls.devloginapi'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Connection: keep-alive'
        ));
        $array = ['client_id' => 1, 'client_secret' => 'VPbpBVpO8wnIlhdTP7s6OidAqzyzltCQqyhq1MN6'];

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($array));

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    public function getDevAllVersion(Request $request)
    {
        $ch = curl_init(config('urls.basedevapi') . config('urls.devgetallapi'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: ' . $request->get('auth'),
            'Connection: keep-alive'
        ));

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    public function devUpdateVersion(Request $request)
    {
        $ch = curl_init(config('urls.basedevapi') . config('urls.devupdateapi'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: ' . $request->get('auth'),
            'Connection: keep-alive'
        ));
        $array = ['server_id' => $request->get('id'), 'version_name' => $request->get('ver'), 'description' => $request->get('desc')];

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($array));

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    public function devInsertVersion(Request $request)
    {
        $ch = curl_init(config('urls.basedevapi') . config('urls.devinsertapi'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: ' . $request->get('auth'),
            'Connection: keep-alive'
        ));
        $array = ['version_name' => $request->get('ver'), 'description' => $request->get('desc')];

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($array));

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    public function getMemberPLU(Request $Request)
    {

        if ($Request->get('tipemember') == "x") {
            $tipe = "%";
        } else {
            $tipe = $Request->get('tipemember');
        }

        if ($Request->get('tipecabang') == "x") {
            $cab = "%";
        } else {
            $cab = $Request->get('tipecabang');
        }

//        dd($tipe, $cab);
        $MemberPLUTmiAssoc = MasterPlu::getPlu($tipe, $cab);

//        dd($tipe, $cab, $MemberPLUTmiAssoc);
        foreach ($MemberPLUTmiAssoc as $row) {
//            $row->aksi  = '<button id="btn_edit_plu" type="button" class="btn btn-info flat" style="width:70px;margin-bottom: 5px;" value="' . $row->id . '"> Edit </button>';
        }

        return \Datatables::of($MemberPLUTmiAssoc)->make(true);
    }

    public function getPluAjax(Request $Request)
    {

        $id = $Request->id;

        $data = \DB::table('master_plu')
            ->leftJoin('master_toko', 'master_toko.id', '=', 'master_plu.tmi_id')
            ->Where('master_plu.id', $id)
            ->Get();
        return $data;
    }

    public function PostMasterMargin(Request $request)
    {

        $splu = $request->splu;
        $cabpecah = explode(',', $splu);

        $branch = \DB::table('branches')
            ->Selectraw('kode_igr')
            ->whereNotIn('kode_igr', $cabpecah)
            ->get();

        $date = Carbon::Now();

        $newMrg = new Margin();
        $newMrg->kode_tmi = $request->tipe;
        $newMrg->kode_mrg = $request->tipe . $request->dep . $request->kat;
        if (count($cabpecah) == 21) {
            $newMrg->flag_cab = 1;
        } else {
            $newMrg->flag_cab = 0;
        }

        $newMrg->div = $request->div;
        $newMrg->dep = $request->dep;
        $newMrg->kat = $request->kat;
        $newMrg->margin_min = $request->min;
        $newMrg->margin_max = $request->max;
        $newMrg->margin_saran = $request->saran;
        $newMrg->created_at = Carbon::now();
        $newMrg->modify_at = Carbon::now();
        $newMrg->created_by = 'ADM';
        $newMrg->modify_by = 'ADM';
        $newMrg->save();

        foreach ($cabpecah as $sp) {
            $newMrgDtl = new MarginDetail();
            $newMrgDtl->margin_id = $newMrg->id;
            $newMrgDtl->kode_igr = $sp;
            $newMrgDtl->created_at = $date;
            $newMrgDtl->updated_at = $date;
            $newMrgDtl->deleted_at = null;
            $newMrgDtl->modify_by = 'ADM';;
            $newMrgDtl->save();
        }

        return "true";
    }

    public function getSearchPlu(Request $Request)
    {

        $kodeplu = $Request->id;
        $kodeigr = $Request->cab;


        $prodmastsearch = Margin::Distinct()
            ->Join('products', function ($join) {
                $join->on('master_margin.div', '=', 'products.kode_division');
                $join->on('master_margin.dep', '=', 'products.kode_department');
                $join->on('master_margin.kat', '=', 'products.kode_category');
            })
            ->leftJoin('category', function ($join) {
                $join->on('products.kode_department', '=', 'category.kat_kodedepartement');
                $join->on('products.kode_category', '=', 'category.kat_kodekategori');
            })
            ->Where('prdcd', 'LIKE', '%' . $kodeplu . '%')
            ->groupby('prdcd')
            ->get();


        return $prodmastsearch;
    }

    public function getCabPlu(Request $Request)
    {

        $kodeplu = $Request->id;
//        $kodeigr = $Request->cab;

        $prodmastcab = \DB::connection('webmm')->table('products')
            ->Select('name')
            ->join('branches', 'products.kode_igr', '=', 'branches.kode_igr')
//            ->Where('prdcd', $kodeplu)
            ->Where('prdcd', 'LIKE', '%' . $kodeplu . '%')
//            ->groupby('prdcd')
            ->get();

        return $prodmastcab;
    }

    public function EditPluAjax(Request $request)
    {
        $id = $request->id;

        $data = MasterPlu::find($id);
        $data->hrg_jualigr = $request->hrg_jualigr;
        $data->save();

        return "true";
    }

    public function AktivasiMember(Request $request)
    {
        $id = $request->tipeid;

        $data = MasterToko::find($id);

        $data->tmi_id = $request->tipetmi;
        $data->status = 1;
        $data->save();

        return "true";
    }

    public function AddPluTmi(request $request)
    {
        ini_set('memory_limit', '-1');

        ini_set('max_execution_time', 300);

        $cekisi = "";
        $branchFormat = "";

        $date = Carbon::Now();

        $path = $request->file('import_file')->getRealPath();


        $branch = \DB::table('branches')
            ->get();

        foreach ($branch as $index => $row) {
            $branchFormat .= "<option style='font-size: 12px;' value='" . $row->kode_igr . "'>" . $row->kode_igr . " --> " . $row->name . "</option>";
        }

        $plu = Excel::load($path)->calculate()->toArray();


        \DB::beginTransaction();
        try {
//        if (!empty($plu)) {
            $tempErrorFiles = array();
            foreach ($plu as $key => $value) {
//                    if ($value['kodemgn'] != null || $value['kodemgn'] != "") {
//                        $kode = \DB::table('margin_details')
//                            ->Selectraw('kode_mrg, kode_igr')
//                            ->Join('master_margin', 'margin_details.margin_id', '=', 'master_margin.id')
//                            ->Where('kode_mrg', $value['kodemgn'])
//                            ->WhereNull('deleted_at')
//                            ->get();
//                    }
//                    if($value['kodemgn'] != null || $value['kodemgn'] != ""){
                $PLU = substr($value['plu'], 0, 6);

                $product = \DB::table('products')
                    ->Selectraw('prdcd, kode_igr, hrg_jual, frac, unit, long_description, kode_tag, min_jual')
                    ->Where('prdcd', 'LIKE', '' . $PLU . '%')
                    ->get();

                $countplu = \DB::table('master_plu')
                    ->Where('kodeplu', 'LIKE', '' . $PLU . '%')
                    ->Where('mrg_id', $value['kodemgn'])
                    ->get();

                $countmrg = \DB::table('master_margin')
                    ->Where('kode_mrg', $value['kodemgn'])
                    ->get();

                dd($countmrg);

//                    dd(count($countplu));

                if ($product != null && count($countplu) == 0 && count($countmrg) > 0) {
                    foreach ($product as $key => $row) {
                        $plutmi = new MasterPlu;
                        $plutmi->kodeplu = $row->prdcd;
                        $plutmi->kode_igr = $row->kode_igr;
                        $plutmi->hrg_jual = $row->hrg_jual;
                        $plutmi->display = $value['display'];
                        $plutmi->unit_tmi = $value['satjualtmi'];
                        $plutmi->mrg_id = $value['kodemgn'];
                        $plutmi->frac_tmi = $value['konversi'] * $row->frac;
                        $plutmi->qty_min = $value['minqty'];
                        $plutmi->qty_max = $value['maxqty'];
                        $plutmi->min_dis = $value['mindis'];
                        $plutmi->min_jual = $row->min_jual;
                        $plutmi->frac_igr = $row->frac;
                        $plutmi->unit_igr = $row->unit;
                        $plutmi->tag = $row->kode_tag;
                        $plutmi->long_desc = $row->long_description;
//                            $plutmi->barcode = $row->brc_barcode;
                        $plutmi->save();
                    }

                } else {
                    array_push($tempErrorFiles, $value['plu']);
                }

            }

//            dd($tempErrorFiles);

            if (count($tempErrorFiles) > 0) {
                $message = '<br/>Kode PLU :<br/>';
                foreach ($tempErrorFiles as $file)
                    $message = $message . ' ' . $file . ', ';
                $message = $message . '<br/> gagal di-Upload! Format atau Nama Kode Sudah Ada atau Tidak Terdaftar';
                $request->session()->flash('error_message', $message);
            } else {
                $request->session()->flash('success_message', 'Data berhasil di-Upload!');
            }

            \DB::commit();
            return view('admin.uploadplu')->with('Tes', 'Berhasil Upload');

        } catch (Exception $ex) {
            \DB::rollBack();
            return view('admin.uploadplu')->with('nodata', 'Gagal Upload File');
        }

    }

    public function UploadPlu(request $request)
    {
        ini_set('memory_limit', '-1');

        ini_set('max_execution_time', 300);

        $cekisi = "";
        $branchFormat = "";

        $date = Carbon::Now();

        $cab = $request->cabang;

        $path = $request->file('import_file')->getRealPath();

        $branch = \DB::table('branches')
            ->get();

        foreach ($branch as $index => $row) {
            $branchFormat .= "<option style='font-size: 12px;' value='" . $row->kode_igr . "'>" . $row->kode_igr . " --> " . $row->name . "</option>";
        }

        $plu = Excel::load($path)->calculate()->toArray();

        if ($plu == null) {
            $request->session()->flash('error_message', 'File tidak Valid!');
            return view('admin.uploadplu')->with('err', 'File tidak Valid!')->with('branch', $branchFormat);
        }

        \DB::beginTransaction();
        try {

            $tempErrorFiles = array();
//            dd($plu);
            foreach ($plu as $key => $value) {
                $PLU = substr($value['plu'], 0, 6);
//                dd($PLU, $value);
                if ($PLU == null) {
//                    dd($key, $value);
                    continue;
                }

                $product = \DB::table('products')
                    ->Selectraw('prdcd, kode_igr, hrg_jual, frac, unit, long_description, kode_tag, min_jual')
                    ->Where('prdcd', 'LIKE', '' . $PLU . '%')
                    ->Where('kode_igr', $cab)
                    ->get();

                //0000400 ->target
//                if($PLU == '0391920'){
//                    dd('1');
//                } else if($PLU == '039192'){
//                    dd($product);
//                }
//                dd('test',$PLU, $product, $cab);

                if ($product != null) {
                    foreach ($product as $key => $row) {
                        //todo lanjut bsk, cabang solo masih masalah
//                        if($row->prdcd == )
                        $plutmi = MasterPlu::firstOrNew(['mrg_id' => $value['kodemgn'], 'kodeplu' => $row->prdcd, 'kode_igr' => $row->kode_igr]);
//                        if($PLU == '000039'){
//                            dd($PLU, $product, 'vanQ', $plutmi);
//                        }
//                        $plutmi->kodeplu = $row->prdcd;
                        $plutmi->kode_igr = $row->kode_igr;
                        $plutmi->hrg_jual = $row->hrg_jual;
                        $plutmi->display = $value['display'];
                        $plutmi->unit_tmi = $value['satjualtmi'];
                        $plutmi->frac_tmi = $value['konversi'] * $row->frac;
                        $plutmi->qty_min = $value['minqty'];
                        $plutmi->qty_max = $value['maxqty'];
                        $plutmi->min_dis = $value['mindis'];
                        $plutmi->min_jual = $row->min_jual;
                        $plutmi->frac_igr = $row->frac;
                        $plutmi->unit_igr = $row->unit;
                        $plutmi->tag = $row->kode_tag;
                        $plutmi->long_desc = $row->long_description;
                        $plutmi->created_at = $date;
                        $plutmi->updated_at = $date;
                        try {
                            $plutmi->save();
                        } catch (\Exception $exception) {
                            dd($exception, $row);
                        }
                    }

                } else {
                    array_push($tempErrorFiles, $value['plu']);
                }

            }

            if (count($tempErrorFiles) > 0) {
                foreach ($tempErrorFiles as $file) {
                    \DB::table('plu_tolakan')->insert(
                        ['plu' => $file, 'created_at' => $date, 'updated_at' => $date]
                    );
                }
//                $message = '<br/>Kode PLU :<br/>';
//                foreach ($tempErrorFiles as $file)
//                    $message = $message . ' ' . $file . ', ';
//                $message = $message . '<br/> gagal di-Upload! Format atau Nama Kode Sudah Ada atau Tidak Terdaftar';
//                $request->session()->flash('error_message', $message);
            } else {
//                dd('hello vanworld');
                $request->session()->flash('success_message', 'Data berhasil di-Upload!');
            }



            \DB::commit();
            $request->session()->flash('success_message', 'Data berhasil di-Upload!');
            return view('admin.uploadplu')->with('suc', 'Berhasil Upload')->with('branch', $branchFormat);

        } catch (Exception $ex) {
            \DB::rollBack();
            return view('admin.uploadplu')->with('nodata', 'Gagal Upload File')->with('branch', $branchFormat);
        }

    }

    public function PostUploadMargin(request $request)
    {
        $cekisi = "";

        $date = Carbon::Now();

        $path = $request->file('import_file')->getRealPath();

        $margin = Excel::load($path)->calculate()->toArray();

        $branch = \DB::table('branches')
            ->get();

        $branchFormat = "";


        foreach ($branch as $index => $row) {
            $branchFormat .= "<option style='font-size: 12px;' value='" . $row->kode_igr . "'>" . $row->kode_igr . " --> " . $row->name . "</option>";
        }
        $cab = $request->cabang;

        if ($margin == null) {
            $request->session()->flash('error_message', 'File tidak Valid!');
            return view('admin.uploadmargin')->with('suc', 'File tidak Valid!')->with('branch', $branchFormat);
        }

        \DB::beginTransaction();
        try {

            foreach ($margin as $key => $value) {
                $newmaster = Margin::firstOrNew(['kode_tmi' => $value['kodetmi'], 'kode_mrg' => $value['kodemgn'], 'kode_igr' => $cab]);
                $newmaster->margin_saran = $value['saran'];
                //                        $newmaster->margin_max = $value['max'];
                $newmaster->margin_min = $value['min'];
                $newmaster->div = $value['div'];
                $newmaster->dep = $value['dept'];
                $newmaster->kat = $value['kat'];
                $newmaster->kode_igr = $cab;
                $newmaster->created_by = 'ADM';
                $newmaster->created_at = $date;
                $newmaster->modify_at = $date;
                $newmaster->modify_by = 'ADM';
                $newmaster->save();

            }

            \DB::commit();
            $request->session()->flash('success_message', 'Data berhasil di-Upload!');
            return view('admin.uploadmargin')->with('suc', 'Berhasil Upload')->with('branch', $branchFormat);

        } catch (Exception $ex) {
            \DB::rollBack();
            return view('admin.uploadmargin')->with('nodata', 'Gagal Upload File')->with('branch', $branchFormat);
        }


    }

    public function PostUploadMaxHarga(request $request)
    {
        $cekisi = "";

        $date = Carbon::Now();

        $path = $request->file('import_file')->getRealPath();
        $margin = Excel::load($path)->calculate()->toArray();

//        dd($margin[0]);
        $branch_code = $request->cabang;

        $branch = \DB::table('branches')
            ->get();

        //kode_igr, kode_mrg, kode_tmi
//        protected $fillable = ['kode_tmi', 'kode_mrg', 'flag_cab', 'div', 'dep', 'kat', 'margin_min', 'margin_saran', 'margin_max'];
//        dd($margin[0]);
        DB::beginTransation();
//        dd($branch_code, $margin);
        try {
            foreach ($margin as $m) {
//                $test = Margin::where([
//                    'kode_tmi'=>$m['kodetmi'],
//                    'kode_mrg'=>$m['kodemgn'],
//                    'kode_igr'=>$branch_code
//                ])->first();
//                dd($test);
//                dd($m, $m['kodetmi']);
                $mgn = Margin::updateOrCreate([
                    'kode_tmi' => $m['kodetmi'],
                    'kode_mrg' => $m['kodemgn'],
                    'kode_igr' => $branch_code,
//                    'div'=>$m->div,
//                    'dep'=>$m->dept,
//                    'kat'=>$m->kat,
                ], [
                    //                'margin_min'=>$m->min,
                    'margin_max' => $m['max'],
                    //                'margin_saran'=>$m->saran,
                ]);
            }
            DB::commit();
            $request->session()->flash('success_message', 'Data berhasil di-Upload!');
            return redirect()->back()->with('suc', 'Berhasil Upload');

        } catch (Exception $ex) {
            DB::rollBack();
            return view('admin.uploadmaxharga')->with('nodata', 'Gagal Upload File');
        }


        dd($margin);

        $branchFormat = "";


        foreach ($branch as $index => $row) {
            $branchFormat .= "<option style='font-size: 12px;' value='" . $row->kode_igr . "'>" . $row->kode_igr . " --> " . $row->name . "</option>";
        }
        $cab = $request->cabang;

        if ($margin == null) {
            $request->session()->flash('error_message', 'File tidak Valid!');
            return view('admin.uploadmargin')->with('suc', 'File tidak Valid!')->with('branch', $branchFormat);
        }

        \DB::beginTransaction();
        try {

            foreach ($margin as $key => $value) {
                $newmaster = Margin::firstOrNew(['kode_tmi' => $value['kodetmi'], 'kode_mrg' => $value['kodemgn'], 'kode_igr' => $cab]);
                $newmaster->margin_saran = $value['saran'];
                //                        $newmaster->margin_max = $value['max'];
                $newmaster->margin_min = $value['min'];
                $newmaster->div = $value['div'];
                $newmaster->dep = $value['dept'];
                $newmaster->kat = $value['kat'];
                $newmaster->kode_igr = $cab;
                $newmaster->created_by = 'ADM';
                $newmaster->created_at = $date;
                $newmaster->modify_at = $date;
                $newmaster->modify_by = 'ADM';
                $newmaster->save();

            }

            \DB::commit();
            $request->session()->flash('success_message', 'Data berhasil di-Upload!');
            return view('admin.uploadmargin')->with('suc', 'Berhasil Upload')->with('branch', $branchFormat);

        } catch (Exception $ex) {
            \DB::rollBack();
            return view('admin.uploadmargin')->with('nodata', 'Gagal Upload File')->with('branch', $branchFormat);
        }


    }

    public function getMarginAjax(Request $request)
    {
        $id = $request->id;

        $data1 = Margin::SelectRaw('flag_cab, tipe_tmi.nama as tipetmi, margin_min, margin_max, margin_saran, kat_namakategori, master_margin.id as idmrg')
            ->leftJoin('tipe_tmi', 'master_margin.kode_tmi', '=', 'tipe_tmi.kode_tmi')
//            ->leftJoin('branches', 'master_margin.kode_igr', '=', 'branches.kode_igr')
            ->leftJoin('divisi', 'div_kodedivisi', '=', 'div')
//            ->Join('department', 'dep_kodedepartement', '=', 'dep')
            ->leftJoin('department', function ($join) {
                $join->on('master_margin.dep', '=', 'department.dep_kodedepartement');
                $join->on('divisi.div_kodedivisi', '=', 'department.dep_kodedivisi');
            })
            ->leftJoin('category', function ($join) {
                $join->on('master_margin.kat', '=', 'category.kat_kodekategori');
                $join->on('department.dep_kodedepartement', '=', 'category.kat_kodedepartement');
            })
            ->Where('master_margin.id', $id)
            ->OrderBy('master_margin.id', 'DESC')
//            ->Join('category', 'kat_kodekategori', '=', 'kat')
            ->get();

        $data2 = MarginDetail::Selectraw('margin_id, kode_igr')
            ->Where('margin_id', $id)
            ->WhereNull('deleted_at')
            ->get();

//        return $data1;

        return $data1 . '!@#$' . $data2;
    }

    public function EditMargin(Request $request)
    {
        $id = $request->id;
        $splu = $request->splu;
        $cabpecah = explode(',', $splu);

//        $cekisibranch = MarginDetail::Selectraw('margin_id, kode_igr')
//            ->WhereNotIn('margin_id',$cabpecah)
//            ->get();

        $cekisinotbranch = \DB::table('margin_details')
            ->Selectraw('kode_igr, margin_id')
            ->whereNotIn('kode_igr', $cabpecah)
            ->Where('margin_id', $id)
            ->get();

        $cekisibranch = \DB::table('margin_details')
            ->Selectraw('kode_igr, margin_id')
            ->whereIn('kode_igr', $cabpecah)
            ->Where('margin_id', $id)
            ->get();


        $date = Carbon::Now();


        if (count($request->splu) > 0) {

            foreach ($cekisinotbranch as $sp) {
                \DB::table('margin_details')
                    ->where('margin_id', $sp->margin_id)
                    ->where('kode_igr', $sp->kode_igr)
                    ->update(['deleted_at' => $date]);
            }

            foreach ($cekisibranch as $sp) {
                \DB::table('margin_details')
                    ->where('margin_id', $sp->margin_id)
                    ->where('kode_igr', $sp->kode_igr)
                    ->update(['deleted_at' => null]);
            }

            $data = Margin::find($id);
            $data->margin_min = $request->minx;
            $data->margin_max = $request->maxx;
            $data->margin_saran = $request->saranx;
            if (count($cabpecah) < 21) {
                $data->flag_cab = 0;
            } else {
                $data->flag_cab = 1;
            }
            $data->save();

        } else {

            $data = Margin::find($id);
            $data->margin_min = $request->minx;
            $data->margin_max = $request->maxx;
            $data->margin_saran = $request->saranx;
            $data->save();
        }

        return "true";
    }

    public function getUploadMaxHarga()
    {
        $user = Auth::user();
        $branch = TDBBranch::get();
        return view('admin.uploadmaxharga', ['branch_data' => $branch]);
        
    }

    public function rounding($input)
    {
        $input = round($input);
        $ratusan = substr($input, -3);
        if ($ratusan <= 500) {
            $output = $input + (500 - $ratusan);
        } else {
            $output = $input + (1000 - $ratusan);
        }
        return $output;
    }

    public function convertIntToColumnExcel($position)
    {
        $position--;
        $a = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        ];

//        dd($position);
        if ($position >= 26) {
//            do{
//                for ($i=0; $i<$position; $position++){
//                    $a
//                }
//            }while();
            for ($i = 0; $i < 26; $i++) {
                for ($j = 0; $j < 26; $j++) {
                    if ($position == 0) {
                        return $a[$i - 1] . $a[$j];
                    }
                    $position--;
                }
            }
        } else {
            return $a[$position];
        }
    }

    public function getCreditApprovalDoc($credit_number_encrypted){
        
        // $pdf = PDF::loadView('pdf.users', ['users' => $users]);
        // return $pdf->setPaper('a4')->stream();

        // buat test : TMI-C-K/2021/02/15-J28194-001
        $helper = new Helper();
        $credit_number_decrypted = $helper->decryptString($credit_number_encrypted);
        
        if($credit_number_decrypted == false){
            return 'Nomor Pengajuan TIDAK VALID!';
        }
        $userCredit = TDBUserCredit::join('users', 'user_credits.user_id', '=', 'users.id')
                    ->join('branches', 'users.branch_id', '=', 'branches.id')
                    ->join('tmi_types', 'users.tmi_type_id', '=', 'tmi_types.id')
                    ->where(['user_credits.credit_number'=>$credit_number_decrypted])
                    ->select('branches.name as branch_name', 'users.name as username', 'users.member_code', 'users.address',
                        'users.phone_number', 'user_credits.npwp_number', 'user_credits.pkp', 'tmi_types.description as tmi_type',
                        'user_credits.credit_limit', 'user_credits.tenor', DB::raw('DATE(user_credits.last_order_period) as last_order_period'),
                        'user_credits.end_period', 'user_credits.grace_period', 'user_credits.nik', 'users.store_name')
                    ->first();
        if(empty($userCredit)){
            return 'Nomor Pengajuan TIDAK DITEMUKAN!';
        }
        $data = array(
                    'store_name' => $userCredit->store_name,
                    'nik' => $userCredit->nik,
                    'branch_name' => $userCredit->branch_name,
                    'credit_approval_number' => $credit_number_decrypted,
                    'username' => $userCredit->username,
                    'member_code' => $userCredit->member_code,
                    'address' => $userCredit->address,
                    'phone_number' => $userCredit->phone_number,
                    'npwp' => $userCredit->npwp_number,
                    'pkp' => $userCredit->pkp,
                    'member_type' => $userCredit->tmi_type,
                    'credit_limit' => $this->toIDRCurrency($userCredit->credit_limit),
                    'tenor' => $userCredit->tenor.' Bulan',
                    'last_order_date' => $userCredit->last_order_period,
                    'end_period' => empty($userCredit->end_period)?'Tidak':'YA',
                    'grace_period' => $userCredit->grace_period . ' bulan'
                );
        $pdf = PDF::loadView('template_laporan.dokumen_pengajuan_cicilan', $data);
  
        return $pdf->setPaper('a4')->stream();
    }

    public function getStatementOfDebtAcknowledgment($credit_number_encrypted){
        $helper = new Helper();
        $credit_number_decrypted = $helper->decryptString($credit_number_encrypted);

        if($credit_number_decrypted == false){
            return 'Nomor Pengajuan TIDAK VALID!';
        }

        $userCredit = TDBUserCredit::join('users', 'user_credits.user_id', '=', 'users.id')
                ->join('branches', 'users.branch_id', '=', 'branches.id')
                ->where('credit_number', '=', $credit_number_decrypted)
                ->select('branches.name as branch_name', 'users.member_code', 'user_credits.credit_number',
                    'users.name as username', 'users.address', 'users.store_name',
                    DB::raw('(
                        CASE 
                            WHEN credit_type_id = 1 THEN (real_order/tenor)
                            ELSE installments
                        END
                    ) as cicilan'),
                    DB::raw('('.
                    'CASE '.
                        'WHEN user_credits.flag_take_over = \'Y\' THEN user_credits.monitoring_period '.
                        'ELSE user_credits.tenor '.
                    'END) as tenor')
                    , 'user_credits.real_order',
                    'user_credits.nik', DB::raw('DATE_FORMAT(start_period, "%m/%d/%Y") as start_period'))
                ->first();
        if(empty($userCredit)){
            return 'Nomor Pengajuan TIDAK DITEMUKAN!';
        }
        $data['nik'] = $userCredit->nik;
        $data['branch_name'] = $userCredit->branch_name;
        $data['member_code'] = $userCredit->member_code;
        $data['spph_number'] = $userCredit->credit_number;
        $data['username'] = $userCredit->username;
        $data['address'] = $userCredit->address;
        $data['store_name'] = $userCredit->store_name;
        $data['store_address'] = $userCredit->address;
        
        $cpm = $userCredit->cicilan;
        $data['credit_per_month'] = $this->toIDRCurrency($cpm);
        $data['total_credit'] = $this->toIDRCurrency($userCredit->real_order);
        $data['tenor'] = $userCredit->tenor;
        $data['total_month'] = $userCredit->tenor;
        $total_month = $data['total_month'];
        
        $total_credit = $userCredit->real_order;
        $cicilan = $cpm;
        /*
        foreach($spph as $s){
            $total_month = $s->tenor;
            $total_credit = $s->real_order;
            $current_date = strtotime($s->start_period);
            $cpm = $s->cicilan; // credit per monthinstallments
            for($index = 0; $index < $total_month; $index++){
                if($total_credit > $cpm){
                    $total_credit -= $cpm;
                } else{
                    $cpm = $total_credit;
                }
                array_push($spph_array, array(
                    'NO_DPC'=>$s->NO_DPC,
                    'NP_SPPH'=>($index+1),
                    'TANGGAL_JATUH_TEMPO'=>date('m/d/Y', strtotime("+".$index." month", $current_date)),
                    'AMOUNT_CICILAN'=>$cpm
                ));
            }
        }
        */
        $arr_detail = array();
        $current_date = strtotime($userCredit->start_period);
        for($i = 0; $i < $total_month; $i++){
            if($total_credit > $cicilan){
                $total_credit -= $cicilan;
            } else{
                $cicilan = $total_credit;
            }
            //date('m/d/Y', strtotime("+".$index." month", $current_date)),
            // date('m/d/Y', strtotime("+".$index." month", $current_date))

            $detail = array();
            $detail['no'] = ($i+1);
            $detail['month_year'] = date('M-Y', strtotime("+".$i." month", $current_date));
            $detail['credit_per_month'] = $this->toIDRCurrency($cicilan);//credit_per_month
            $detail['sisa_pokok'] = $this->toIDRCurrency($total_credit);
            $detail['keterangan'] = 'Cicilan ke - '.($i+1);
            array_push($arr_detail, $detail);
        }
        $data['credit_detail'] = $arr_detail;
        $pdf = PDF::loadView('template_laporan.surat_pernyataan_pengakuan_hutang', $data);
  
        return $pdf->setPaper('a4')->stream();
    }

    public function encryptString(Request $request){
        $string = $request->content;
        if(empty($string)){
            return response()->json(['status'=>0, 'content harus di isi!']);
        }
        $helper = new Helper();
        $decrypted = $helper->encryptString($string);
        if($decrypted == false){
            return response()->json(['status'=>0, 'message'=>'Terjadi kesalahan dalam enkrisi string']);
        } else{
            return response()->json(['status'=>1, 'message'=>$decrypted]);
        }
    }

    public function toIDRCurrency($money){
        $IDRFormat = "Rp " . number_format($money,2,',','.');
        return $IDRFormat;
    }
}
