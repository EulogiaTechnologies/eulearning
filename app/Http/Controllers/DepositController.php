<?php

namespace App\Http\Controllers;

use Adrianbarbos\Mobilpay\Mobilpay;
use App\DepositRecord;
use Brian2694\Toastr\Facades\Toastr;
use Bryceandy\Laravel_Pesapal\Facades\Pesapal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Modules\BankPayment\Http\Controllers\BankPaymentController;
use Modules\Instamojo\Http\Controllers\InstamojoController;
use Modules\MercadoPago\Http\Controllers\MercadoPagoController;
use Modules\Midtrans\Http\Controllers\MidtransController;
use Modules\Mobilpay\Http\Controllers\MobilpayController;
use Modules\Payeer\Http\Controllers\PayeerController;
use Modules\PaymentMethodSetting\Entities\PaymentMethod;
use Modules\Paytm\Http\Controllers\PaytmController;
use Modules\RazerMS\Http\Controllers\RazerMSController;
use Modules\Razorpay\Http\Controllers\RazorpayController;
use Modules\Sslcommerz\Http\Controllers\SslcommerzController;
use Netopia\Payment\Address;
use Netopia\Payment\Invoice;
use Netopia\Payment\Request\Card;
use Omnipay\Omnipay;
use Unicodeveloper\Paystack\Facades\Paystack;
use Modules\CCAvenue\Libraries\CCAvenue;
use Modules\Organization\Entities\OrganizationFinance;
use Modules\Organization\Events\CourseSellCommissionEvent;

class DepositController extends Controller
{
    public $payPalGateway;

    public function __construct()
    {
        $this->middleware(['maintenanceMode', 'onlyAppMode']);

        $this->payPalGateway = Omnipay::create('PayPal_Rest');
        $this->payPalGateway->setClientId(getPaymentEnv('PAYPAL_CLIENT_ID'));
        $this->payPalGateway->setSecret(getPaymentEnv('PAYPAL_CLIENT_SECRET'));
        $this->payPalGateway->setTestMode(getPaymentEnv('IS_PAYPAL_LOCALHOST') == 'true'); //set it to 'false' when go live
    }

    public function redirectToDashboard()
    {
        if (\auth()->user()->role_id == 3) {
            return redirect(route('studentDashboard'));
        } else {
            return redirect(route('dashboard'));
        }

    }

    public function depositSelectOption(Request $request)
    {
        $data = $request->validate([
            'deposit_amount' => 'required|numeric',
        ]);
        $amount = $request->deposit_amount;
        $records = DepositRecord::where('user_id', Auth::user()->id)->paginate(5);
        $methods = PaymentMethod::where('active_status', 1)->where('method', '!=', 'Wallet')->get(['method', 'logo']);
        return view(theme('depositSelect'), compact('records', 'methods', 'amount'));
    }

    public function depositSubmit(Request $request)
    {
        $data = $request->validate([
            'deposit_amount' => 'required|numeric',
            'method' => 'required',
        ]);

        if (Settings('hide_multicurrency') == 1) {
            $amount = (float)number_format(convertCurrency(auth()->user()->currency->code ?? Settings('currency_code'), Settings('currency_code'), $data['deposit_amount']), 2);
        } else {
            $amount = $data['deposit_amount'];
        }
        $system_currency = Settings('currency_code');

        $request->merge([
            "deposit_amount" => $amount
        ]);

        $data['deposit_amount'] = $amount;

        if ($data['method'] == "Sslcommerz") {
            $ssl = new SslcommerzController();
            $ssl->deposit($amount);

        } elseif ($data['method'] == "PayPal") {

            $response = $this->payPalGateway->purchase(array(
                'amount' => $amount,
                'currency' => $system_currency,
                'returnUrl' => route('paypalDepositSuccess'),
                'cancelUrl' => route('paypalDepositFailed'),

            ))->send();

            if ($response->isRedirect()) {
                $response->redirect(); // this will automatically forward the customer
            } else {
                Toastr::error($response->getMessage(), trans('common.Failed'));
                return \redirect()->back();
            }
        } elseif ($data['method'] == "Midtrans") {

            try {
                $midtrans = new MidtransController();
                $request->merge(['type' => 'Deposit']);
                $response = $midtrans->makePayment($request);

                if ($response) {
                    return $response;
                } else {
                    Toastr::error('Something went wrong', 'Failed');
                    return \redirect()->back();
                }
            } catch (\Exception $e) {
                GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

            }


        } elseif ($data['method'] == "Payeer") {

            try {
                $payeer = new PayeerController();
                $request->merge(['type' => 'Deposit']);
                $response = $payeer->makePayment($request);
                if ($response) {
                    return \redirect()->to($response);
                } else {
                    Toastr::error('Something went wrong', 'Failed');
                    return \redirect()->back();
                }
            } catch (\Exception $e) {
                GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

            }


        } elseif ($data['method'] == "MercadoPago") {
            $mercadoPagoController = new MercadoPagoController();
            $response = $mercadoPagoController->payment($request->all());
            return response()->json(['target_url' => $response]);
        } elseif ($data['method'] == "Instamojo") {
            $instamojo = new InstamojoController();
            $response = $instamojo->depositProcess($request);
            if ($response) {
                return \redirect()->to($response);
            } else {
                Toastr::error('Something went wrong', 'Failed');
                return \redirect()->back();
            }

        } elseif ($data['method'] == "Stripe") {

            if (empty($request->get('stripeToken'))) {
                Toastr::error('Something went wrong', 'Failed');
                return $this->redirectToDashboard();
            }

            $token = $request->stripeToken;
            $gatewayStripe = Omnipay::create('Stripe');
            $gatewayStripe->setApiKey(getPaymentEnv('STRIPE_SECRET'));


            $response = $gatewayStripe->purchase(array(
                'amount' => $amount,
                'currency' => $system_currency,
                'token' => $token,
            ))->send();

            if ($response->isRedirect()) {
                // redirect to offsite payment gateway
                $response->redirect();
            } elseif ($response->isSuccessful()) {
                // payment was successful: update database

                $amount = number_format($response->getData()['amount'] / 100, 2);

                $payWithStripe = $this->depositWithGateWay($amount, $response, "Stripe");
                if ($payWithStripe) {
                    Toastr::success('Payment done successfully', 'Success');
                    return $this->redirectToDashboard();
                } else {
                    Toastr::error('Something Went Wrong', 'Error');
                    return $this->redirectToDashboard();
                }

            } else {

                if ($response->getCode() == "amount_too_small") {
                    $amount = number_format($response->getData()['amount'] / 100, 2);
                    $message = "Amount must be at least " . Settings('currency_symbol') . ' ' . $amount;
                    Toastr::error($message, 'Error');
                } else {
                    Toastr::error($response->getMessage(), 'Error');
                }
                return redirect()->back();

            }
        } elseif ($data['method'] == "RazorPay") {

            if (empty($request->razorpay_payment_id)) {
                Toastr::error('Something Went Wrong', 'Error');
                return \redirect()->back();
            }

            $payment = new RazorpayController();
            $response = $payment->payment($request->razorpay_payment_id);
            if ($response['type'] == "error") {
                Toastr::error($response['message'], 'Error');
                return \redirect()->back();
            }


            $amount = number_format($response['response']['amount'] / 100, 2);

            $payWithRazorPay = $this->depositWithGateWay($amount, $response['response'], "RazorPay");

            if ($payWithRazorPay) {
                Toastr::success('Payment done successfully', 'Success');
                return $this->redirectToDashboard();
            } else {
                Toastr::error('Something Went Wrong', 'Error');
                return $this->redirectToDashboard();
            }
        } elseif ($data['method'] == "PayTM") {

            $phone = Auth::user()->phone;
            $email = Auth::user()->email;
            if (empty($phone)) {
                Toastr::error('Phone number is required. Please update your profile ', 'Error');
                return redirect()->back();
            }

            $payment = new PaytmController();
            $userData = [
                'user' => Auth::user()->id,
                'mobile' => $phone,
                'email' => $email,
                'amount' => $amount,
                'order' => Auth::user()->phone . "_" . rand(1, 1000),
            ];
            return $payment->deposit($userData);
        } elseif ($data['method'] == "PayStack") {
            try {
                return Paystack::getAuthorizationUrl()->redirectNow();
            } catch (\Exception $e) {
                GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

            }
        } elseif ($data['method'] == "RazerMS") {
            try {
                $payment = new RazerMSController();
                $url = $payment->generatePaymentUrl($data['deposit_amount'], 'deposit');
                return redirect($url);
            } catch (\Exception $e) {
                GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

            }
        } elseif ($data['method'] == "Bank Payment") {


            $rules = [
                'bank_name' => 'required',
                'branch_name' => 'required',
                'type' => 'required',
                'account_number' => 'required',
                'account_holder' => 'required',
                'image' => 'mimes:jpeg,jpg,png,gif|required',
            ];
            $this->validate($request, $rules, validationMessage($rules));


            try {

                $payment = new BankPaymentController();
                $result = $payment->store($request);

                if ($result) {
                    return $this->redirectToDashboard();
                } else {
                    return redirect()->back();
                }

            } catch (\Exception $e) {
                GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

            }
        } elseif ($data['method'] == "Pesapal") {
            try {
                $paymentData = [
                    'amount' => $request->deposit_amount,
                    'currency' => Settings('currency_code'),
                    'description' => 'Deposit',
                    'type' => 'MERCHANT',
                    'reference' => 'Deposit|' . $request->deposit_amount,
                    'first_name' => Auth::user()->first_name,
                    'last_name' => Auth::user()->last_name,
                    'email' => Auth::user()->email,
                ];

                $iframe_src = Pesapal::getIframeSource($paymentData);

                return view('laravel_pesapal::iframe', compact('iframe_src'));

            } catch (\Exception $e) {
                GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

            }
        } elseif ($data['method'] == "Mobilpay") {
            $mobilpay = new MobilpayController();
            return $mobilpay->depositProcess($request);

        } elseif ($data['method'] == 'Authorize.Net') {
            $authorize = new \Modules\AuthorizeNet\Repositories\AuthorizeNetRepository();
            $response = $authorize->payNow($request, $data['deposit_amount']);
            if ($response != null) {
                if ($response->getMessages()->getResultCode() == "Ok") {
                    $tresponse = $response->getTransactionResponse();
                    if ($tresponse != null && $tresponse->getMessages() != null) {
                        $payWithStripe = $this->depositWithGateWay($data['deposit_amount'], $response, "Authorize.Net");
                        if ($payWithStripe) {
                            Toastr::success('Payment done successfully', 'Success');
                            return $this->redirectToDashboard();
                        } else {
                            Toastr::error('Something Went Wrong', 'Error');
                            return $this->redirectToDashboard();
                        }
                    } else {
                        $msg = 'There were some issue with the payment. Please try again later.';
                        if ($tresponse->getErrors() != null) {
                            $msg = $tresponse->getErrors()[0]->getErrorText();
                        }
                        Toastr::error($msg, 'Failed');
                        return Redirect::back();
                    }
                } else {
                    Toastr::error('Something went wrong', 'Failed');
                    return Redirect::back();
                }
            } else {
                Toastr::error('Something went wrong', 'Failed');
                return Redirect::back();
            }
        } elseif ($data['method'] == 'Braintree') {
            $braintree = new \Modules\Braintree\Repositories\BraintreeRepository();
            $result = $braintree->payNow($request, $data['deposit_amount']);
            if ($result->success) {
                $payWithBrain = $this->depositWithGateWay($data['deposit_amount'], $result, "Braintree");
                if ($payWithBrain) {
                    Toastr::success('Payment done successfully', 'Success');
                    return $this->redirectToDashboard();
                } else {
                    Toastr::error('Something Went Wrong', 'Error');
                    return $this->redirectToDashboard();
                }
            } else {
                Toastr::error('Something Went Wrong', 'Error');
                return $this->redirectToDashboard();
            }
        } elseif ($data['method'] == "Mollie") {
            $mollie = new \Modules\Mollie\Repositories\MollieRepository();
            $payment_info = [
                "amount" => $data['deposit_amount'],
                'success_url' => route('mollieDepositSuccess'),
                "order_id" => time(),
                "details" => "Balance Deposit",
            ];
            $result = $mollie->preparePayment($payment_info);
            session()->put('mollie_id', $result->id);
            return redirect($result->getCheckoutUrl(), 303);
        } elseif ($data['method'] == 'Flutterwave') {
            $flutterwave = new \Modules\Flutterwave\Repositories\FlutterwaveRepository();
            $pay_info = [
                "name" => Auth::user()->name,
                "phone" => Auth::user()->phone,
                "email" => Auth::user()->email,
                "title" => "Balance Deposit",
                "description" => "Deposit Money on your Account",
                "webhook" => route('flutterwaveDepositWebhook')
            ];
            $payment = $flutterwave->payNow($pay_info, $request->deposit_amount);
            if ($payment->status == 'success') {
                return redirect()->to($payment->data->link);
            } else {
                Toastr::error("Something went wrong", 'Error');
                return back();
            }
        } elseif ($data['method'] == 'CCAvenue') {
            $ccavenue = new CCAvenue();
            $payment_info = [
                "currency" => "INR",
                "name" => Auth::user()->name,
                "city" => Auth::user()->cityDetails->name,
                "state" => Auth::user()->stateDetails->name,
                "country" => Auth::user()->userCountry->name,
                "zip" => Auth::user()->zip,
                "mobile" => Auth::user()->phone,
                "email" => Auth::user()->email,
                "customer_identifier" => "",
            ];
            return $response = $ccavenue->prepare($payment_info, $request->deposit_amount);
        } elseif ($data['method'] == 'Jazz Cash') {
            try {
                $jazz = new \Modules\JazzCash\Repositories\JazzcashRepository();
                $payment_info = [
                    "billref" => time(),
                    "description" => "Deposit Amount",
                    "email" => Auth::user()->email,
                    "mobile" => Auth::user()->phone,
                ];
                $result = $jazz->getArray($payment_info, $request->deposit_amount);
                $post_url = $jazz->getPostUrl();
                session()->put('from', 'deposit');
                return view('jazzcash::payment', compact('post_url'));
            } catch (\Exception $e) {
                Toastr::error($e->getMessage(), 'Error');
                return back();
            }
        } elseif ($data['method'] == 'Coinbase') {
            $coinbase = new \Modules\Coinbase\Repositories\CoinbaseRepository();
            $payment_info = [
                "name" => "Deposit Balance",
                "description" => "Deposit Balance",
                "currency" => "USD",
                "charge_type" => "deposit"
            ];


            $result = $coinbase->makePayment($payment_info, $request->deposit_amount);

            session()->get('pay_from', 'deposit');
            session()->get('charge_id', $result['data']['id']);


            if (isset($result['data']) && isset($result['data']['hosted_url'])) {
                return redirect()->to($result['data']['hosted_url']);
            } else {
                Toastr::error("Something went wrong", 'Error');
                return back();
            }
        }


    }


    public static function depositWithGateWay($amount, $response, $gateWayName, $user = null)
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
            }

            if ($user) {

                DB::beginTransaction();
                $user->balance += (float)$amount;
                $user->save();
                Log::info($user);
                $depositRecord = new DepositRecord();
                $depositRecord->user_id = $user->id;
                $depositRecord->method = $gateWayName;
                $depositRecord->amount = (float)$amount;
                $depositRecord->response = json_encode($response);
                $depositRecord->save();

                if (isModuleActive('Cashback') && $depositRecord) {
                    generateCashback($depositRecord->user_id, $depositRecord->amount, 'recharge', $depositRecord);
                }
                if (isModuleActive('Organization') && $user->isOrganization()) {
                    $data = [
                        'user_id' => $user->id,
                        'amount' => $amount,
                        'status' => true,
                        'type' => OrganizationFinance::$credit,
                        'description' => OrganizationFinance::$deposit_description,
                        'data_type' => OrganizationFinance::$type_deposit,
                        'payment_type' => OrganizationFinance::$payment_completed,
                    ];
                    event(new CourseSellCommissionEvent($data));
                }

                Toastr::success('Deposit done successfully', trans('common.Success'));
                DB::commit();
                return true;

            } else {

                Log::info('Something Went Wrong');
                Toastr::error('Something Went Wrong', 'Error');
                return false;
            }


        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent(), true);
        }
    }

    public function paypalDepositSuccess(Request $request)
    {

        // Once the transaction has been approved, we need to complete it.
        if ($request->input('paymentId') && $request->input('PayerID')) {
            $transaction = $this->payPalGateway->completePurchase(array(
                'payer_id' => $request->input('PayerID'),
                'transactionReference' => $request->input('paymentId'),
            ));
            $response = $transaction->send();

            if ($response->isSuccessful()) {
                // The customer has successfully paid.
                $arr_body = $response->getData();
                $paymentAmount = $arr_body['transactions'][0]['amount'];

                $amount = number_format($paymentAmount['total'], 2);

                $payWithPapal = $this->depositWithGateWay($amount, $arr_body, "PayPal");
                if ($payWithPapal) {
                    Toastr::success('Payment done successfully', 'Success');
                    return $this->redirectToDashboard();
                } else {
                    Toastr::error('Something Went Wrong', 'Error');
                    return $this->redirectToDashboard();
                }

            } else {
                $msg = str_replace("'", " ", $response->getMessage());
                Toastr::error($msg, 'Failed');
                return redirect()->back();
            }
        } else {
            Toastr::error('Transaction is declined');
            return redirect()->back();
        }


    }

    public function paypalDepositFailed()
    {
        Toastr::error('User is canceled the payment.', 'Failed');
        return redirect()->back();
    }
}
