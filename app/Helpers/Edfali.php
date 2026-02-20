<?php

namespace App\Helpers;

use Exception;
use SoapClient;

class Edfali {

    protected $wsdl = 'http://62.240.55.2:6187/BCDUssd/Edfali.asmx?WSDL';

    // دالة لتحويل المعاملات
    public function doPTrans( $customerMobile, $amount) {
        // تنسيق رقم العميل
        $formattedCustomerMobile = '+218' . ltrim($customerMobile, '+');

        // تحضير المعاملات
        $params = [
            'Mobile' => "942602030",
            'Pin' =>"8494",
            'Cmobile' => $formattedCustomerMobile,
            'Amount' => $amount,
            'PW' => env('DEFAULT_PW', '123@xdsr$#!!')  // من الأفضل تخزين كلمة المرور في البيئة
        ];

        try {
            // إنشاء اتصال بـ SOAP باستخدام WSDL
            $client = new SoapClient($this->wsdl, ['trace' => 1]);

            // استدعاء الدالة SOAP المطلوبة مع المعاملات
            $response = $client->__soapCall('DoPTrans', [$params]);

            // معالجة الاستجابة
            if (isset($response->DoPTransResult)) {
                return $response->DoPTransResult;
            } else {
                throw new Exception('Unexpected response type.');
            }

        } catch (\SoapFault $fault) {
            throw new Exception('SOAP Fault: ' . $fault->getMessage());
        }
    }
    public function OnlineConfTrans( $amount,$sessionID,$Pin) {


        // تحضير المعاملات
        $params = [
            'Mobile' => "942602030",
            'Pin' =>$Pin,
            'sessionID' => $sessionID,
            'Amount' => $amount,
            'PW' => env('DEFAULT_PW', '123@xdsr$#!!')  // من الأفضل تخزين كلمة المرور في البيئة
        ];

        try {
            // إنشاء اتصال بـ SOAP باستخدام WSDL
            $client = new SoapClient($this->wsdl, ['trace' => 1]);

            // استدعاء الدالة SOAP المطلوبة مع المعاملات
            $response = $client->__soapCall('OnlineConfTrans', [$params]);


            // معالجة الاستجابة


        } catch (\SoapFault $fault) {
            throw new Exception('SOAP Fault: ' . $fault->getMessage());
        }
    }

}
