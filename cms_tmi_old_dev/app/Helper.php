<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Helper extends Model
{
    protected $key = 'tmi-key';
    protected $cipher = "AES-256-CBC";
    protected $unique = '(TMI)';

    public $REQUEST_TMI_ACCESS_TOKEN = 1;
    public $REQUEST_BRANCHES_ACCESS_TOKEN = 2;

    public function encryptString($string){
        $encrypted = openssl_encrypt($string, $this->cipher, $this->key);
        $encrypted = str_replace("/", $this->unique, $encrypted);
        return $encrypted;
    }

    public function decryptString($encrypted){
        $encrypted = str_replace($this->unique, "/", $encrypted);
        $decrypted = openssl_decrypt($encrypted, $this->cipher, $this->key);
        return $decrypted;
    }

    public function getTmiMachineAccessToken(){
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
//            return $result;
        if(!$result){die("Connection Failure");}
        curl_close($curl);

        $data_login_machine = json_decode($result, true);


        if($data_login_machine['status'] == 0){
            return array('status'=>0, 'message'=>$data_login_machine['message']);
        }
        return array('status'=>1,'message'=>$data_login_machine['access_token']);
    }

    public function getIgrGatewayAccessToken(){
        $curl = curl_init();
        $auth_data = array(
            'grant_type'=>'password',
            'username'=>'CMS_TMI',
            'password'=>'CMS_TMI'
        );

        // $url_gateway = config('cms_config.gateway_branch_api').'oauth/token';
        //todo buat sementara aja!!!! karena ada masalah di variable config nya!
        //$url_gateway = 'http://172.20.28.23:8080/ho-gateway-dev/public/api/'.'oauth/token';
        $url_gateway = config('cms_config.gateway_branch_api').'oauth/token';

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $auth_data);
        curl_setopt($curl, CURLOPT_URL, $url_gateway);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_PROXY, '');

        $result = curl_exec($curl);
        curl_close($curl);
        $data_login_gateway = json_decode($result, true);

        if($result['status'] == 'ERROR'){
            return array('status'=>0, 'message'=>$data_login_gateway['message']);
        } 
        return array('status'=>1, 'message'=>$data_login_gateway['access_token']);
    }

    public function curlHelper($method, $url, $token_type, $body){
        $is_need_access_token = true;
        $access_token = null;
        if($method != 'POST' && $method != 'GET'){
            return array(
                'status'=>0,
                'message'=>'Method tidak sesuai'
            );
        }

        if($token_type == $this->REQUEST_TMI_ACCESS_TOKEN){
            $data = $this->getTmiMachineAccessToken();
            if($data['status'] == 1){
                $access_token = $data['message'];
            } else{
                $error_message = $data['message']==null?'Terjadi kesalahan dalam mendapatkan akses token':$data['message'];
                return array('status'=>0, 'message'=>$error_message);
            }
        } else if($token_type == $this->REQUEST_BRANCHES_ACCESS_TOKEN){
            $data = $this->getIgrGatewayAccessToken();
            if($data['status'] != 'OK'){
                $access_token = $data['message'];
            } else{
                return array('status'=>0, 'message'=>$data['message']);
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_URL, $url);
        $headers = array(
            // "Content-Type: application/json",
            "Accept: application/json"
        );
        if($is_need_access_token){
            array_push($headers, 
                'Authorization: Bearer '.$access_token
            );
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //CURLOPT_RETURNTRANSFER => true,
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 400);
        curl_setopt($ch, CURLOPT_PROXY, '');

        $get_response = curl_exec($ch);

        curl_close($ch);

        $get_response = json_decode($get_response, true);
        return $get_response;
    }
}
