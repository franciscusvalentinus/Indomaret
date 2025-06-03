<?php
//todo kalau ada perubahan, di update ke kedua IP ini
//http://172.31.17.94/login ->IP LAMA
//
//172.31.27.68 -> IP BARU
if(env('APP_ENV') == 'production'){
    return [
        'connection_tmi_api'=>'tmibaru',
        'connection_tmi_cms'=>'cms_tmi',
        'tmi_api'=>'http://18.138.71.214:81/api/',
        'tmi_secret_key'=>'RkUeQyBuyiQG78sv9FA4BajVnGGdolhbGfR2YrFD',
        'gateway_branch_api'=>'http://172.31.16.30/api/',
        'hr_domain' => 'http://172.20.12.24/RESTSecurityDev/RESTSecurity.svc/'
    ];
} else if(env('APP_ENV') == 'local'){
    return [
        'connection_tmi_api'=>'tmibaru_local',
        'connection_tmi_cms'=>'cms_tmi_local',
        'tmi_api'=>'http://172.20.110.67/tmi/public/api/',
        'tmi_secret_key'=>'RkUeQyBuyiQG78sv9FA4BajVnGGdolhbGfR2YrFD',
        'gateway_branch_api'=>'http://172.20.28.23:8080/ho-gateway-dev/public/api/',
        'hr_domain' => 'http://172.20.12.24/RESTSecurityDev/RESTSecurity.svc/'
    ];
} else if(env('APP_ENV') == 'sim'){
    return [
        'connection_tmi_api'=>'tmibaru_sim',
        'connection_tmi_cms'=>'cms_tmi_sim',
        'tmi_api'=>'http://172.20.28.23:8080/tmi/public/api/',
        'tmi_secret_key'=>'RkUeQyBuyiQG78sv9FA4BajVnGGdolhbGfR2YrFD',
        'gateway_branch_api'=>'http://172.20.28.23:8080/ho-gateway-dev/public/api/',
        'hr_domain' => 'http://172.20.12.24/RESTSecurityDev/RESTSecurity.svc/'
    ];
} else if(env('APP_ENV') == 'stagging'){
    return [
        'connection_tmi_api'=>'tmibaru_stagging',
        'connection_tmi_cms'=>'cms_tmi_stagging',
        'tmi_api'=>'http://54.251.209.39:81/api/',
        'tmi_secret_key'=>'RkUeQyBuyiQG78sv9FA4BajVnGGdolhbGfR2YrFD',
        'gateway_branch_api'=>'http://172.20.28.23:8080/ho-gateway-dev/public/api/',
        'hr_domain' => 'http://172.20.12.24/RESTSecurityDev/RESTSecurity.svc/'
    ];
}