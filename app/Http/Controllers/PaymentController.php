<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
       public function createInvoice(Request $request)
    {
        // Set the API endpoint
        $url = 'https://api.nowpayments.io/v1/invoice';

        // Get data from the request
        $data = [
            'price_amount' => $request->input('price_amount'),
            'price_currency' => 'usd',
            'order_id' => $request->input('order_id'),
            'order_description' => $request->input('order_description'),
            'ipn_callback_url' => "https://nowpayments.io",
            'success_url' => "https://nowpayments.io",
            'cancel_url' => "https://nowpayments.io",
            'partially_paid_url' => "https://nowpayments.io",
            'is_fixed_rate' => true,
            'is_fee_paid_by_user' => false,
        ];

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ZNEQVY7-MHZM5Y1-GXPMAS4-8BRP11G',
            'Content-Type: application/json'
        ]);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        // Execute cURL request and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
        }

        // Close cURL session
        curl_close($ch);

        // If there was an error, return it
        if (isset($error_msg)) {
            return response()->json(['error' => $error_msg], 500);
        }

        // Decode the JSON response
        $invoiceData = json_decode($response, true);
        $invoiceUrl = $invoiceData['invoice_url'];

        // Return the invoice as JSON response
        return redirect($invoiceUrl);
        //return response()->json($invoiceUrl);
    }

    public function listPayments()
    {
        // Set the API endpoint
        $url = 'https://api.nowpayments.io/v1/payment';

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ZNEQVY7-MHZM5Y1-GXPMAS4-8BRP11G',
        ]);

        // Execute cURL request and get the response
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
        }

        // Close cURL session
        curl_close($ch);

        // If there was an error, return it
        if (isset($error_msg)) {
            return response()->json(['error' => $error_msg], 500);
        }

        // Decode the JSON response
        $payments = json_decode($response, true);

        // Return the list of payments as JSON response
        return response()->json($payments);
    }
}
