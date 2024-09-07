<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use ShurjopayPlugin\PaymentRequest;
use ShurjopayPlugin\Shurjopay;
use GuzzleHttp\Client;
use App\Providers\ShurjoPayServiceProvider;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public $sp_instance;
    public $sp_token;
    /* Shurjopay injected in a constructor */
    public function __construct(Shurjopay $sp)
    {
        $this->sp_instance = $sp;
    }

    public function paymentForm($id)
    {
        $booking = Booking::find($id);
        return view('frontend.pages.payment.form', compact('booking'));
    }

    public function initiatePayment(Request $request, $id)
    {
        if ($users = auth()->guard('tenantCheck')->user()) {
            $tenant = $users->id;
            $user = $users->id;
            $bookingDetails = Booking::with('flat.house')->where('tenant_id', $tenant)->first();
            $userName = auth()->guard('tenantCheck')->user()->name;
            $userPhone = auth()->guard('tenantCheck')->user()->phone;
            $userEmail = auth()->guard('tenantCheck')->user()->email;
            $userAddress = auth()->guard('tenantCheck')->user()->address;
        } elseif ($users = auth()->guard('ownerCheck')->user()) {
            $owner = $users->id;
            $user = $users->id;
            $bookingDetails = Booking::with('flat.house.houseOwner')->where('owner_id', $owner)->first();
            $userName = auth()->guard('ownerCheck')->user()->name;
            $userPhone = auth()->guard('ownerCheck')->user()->phone;
            $userEmail = auth()->guard('ownerCheck')->user()->email;
            $userAddress = auth()->guard('ownerCheck')->user()->address;
        } else {
            return redirect()->back()->with('error', 'Unauthorised user.');
        }

        $postFields = array('username' => env('SP_USERNAME'), 'password' => env('SP_PASSWORD'));
        $client = new Client();

        $response = $client->request('POST', env('URL_AUTH'), [
            'verify' => false,
            'form_params' => $postFields
        ]);
        $info = json_decode($response->getBody()->getContents());
        $this->sp_token = $info->token;

        $payment_req = [
            'token' => $this->sp_token,
            'prefix' => 'sp',
            'return_url' => route('frontend.success'),
            'cancel_url' => route('frontend.booking.list', $user),
            'store_id' => '1',
            'order_id' => $id,
            'currency' => 'BDT',
            'amount' => $request->paid_amount,
            'discountAmount' => '0',
            'discPercent' => '0',
            'client_ip' => request()->ip(),
            'customer_name' => $userName,
            'customer_phone' => $userPhone,
            'customer_email' => $userEmail,
            'customer_address' => $userAddress,
            'customer_city' => 'Dhaka',
            'customerState' => 'Dhaka',
            'customerPostcode' => '1207',
            'customerCountry' => 'Bangladesh',
            'shippingAddress' => 'Sirajganj',
            'shippingCity' => 'Dhaka',
            'shippingCountry' => 'Bangladesh',
            'receivedPersonName' => 'Cris Gayle',
            'shippingPhoneNumber' => '01722222222',
            'value1' => 'value1',
            'value2' => 'value2',
            'value3' => 'value3',
            'value4' => 'value4',
        ];

        session([
            'amount' => $bookingDetails->flat->rent,
            'paid_amount' => $request->paid_amount,
            'due' => $request->due,
            'booking_id' => $id,
        ]);

        $response = $client->post('https://sandbox.shurjopayment.com/api/secret-pay', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->sp_token,
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
            'json' => $payment_req,
        ]);

        $responseBody = json_decode($response->getBody()->getContents());
        $responseCode = $response->getStatusCode();

        return redirect($responseBody->checkout_url);

    }

    public function success(Request $request)
    {
        $order_id = $request->order_id;
        $client = new Client();
        $postFields = array('username' => env('SP_USERNAME'), 'password' => env('SP_PASSWORD'));

        $response = $client->request('POST', env('URL_AUTH'), [
            'verify' => false,
            'form_params' => $postFields
        ]);
        $info = json_decode($response->getBody()->getContents());
        $this->sp_token = $info->token;

        $verify_response = $client->post('https://www.sandbox.shurjopayment.com/api/verification', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->sp_token,
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
            'json' => ['order_id' => $order_id],
        ]);

        $data = json_decode($verify_response->getBody()->getContents(), true);

        if (auth('tenantCheck')->check()) {
            $userId = auth('tenantCheck')->id();
        } elseif (auth('ownerCheck')->check()) {
            $userId = auth('ownerCheck')->id();
        }

        if (isset($data[0]) && $data[0]['bank_status'] == 'Success') {
            $paymentDetails = $data[0];

            Payment::create([
                'booking_id' => session('booking_id'),
                'transaction_id' => $paymentDetails['bank_trx_id'],
                'payment_date' => now(),
                'payment_month' => now()->format('F'),
                'amount' => session('amount'),
                'paid_amount' => session('paid_amount'),
                'due' => session('due'),
                'payment_method' => 'ShurjoPay',
            ]);

            Booking::where('id', session('booking_id'))->update([
                'payment_status' => 'Paid',
            ]);
            return redirect()->route('frontend.booking.list', $userId)->with('success', 'Payment successful.');
        } else {
            return redirect()->route('frontend.booking.list', $userId)->with('error', 'Payment verification failed.');
        }
    }

    public function manualPayment()
    {
        if (auth('ownerCheck')->check()) {
            $ownerId = auth('ownerCheck')->user()->id;
        }
        $bookingDetails = Booking::with(['tenant', 'flat.house.houseOwner'])
            ->whereHas('flat.house', function ($query) use ($ownerId) {
                $query->where('house_owner_id', $ownerId);
            })->where('booking_status', 'Approved')->where('payment_status', 'Pending')->get();
        return view('frontend.pages.payment.manual', compact('bookingDetails', 'ownerId'));
    }

    public function manualPaymentStore(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'amount' => 'required',
            'paid_amount' => 'required',
            'due' => 'required',
            'payment_date' => 'required',
            'payment_month' => 'required',
        ]);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->getMessageBag());
        }

        if (auth('ownerCheck')->check()) {
            $user = auth('ownerCheck')->user()->id;
        } elseif (auth('tenantCheck')->check()) {
            $user = auth('tenantCheck')->user()->id;
        }

        Payment::create([
            'booking_id' => $request->booking_id,
            'transaction_id' => date('YmdHis'),
            'amount' => $request->amount,
            'paid_amount' => $request->paid_amount,
            'due' => $request->due,
            'payment_date' => $request->payment_date,
            'payment_month' => $request->payment_month,
            'payment_method' => 'handcash',
        ]);

        return redirect()->route('frontend.applicant.list', $user)->with('success', 'Payment successful.');
    }

}
