<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use Braintree\Gateway;
use Illuminate\Http\Request;
use App\Http\Requests\Orders\OrderRequest;
use App\Models\Promotion;

class OrderController extends Controller
{
    public function generate(Request $request, Gateway $gateway){

        $token= $gateway->clientToken()->generate();

        $data=[
            'token'=>$token
        ];

        return response()->json($data, 200);
    }

    public function makePayment(OrderRequest $request, Gateway $gateway){

        $promotion=Promotion::find($request->promotion);

        $result=$gateway->transaction()->sale([
            'amount'=>$promotion->price,
            'paymentMethodNonce'=>$request->token,
            'options'=>[
                'submitForSettlement'=>true
            ]
        ]);

        if ($result->success){
            $data=[
                'success'=> true,
                'message'=>'Transazione esequita con successo.'
            ];
            return response()->json($data, 200);
        }else{
            $data=[
                'success'=>false,
                'token'=>'Transazione fallita.'
            ];
            return response()->json($data, 401);
        }
        
    }
}
