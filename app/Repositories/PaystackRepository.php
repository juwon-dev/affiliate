<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Config;
use App\Interfaces\PaymentInterface;
use App\Http\Traits\PaymentTrait;


class PaystackRepository implements PaymentInterface
{

    use PaymentTrait;

    public function redirect($fields)
    {
        $gateway = $fields['gateway'];

        $url = $this->getRedirectUrl($gateway);

        $fields['currency'] = 'NGN';
        $fields['amount'] *= 100;
        $fields['key'] = env('PAYSTACK_PUBLIC_KEY');
        $fields['callback_url'] = route('verify', $gateway);

        $fields_string = http_build_query($fields);
        //open connection
        $ch = curl_init();
        
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer " . $this->getSecretKey($gateway),
        "Cache-Control: no-cache",
        ));
        
        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        
        //execute post
        $result = curl_exec($ch);

        $transaction = json_decode($result, true);
        
        if(! $transaction['status']){
            return redirect()->route('cancel');
        }

        $this->ref = $transaction['data']['reference'];
        $authorization_url = $transaction['data']['authorization_url'];
       // header('Location: ' . $authorization_url, true);
        return redirect($authorization_url);
    }


    public function verify($gateway)
    {
        $curl = curl_init();
        $reference = isset($_GET['reference']) ? $_GET['reference'] : '';
        if(!$reference){
            die('No reference supplied');
        }

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->getVerifyUrl("paystack-basic") . rawurlencode($reference),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer " . $this->getSecretKey($gateway),
            "Cache-Control: no-cache",
            "Content-Type: application/json"
        ],
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if($err){
            // there was an error contacting the Paystack API
        die('Curl returned error: ' . $err);
        }

        $tranx = json_decode($response);

        if(!$tranx->status){
        // there was an error from the API
            die('API returned error: ' . $tranx->message);
        }
        
        if('success' == $tranx->data->status){

            // transaction was successful...
            // please check other things like whether you already gave value for this ref
            // if the email matches the customer who owns the product etc
            // Give value
            return [
                "status" => $tranx->data->status,
                "message" => $tranx->message,
                "data" => $tranx->data,
                "ref" => $reference
            ];
        }else{
            return [
                "status" => false,
                "message" => $tranx->message,
                "ref" => $reference
            ];
        }
    }
}
