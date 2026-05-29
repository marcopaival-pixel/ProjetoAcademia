<?php declare(strict_types = 1);

// odsl-C:\Projetos\ProjetoAcademia\laravel-app\app\Services\Payment
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-enums',
   'data' => 
  array (
    'C:\\Projetos\\ProjetoAcademia\\laravel-app\\app\\Services\\Payment\\AsaasService.php' => 
    array (
      0 => '350e501f46f6f14882c0eaf89176fd9e046faf47521830f6e452bb60ca2f29cf',
      1 => 
      array (
        0 => 'app\\services\\payment\\asaasservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\payment\\__construct',
        1 => 'app\\services\\payment\\getidentifier',
        2 => 'app\\services\\payment\\createcheckout',
        3 => 'app\\services\\payment\\createsubscription',
        4 => 'app\\services\\payment\\cancelsubscription',
        5 => 'app\\services\\payment\\fetchpayment',
        6 => 'app\\services\\payment\\handlewebhook',
        7 => 'app\\services\\payment\\validatesignature',
        8 => 'app\\services\\payment\\refund',
        9 => 'app\\services\\payment\\getorcreatecustomer',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\Projetos\\ProjetoAcademia\\laravel-app\\app\\Services\\Payment\\BasePaymentGateway.php' => 
    array (
      0 => '8d7c1731e765c368cb5e3487463749ee97496b2901e6a634bbf3464a110c6317',
      1 => 
      array (
        0 => 'app\\services\\payment\\basepaymentgateway',
      ),
      2 => 
      array (
        0 => 'app\\services\\payment\\__construct',
        1 => 'app\\services\\payment\\logwebhook',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\Projetos\\ProjetoAcademia\\laravel-app\\app\\Services\\Payment\\PaymentGatewayManager.php' => 
    array (
      0 => '13bd920133b21e119fd8ccba426a05b08862a4ebb6f720c83fc3e1c7e0352af7',
      1 => 
      array (
        0 => 'app\\services\\payment\\paymentgatewaymanager',
      ),
      2 => 
      array (
        0 => 'app\\services\\payment\\getdefaultdriver',
        1 => 'app\\services\\payment\\createdriver',
        2 => 'app\\services\\payment\\createmercadopagodriver',
        3 => 'app\\services\\payment\\createasaasdriver',
        4 => 'app\\services\\payment\\getgatewayconfig',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\Projetos\\ProjetoAcademia\\laravel-app\\app\\Services\\Payment\\PaymentProcessor.php' => 
    array (
      0 => 'ca887f115624e167931afffa67492d555cd8b3dcceef843ef1926b3f8c1a44f7',
      1 => 
      array (
        0 => 'app\\services\\payment\\paymentprocessor',
      ),
      2 => 
      array (
        0 => 'app\\services\\payment\\processapproved',
        1 => 'app\\services\\payment\\processsubscription',
        2 => 'app\\services\\payment\\processaicredits',
        3 => 'app\\services\\payment\\processgeneralcredits',
        4 => 'app\\services\\payment\\processcommission',
      ),
      3 => 
      array (
      ),
    ),
  ),
));