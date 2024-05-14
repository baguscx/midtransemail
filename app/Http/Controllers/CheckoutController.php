<?php

namespace App\Http\Controllers;

use App\Mail\SendEmail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{


    public function process(Request $request)
    {
        $data = $request->all();

        $transaction = Transaction::create([
            'user_id' => Auth::user()->id,
            'product_id' => $data['product_id'],
            'price' => $data['price'],
            'status' => 'pending',
        ]);

        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('midtrans.isProduction');
        \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is3ds');

        $params = array(
            'transaction_details' => array(
                'order_id' => rand(),
                'gross_amount' => $data['price'],
            ),
            'customer_details' => array(
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ),
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);
        $transaction->snap_token = $snapToken;
        $transaction->save();

        return redirect()->route('checkout', $transaction->id);
    }

    public function checkout(Transaction $transaction)
    {
        $products = config('products');
        $product = collect($products)->firstWhere('id', $transaction->product_id);
        return view('checkout',  compact('transaction', 'product'));
    }

    public function success(Transaction $transaction)
    {
        // dd($transaction);

        $transaction->status = 'success';
        $transaction->save();

        $mailData = [
            'title' => 'Mail from me',
            'body' => 'This is for testing email using smtp.'
        ];
        Mail::to('baguscyy@gmail.com')->send(new SendEmail($mailData));

        return view('success');
    }
}
