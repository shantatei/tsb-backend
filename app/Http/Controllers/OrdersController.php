<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class OrdersController extends Controller
{
    //
    protected $user;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->user = $this->guard()->user();
    }


    public function saveOrder(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'billing_firstname' => 'required|string',
            'billing_lastname' => 'required|string',
            'billing_email' => 'required|string',
            'billing_address' => 'required|string',
            'billing_postalcode' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 400);
        }

        $order = new Order();
        $order->billing_firstname = $request->billing_firstname;
        $order->billing_lastname = $request->billing_lastname;
        $order->billing_email = $request->billing_email;
        $order->billing_address = $request->billing_address;
        $order->billing_postalcode = $request->billing_postalcode;

        if ($this->user->orders()->save($order)) {
            //insert into order_product table
            $products = $request->products;
            foreach ($products as $item) {

                OrderProduct::create([
                    'order_id' => $order->id,
                    'listing_id' => $item['id'],
                    'quantity' => $item['cartQuantity']
                ]);
            }

            return response()->json(
                [
                    'status' => true,
                    'order' => $order,
                    'cart' => $products
                ]
            );
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Oops, the order could not be saved',
            ]);
        }
    }


    protected function guard()
    {
        return Auth::guard();
    }
}
