<?php

namespace App\Http\Controllers;

use Request;
use \Midtrans\ApiRequestor;
use \Midtrans\Config;
use \Midtrans\CoreApi;
use \Midtrans\Notification;
use \Midtrans\Sanitizer;
use \Midtrans\Snap;
use \Midtrans\SnapApiRequestor;
use \Midtrans\Transaction;

class MidtransController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function midtrans() {
      print(Config::$serverKey);
      if (!isset(Config::$serverKey)) {
          return "Please set your payment server key";
      }
    }

    public function getSnapToken() {
        $item_list = array();
        $amount = 0;
        Config::$serverKey = 'SB-Mid-server-XrY8eztBflKtDnaMQZcLAxNi';
        if (!isset(Config::$serverKey)) {
            return "Please set your payment server key";
        }

        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        Config::$isProduction = false;

        Config::$isSanitized = true;

        // Enable 3D-Secure
        Config::$is3ds = true;

        // Required
        $item_list[] = [
                'id' => "1",
                'price' => 20000,
                'quantity' => 1,
                'name' => "Dino"
        ];

        $transaction_details = array(
            'order_id' => rand(),
            'gross_amount' => 20000, // no decimal allowed for creditcard
        );

        // Optional
        $item_details = $item_list;

        // Optional
        $billing_address = array(
            'first_name'    => "Dino",
            'last_name'     => "Keylas",
            'address'       => "Jastipin",
            'city'          => "Jakarta",
            'postal_code'   => "16602",
            'phone'         => "081122334455",
            'country_code'  => 'IDN'
        );

        // Optional
        $shipping_address = array(
            'first_name'    => "Fahmi",
            'last_name'     => "Alfareza",
            'address'       => "Aja",
            'city'          => "Jakarta",
            'postal_code'   => "16601",
            'phone'         => "08113366345",
            'country_code'  => 'IDN'
        );

        // Optional
        $customer_details = array(
            'first_name'    => "Arif",
            'last_name'     => "Rahman",
            'email'         => "arif@rahman.com",
            'phone'         => "081122334451",
            'billing_address'  => $billing_address,
            'shipping_address' => $shipping_address
        );

        // Optional, remove this to display all available payment methods
        $enable_payments = array();

        // Fill transaction details
        $transaction = array(
            'enabled_payments' => $enable_payments,
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
        );
        // return $transaction;
        try {
            $snapToken = Snap::getSnapToken($transaction);
            $paymentUrl = Snap::createTransaction($transaction)->redirect_url;
            $result = [
                'token' => $snapToken,
                'redirect_url' => $paymentUrl
            ];
            // dd($result);

            return response()->json($result);
            // return ['code' => 1 , 'message' => 'success' , 'result' => $snapToken];
        } catch (\Exception $e) {
            dd($e);
            return ['code' => 0 , 'message' => 'failed'];
        }
    }
}
