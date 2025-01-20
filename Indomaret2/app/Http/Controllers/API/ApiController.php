<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function getNumber()
    {
        $client = new Client();

        $options = [
            'verify' => false,
            'Accept' => 'application/json',
            'timeout' => 15,
        ];

        $response = Http::get('http://www.randomnumberapi.com/api/v1.0/random?min=100&max=1000&count=5')->getBody()->getContents();

        $response_json = json_decode($response);

        return response()->json($response_json, 200);
    }

    public function login()
    {
        $response = Http::post('http://54.251.209.39:81/api/login_member_api', [
            'email' => 'luckyardiantom3@gmail.com',
            'password' => '123456',
        ]);

        $response_json = json_decode($response, true);

        if ($response_json['status'] == 1) {
            foreach ($response_json['user_data'] as $key => $value) {
                if ($key === 'access_token') {
                    $response2 = Http::withHeaders([
                        'Authorization' => 'Bearer '.$value,
                    ])->post('http://54.251.209.39:81/api/get_data_member');

                    $response_json2 = json_decode($response2);

                    return response()->json($response_json2, 200);
                }
            }
        } else {
            return response()->json([
                "status" => 0,
                "message" => "Email atau password yang anda masukkan salah!",
                "total_product" => 0,
                "user_data" => null,
                "access_token" => null
            ]);
        }
    }
}
