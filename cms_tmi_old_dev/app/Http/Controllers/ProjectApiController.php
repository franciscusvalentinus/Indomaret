<?php

namespace App\Http\Controllers;

use App\Models\MasterToko;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\User;
use Mockery\CountValidator\Exception;

use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ProjectApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAuthData($json){
        //LINUX SERVER
        $response = explode("\n\n", $json, 2);
        if(count($response) < 2){ //CEK WINDOWS SERVER
            $response = explode("\r\n\r\n", $json, 2);
        }
        return json_decode($response[1]);
    }


    public function getMasterTipeTmi(Request $Request){

        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        } catch (Exception $e) {
            return response()->json(['error' => 'unauthorized'], 500);
        }
        $authData = $this->getAuthData(response()->json(compact('user')));

        $response = array();
        try{
            $TipeAssoc = \DB::table('tipe_tmi')
                ->Selectraw('id,nama, kode_tmi')
                ->Get();
            //dd($DivAssoc);
            $response["success"] = 1;
            $response["message"] = "OK";
            $response["tipe_tmi"] = array();
            array_push($response["tipe_tmi"], $TipeAssoc);
        }catch (Exception $ex){
            $response["success"] = 0;
            $response["message"] = "FAILED - " . $ex;
            return response()->json($response);
        }
        //return response()->json($response);
        return response()->json($response);
    }


    public function getMasterPlu(Request $Request){
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        } catch (Exception $e) {
            return response()->json(['error' => 'unauthorized'], 500);
        }
        $authData = $this->getAuthData(response()->json(compact('user')));

        $response = array();
        try{
            $PluAssoc = \DB::table('master_margin')
                ->Selectraw('kodeplu, margin_min, margin_max, margin_saran,master_plu.kode_igr as cabang, 
                master_plu.hrg_jual as harga, tag, long_desc, display, frac_tmi as konversi ,unit_tmi, frac_igr, 
                unit_igr, min_jual,tag, min_dis, qty_max, qty_min, `div`, dep, kat,master_margin.id as idcategory ')
                ->leftJoin('master_plu', 'master_margin.kode_mrg', '=', 'master_plu.mrg_id')

//                ->join('barcodes', function ($join) {
//                    $join->on(\DB::raw('substr(master_plu.kodeplu, 1, 6)'), '=', \DB::raw('substr(barcodes.brc_prdcd, 1, 6)'));
//                    $join->on('master_plu.kode_igr', '=', 'barcodes.brc_kodeigr');
//                })
                ->Where('kode_tmi', $Request->get('tipe'))
                ->Where('master_plu.kode_igr', $Request->get('cab'))
                ->whereNotNull('master_margin.kode_mrg')
                ->Get();


            foreach ($PluAssoc as $index => $Row) {
                $kodecab = $Row->cabang;

                $PLU = substr($Row->kodeplu, 0, 6);

                $barcode = \DB::table('barcodes')
                    ->Selectraw('brc_barcode')
//                    ->Where('brc_prdcd', $Row->kodeplu)
                    ->Where('brc_prdcd', 'LIKE', '' . $PLU . '%')
                    ->Where('brc_kodeigr', $kodecab)
                    ->WhereNotNull('brc_barcode')
                    ->get();

                if($barcode != null && $barcode != ""){
                    foreach ($barcode as $index => $row) {
                        $barcodesss = $row->brc_barcode;
                        $stuff = array($barcodesss);
                        $arraynew = implode(', ', $stuff);
                        $Row->barcode[] = $arraynew;
                    }
                }else{
                        $Row->barcode[] = "";
                }

            }

            $response["success"] = 1;
            $response["message"] = "OK";
            $response["prodmast"] = $PluAssoc;
            $response['tsdfsdfsdf'] = $Request->cab.'  |  '. $Request->tipe;



        }catch (Exception $ex){
            $response["success"] = 0;
            $response["message"] = "FAILED - " . $ex;
            return response()->json($response);
        }
        //return response()->json($response);
        return response()->json($response);
    }

    public function getCabangApi(Request $Request){
        $response = array();
        try{
            $cabAssoc = \DB::table('email_recipients') 
                ->Select('email')
                ->Where('kode_cabang', $Request->get('cab'))
                ->Get();
            //dd($DivAssoc);
            $response["success"] = 1;
            $response["message"] = "OK";
            $response["email_recipients"] = array();
            array_push($response["email_recipients"], $cabAssoc);
        }catch (Exception $ex){
            $response["success"] = 0;
            $response["message"] = "FAILED - " . $ex;
            return response()->json($response);
        }
        //return response()->json($response);
        return response()->json($cabAssoc);
    }




}
