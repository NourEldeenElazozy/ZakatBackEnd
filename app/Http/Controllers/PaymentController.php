<?php

namespace App\Http\Controllers;

use App\Helpers\Edfali;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SoapClient;

class PaymentController extends Controller
{
    protected $edfali;

    public function __construct(Edfali $edfali)
    {
        $this->edfali = $edfali;
    }

    public function doPayment(Request $request)
{

    $Cmobile = $request->input('Cmobile');
    $amount = $request->input('amount');

    try {
        // استدعاء الدالة لإجراء العملية
        $soapResponse = $this->edfali->doPTrans( $Cmobile, $amount);

        // معالجة الرد بناءً على القيم المختلفة
        $message = '';
        $httpStatus = 200; // الحالة الافتراضية للنجاح

        switch ($soapResponse) {
            case 'PW1':
                $message = 'كلمة مرور الخدمة خاطئة.';
                $httpStatus = 400; // خطأ في الإدخال
                break;
            case 'PW':
                $message = 'رقم PIN خاطئ.';
                $httpStatus = 400; // خطأ في الإدخال
                break;
            case 'ACC':
            case 'LIMIT':
            case 'BAL':
                $message = 'يوجد خطأ في الحساب أو حد الرصيد.';
                $httpStatus = 400; // خطأ في الإدخال
                break;
            default:
                if (is_numeric($soapResponse)) {
                    $message = "العملية تمت بنجاح، رقم المعاملة: $soapResponse";
                } else {
                    throw new Exception('رد غير متوقع من الخدمة.');
                }
        }

        // إعادة الرد كاستجابة JSON
        return response()->json([
            'status' => $httpStatus === 200 ? 'success' : 'error',
            'message' => $message,
        ], $httpStatus);

    } catch (Exception $e) {
        // في حالة حدوث خطأ أثناء العملية
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500); // خطأ داخلي في الخادم
    }
}
public function doConf(Request $request)
{
    $sessionID = $request->input('sessionID');
    $amount = $request->input('amount');
    $Pin = $request->input('Pin');

    // تحضير المعاملات
    $params = [
        'Mobile' => "942602030", // رقم الهاتف
        'Pin' => $Pin,           // الرقم السري
        'sessionID' => $sessionID,
        'Amount' => $amount,
        'PW' => env('DEFAULT_PW', '123@xdsr$#!!') // كلمة المرور الافتراضية
    ];

    try {
        // إنشاء اتصال بـ SOAP باستخدام WSDL

        $client = new SoapClient('http://62.240.55.2:6187/BCDUssd/Edfali.asmx?WSDL', ['trace' => 1]);

        // استدعاء الدالة SOAP المطلوبة مع المعاملات
        $response = $client->__soapCall('OnlineConfTrans', [$params]);

        // تسجيل الاستجابة للتشخيص
        Log::info("SOAP Response: ", (array) $response);

        // التحقق من الاستجابة
        if (isset($response->OnlineConfTransResult)) {
            $soapResponse = $response->OnlineConfTransResult;
        } else {
            throw new Exception('لم يتم الحصول على استجابة صالحة من الخدمة.');
        }

        // معالجة الرد بناءً على القيم المختلفة
        $message = '';
        $httpStatus = 200; // الحالة الافتراضية للنجاح


        switch ($soapResponse) {
            case 'OK':
                $message = 'العملية تمت بنجاح';
                $httpStatus = 200; // خطأ في الإدخال
                break;
            case 'PW1':
                $message = 'كلمة مرور الخدمة خاطئة.';
                $httpStatus = 400; // خطأ في الإدخال
                break;
            case 'PW':
                $message = 'رقم PIN خاطئ.';
                $httpStatus = 400; // خطأ في الإدخال
                break;
            case 'ACC':
            case 'LIMIT':
            case 'BAL':
                $message = 'يوجد خطأ في الحساب أو حد الرصيد.';
                $httpStatus = 400; // خطأ في الإدخال
                break;
            default:
                if ($soapResponse=='OK') {
                    $message = "العملية تمت بنجاح، رقم المعاملة: $soapResponse";
                } else {
                    throw new Exception('رد غير متوقع من الخدمة.');
                }
        }

        // إعادة الرد كاستجابة JSON
        return response()->json([
            'status' => $httpStatus === 200 ? 'success' : 'error',
            'message' => $message,
        ], $httpStatus);

    } catch (Exception $e) {
        // في حالة حدوث خطأ أثناء العملية
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500); // خطأ داخلي في الخادم
    }


}


}
