<?php

namespace App\Http\Controllers\Front;

use App\Classes\GeniusMailer;
use App\Classes\GeniusMessenger;
use App\Models\Order;
use App\Models\OrderTrack;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Generalsetting;
use App\Models\Notification;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorOrder;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use DB;
use Mail;

class FlutterWaveController extends Controller
{

    public function store(Request $request){

        if (!Session::has('cart')) {
            return redirect()->route('front.cart')->with('success',"You don't have any product to checkout.");
         }
    
        if($request->pass_check) {
            $users = User::where('email','=',$request->personal_email)->get();
            if(count($users) == 0) {
                if ($request->personal_pass == $request->personal_confirm){
                    $user = new User;
                    $user->name = $request->personal_name; 
                    $user->email = $request->personal_email;   
                    $user->password = bcrypt($request->personal_pass);
                    $token = md5(time().$request->personal_name.$request->personal_email);
                    $user->verification_link = $token;
                    $user->affilate_code = md5($request->name.$request->email);
                    $user->email_verified = 'Yes';
                    $user->save();
                    Auth::guard('web')->login($user);                     
                }else{
                    return redirect()->back()->with('unsuccess',"Confirm Password Doesn't Match.");     
                }
            } else {
                return redirect()->back()->with('unsuccess',"This Email Already Exist.");  
            }
        }
        
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        if (Session::has('currency')) 
        {
            $curr = Currency::find(Session::get('currency'));
        }
        else
        {
            $curr = Currency::where('is_default','=',1)->first();
        }

        $available_currency = array(
            'BIF',
            'CAD',
            'CDF',
            'CVE',
            'EUR',
            'GBP',
            'GHS',
            'GMD',
            'GNF',
            'KES',
            'LRD',
            'MWK',
            'NGN',
            'RWF',
            'SLL',
            'STD',
            'TZS',
            'UGX',
            'USD',
            'XAF',
            'XOF',
            'ZMK',
            'ZMW',
            'ZWD'
        );

        if(!in_array($curr->name,$available_currency))
        {
            return redirect()->back()->with('unsuccess','Invalid Currency For Flutter Wave.');
        }

        foreach($cart->items as $key => $prod)
        {
            if(!empty($prod['item']['license']) && !empty($prod['item']['license_qty']))
            {
                foreach($prod['item']['license_qty']as $ttl => $dtl)
                {
                    if($dtl != 0)
                    {
                        $dtl--;
                        $produc = Product::findOrFail($prod['item']['id']);
                        $temp = $produc->license_qty;
                        $temp[$ttl] = $dtl;
                        $final = implode(',', $temp);
                        $produc->license_qty = $final;
                        $produc->update();
                        $temp =  $produc->license;
                        $license = $temp[$ttl];
                         $oldCart = Session::has('cart') ? Session::get('cart') : null;
                         $cart = new Cart($oldCart);
                         $cart->updateLicense($prod['item']['id'],$license);  
                         Session::put('cart',$cart);
                        break;
                    }                    
                }
            }
        }

        $settings = Generalsetting::findOrFail(1);

        $order = Order::findOrFail(Session::get('order'));

        $item_number = str_random(4).time();
        $item_amount = $request->total;

        $order['customer_state'] = $request->state;
        $order['shipping_state'] = $request->shipping_state;
        $order['user_id'] = Auth::check() ? Auth::user()->id : $request->user_id;
        $order['cart'] = utf8_encode(bzcompress(serialize($cart), 9));
        $order['totalQty'] = $request->totalQty;
        $wallet = $request->wallet_price;
        $order['pay_amount'] = $request->total / $curr->value;
        $order['method'] = $request->method;
        $order['customer_email'] = $request->email;
        $order['customer_name'] = $request->name;
        $order['customer_phone'] = $request->phone;
        $order['order_number'] = $item_number;
        $order['shipping'] = $request->shipping;
        $order['pickup_location'] = $request->pickup_location;
        $order['customer_address'] = $request->address;
        $order['customer_country'] = $request->customer_country;
        $order['customer_city'] = $request->city;
        $order['customer_zip'] = $request->zip;
        $order['shipping_email'] = $request->shipping_email;
        $order['shipping_name'] = $request->shipping_name;
        $order['shipping_phone'] = $request->shipping_phone;
        $order['shipping_address'] = $request->shipping_address;
        $order['shipping_country'] = $request->shipping_country;
        $order['shipping_city'] = $request->shipping_city;
        $order['shipping_zip'] = $request->shipping_zip;
        $order['order_note'] = $request->order_notes;
        $order['coupon_code'] = $request->coupon_code;
        $order['coupon_discount'] = $request->coupon_discount;
        $order['payment_status'] = "Pending";
        $order['currency_sign'] = $curr->sign;
        $order['currency_value'] = $curr->value;
        $order['shipping_cost'] = $request->shipping_cost;
        $order['packing_cost'] = $request->packing_cost;
        $order['shipping_title'] = $request->shipping_title;
        $order['packing_title'] = $request->packing_title;
        $order['tax'] = $request->tax;
        $order['dp'] = $request->dp;

        $order['vendor_shipping_id'] = $request->vendor_shipping_id;
        $order['vendor_packing_id'] = $request->vendor_packing_id;
        $order['wallet_price'] = round($wallet / $curr->value, 2);  

        if($order['dp'] == 1) {
            $order['status'] = 'completed';
        }

        if (Session::has('affilate')) {
            $val = $request->total / $curr->value;
            $val = $val / 100;
            $sub = $val * $settings->affilate_charge;
            $user = User::findOrFail(Session::get('affilate'));
            if($user){
                if($order['dp'] == 1)
                {
                    $user->affilate_income += $sub;
                    $user->update();
                }

                $order['affilate_user'] = $user->id;
                $order['affilate_charge'] = $sub;
            }
        }
        $order->save();

        if(Auth::check()){
            Auth::user()->update(['balance' => (Auth::user()->balance - $order->wallet_price)]);
        }


        if($request->coupon_id != "")
        {
            $coupon = Coupon::findOrFail($request->coupon_id);
            $coupon->used++;
            if($coupon->times != null)
            {
                $i = (int)$coupon->times;
                $i--;
                $coupon->times = (string)$i;
            }
            $coupon->update();

        }

        foreach($cart->items as $prod)
        {
            $x = (string)$prod['stock'];
            if($x != null)
            {
                $product = Product::findOrFail($prod['item']['id']);
                $product->stock =  $prod['stock'];
                $product->update();                
            }
        }

        foreach($cart->items as $prod)
        {
            $x = (string)$prod['size_qty'];
            if(!empty($x))
            {
                $product = Product::findOrFail($prod['item']['id']);
                $x = (int)$x;
                $x = $x - $prod['qty'];
                $temp = $product->size_qty;
                $temp[$prod['size_key']] = $x;
                $temp1 = implode(',', $temp);
                $product->size_qty =  $temp1;
                $product->update();               
            }
        }

        foreach($cart->items as $prod)
        {
            $x = (string)$prod['stock'];
            if($x != null)
            {

                $product = Product::findOrFail($prod['item']['id']);
                $product->stock =  $prod['stock'];
                $product->update();  
                if($product->stock <= 5)
                {
                    $notification = new Notification;
                    $notification->product_id = $product->id;
                    $notification->save();    
                    
                    $gs = Generalsetting::first();
                    if($gs->is_smtp == 1)
                    {
                        $maildata = [
                            'to' => $product->user->email,
                            'subject' => 'Out of Stock Alert!',
                            'body' => "One of your product is almost out of stock (less or equal to 5).\n<strong>Product Link: </strong> <a target='_blank' href='".url('/').'/'.'item/'.$product->slug."'>".$product->name."</a>",
                        ];
                        $mailer = new GeniusMailer();
                        $mailer->sendCustomMail($maildata);
                    }
                    else
                    {
                        $to = $product->user->email;
                        $subject = 'Out of Stock Alert!';
                        $msg = "One of your product is almost out of stock (less or equal to 5).\n<strong>Product Link: </strong> <a target='_blank' href='".url('/').'/'.'item/'.$product->slug."'>".$product->name."</a>";
                        $headers = "MIME-Version: 1.0" . "\r\n";
                        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                        $headers .= "From: ".$gs->from_name."<".$gs->from_email.">";
                        mail($to,$subject,$msg,$headers);
                    }
                }              
            }
        }


        $gs = Generalsetting::find(1);
       
        //Sending Email To Buyer

        if($gs->is_smtp == 1)
        {
            $data = [
                'to' => $request->email,
                'type' => "new_order",
                'cname' => $request->name,
                'oamount' => "",
                'aname' => "",
                'aemail' => "",
                'wtitle' => "",
                'onumber' => $order->order_number,
            ];

            $mailer = new GeniusMailer();
            $mailer->sendAutoOrderMail($data,$order->id);            
        }
        else
        {
           $to = $request->email;
           $subject = "Your Order Placed!!";
           $msg = "Hello ".$request->name."!\nYou have placed a new order.\nYour order number is ".$order->order_number.".Please wait for your delivery. \nThank you.<br><br>All at ProjectShelve <br> Mobile: (+234) 08147801594 <br>Phone: (+234) 08096221646<br>Email: projectshelve@gmail.com";
           $headers = "MIME-Version: 1.0" . "\r\n";
           $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
           $headers .= "From: ".$gs->from_name."<".$gs->from_email.">";
           // mail($to,$subject,$msg,$headers);            
        }

        //Sending Email To Admin
        // if($gs->is_smtp == 1)
        // {
        //     $data = [
        //         'to' => $gs->header_email,
        //         'subject' => "New Order Recieved!!",
        //         'body' => "Hello Admin!<br>Your store has received a new order.<br>Order Number is ".$order->order_number.".Please login to your panel to check. <br>Thank you.<br><br>All at ProjectShelve <br> Mobile: (+234) 08147801594 <br>Phone: (+234) 08096221646<br>Email: projectshelve@gmail.com",
        //     ];

        //     $mailer = new GeniusMailer();
        //     $mailer->sendCustomMail($data);            
        // }
        // else
        // {
        //     $to = $gs->from_email;
        //     $subject = "New Order Recieved!!";
            
        //     $msg = "Hello Admin!<br><br>Your store has recieved a new order.<br>Order Number is " . $order->order_number;
        //     $msg .= ".Please login to your panel to check.<br>Thank you.<br><br>";
        //     $msg .= "All at ProjectShelve <br> Mobile: (+234) 08147801594 <br>Email: projectshelve@gmail.com";

        //     $headers = "MIME-Version: 1.0" . "\r\n";
        //     $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        //     $headers .= "From: ".$gs->from_name."<".$gs->from_email.">";
           
        //     // mail($to,$subject,$msg,$headers);
        //     Mail::send(array(), array(), function ($message) use ($msg, $to, $subject, $headers) {
        //         $message->to($to)
        //         ->subject($subject)
        //         ->setBody($msg,'text/html');
        //     });  
        // }

        Session::put('tempcart',$cart);
        Session::forget('cart');
        Session::forget('pickup_text');
        Session::forget('pickup_cost');
        Session::forget('pickup_costshow');

        // SET CURL

        $curl = curl_init();

        $customer_email = $request->email;
        $amount = $item_amount;  
        $currency = $curr->name;
        $txref = $item_number; // ensure you generate unique references per transaction.
        $PBFPubKey = $settings->flutter_public_key; // get your public key from the dashboard.
        $redirect_url = action('Front\FlutterWaveController@notify');
        $payment_plan = ""; // this is only required for recurring payments.

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'amount' => $amount,
                'customer_email' => $customer_email,
                'currency' => $currency,
                'txref' => $txref,
                'PBFPubKey' => $PBFPubKey,
                'redirect_url' => $redirect_url,
                'payment_plan' => $payment_plan
            ]),
            CURLOPT_HTTPHEADER => [
                "content-type: application/json",
                "cache-control: no-cache"
            ],
        ));
    
        $response = curl_exec($curl);
        if ($response === FALSE) {  // there was an error contacting the rave API
            // dd('Curl returned error: ' . curl_error($curl));
            // return;
            return redirect()->route('front.cart')->with('unsuccess',"Sorry. We can't connect you to Flutter Wave now.");
        }
        curl_close($curl);
        
        $transaction = json_decode($response);
        if(!$transaction->data && !$transaction->data->link){
          // there was an error from the API
          print_r('API returned error: ' . $transaction->message);
        }
        
        return redirect($transaction->data->link);

    }
   
   
    public function notify(Request $request) {

        $input = json_decode($request['resp']);
        if ( isset($input->tx->txRef) ) {
            $ref = $input->tx->txRef;

            $settings = Generalsetting::findOrFail(1);

            $query = array(
                "SECKEY" => $settings->flutter_secret,
                "txref" => $ref
            );

            $data_string = json_encode($query);
                    
            $ch = curl_init('https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                              
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

            $response = curl_exec($ch);
            curl_close($ch);

            $resp = json_decode($response, true);

            if ($resp['status'] == "success" && !empty($resp['data']['status'])) {

                $paymentStatus = $resp['data']['status'];
                
                // $chargeResponsecode = $resp['data']['chargecode'];

                //  (($chargeResponsecode == "00" || $chargeResponsecode == "0") && ($paymentStatus == "successful")) {
                    
                if ($paymentStatus == "successful") {
        
                    $order = Order::where('order_number',$resp['data']['txref'])->first();
                    $order->update([
                        'txnid' => $resp['data']['txid'],
                        'payment_status' => 'Completed',
                        // 'dp' => 1,
                        'status' => 'completed'
                    ]);
                    // dd($order);
                    // return;
                    if($order->dp == 1) {
                        $track = new OrderTrack;
                        $track->title = 'Completed';
                        $track->text = 'Your order has completed successfully.';
                        $track->order_id = $order->id;
                        $track->save();
                    }
                    else {
                        $track = new OrderTrack;
                        $track->title = 'Pending';
                        $track->text = 'You have successfully placed your order.';
                        $track->order_id = $order->id;
                        $track->save();
                    }
                    if($order->wallet_price != 0)
                    {
                        $user = User::find($order->user_id);
                        $user->balance -= $order->wallet_price;
                        $user->update();
                    }
        
                    $cart = unserialize(bzdecompress(utf8_decode($order->cart)));

                    foreach($cart->items as $keyProduct =>$valueProduct ){
                        // echo "<pre>";print_r($valueProduct['item']['user_id']);die;
                        if($valueProduct['item']['user_id'] != 0 ){
                            $cartitemprice =  DB::table('products')->where('id','=',$keyProduct)->pluck('price');
                        }            
                    }
                        
                    if(isset($cartitemprice)) {
                        foreach($cartitemprice as $k=> $v){
                            $fprice[]= $v;
                        }
                        $sumtotalcartprice = array_sum($fprice);
                    }
                    
                    $notf = null;
        
                    foreach($cart->items as $prod)
                    {
                        $repeat = VendorOrder::where('order_id',$order->id)->first();
                        if($prod['item']['user_id'] != 0 && !$repeat)
                        {
                            $vorder =  new VendorOrder;
                            $vorder->order_id = $order->id;
                            $vorder->user_id = $prod['item']['user_id'];
                            $notf[] = $prod['item']['user_id'];
                            $vorder->qty = $prod['qty'];
                            $vorder->price = $prod['price'];
                            $vorder->order_number = $order->order_number;
                            $vorder->status = $order->payment_status;
                            $vorder->total_price = $sumtotalcartprice;
                            $vorder->save();
                            if($order->dp == 1){
                                $vorder->user->update(['current_balance' => $vorder->user->current_balance += $sumtotalcartprice]);
                            }
                        }        
                    }
        
                    if(!empty($notf))
                    {
                        $users = array_unique($notf);
                        foreach ($users as $user) {
                            $notification = new UserNotification;
                            $notification->user_id = $user;
                            $notification->order_number = $order->order_number;
                            $notification->save();    
                        }
                    }            
        
                    if ($order->user_id != 0 && $order->wallet_price != 0) {
                        $transaction = new \App\Models\Transaction;
                        $transaction->txn_number = str_random(3).substr(time(), 6,8).str_random(3);
                        $transaction->user_id = $order->user_id;
                        $transaction->amount = $order->wallet_price;
                        $transaction->currency_sign = $order->currency_sign;
                        $transaction->currency_code = \App\Models\Currency::where('sign',$order->currency_sign)->first()->name;
                        $transaction->currency_value= $order->currency_value;
                        $transaction->details = 'Payment Via Wallet';
                        $transaction->type = 'minus';
                        $transaction->save();
                    }
        
                    $notification = new Notification;
                    $notification->order_id = $order->id;
                    $notification->save();
                    Session::put('temporder',$order);
                    Session::forget('cart');  
                    
                    $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
                    
                    $str = ''; $str2 = '';                
                    $vid = null; $mstr = '';
                    
                    $prd_type = isset($product['license']) && !empty($product['license']) ? 'license' : 'digital';

                    foreach($cart->items as $product) {
                        
                        if($product['item']['user_id'] != 0)
                        {
                            $vid = $product['item']['user_id'];
                            $mstr .= $product['item']['name'].'<br>';
                        }

                        $dataFormat = DB::table('products')->where('id','=',$product['item']['id'])->get();
                        $subcat_id = $dataFormat[0]->subcategory_id;
                        $dataFormat = $dataFormat[0]->file_format;


                        if ($prd_type == 'license') {
                            $subcat = DB::table('subcategories')->where('id','=',$subcat_id)->get();
                            $subcat = $subcat[0]->name;
                            $str .= "Find below your \"" . $subcat . "\".<br><br>";
                        } else {                
                            $str .= "Click link below to download your product.<br><br>";
                        }

                        $str .= "Product Title: ".$product['item']['name']."<br>";
                        $str .= "Product Code: 000".$product['item']['id']."<br>";
                        $str .= "Price:" .Product::convertPrice($product['item_price'])."<br>";

                        $str2 .= $str;
                        $str2 .= 'Download Link: '.asset('assets/files/'.$product['item']['file']).'<br><br>';
                        // $str .= 'Download Link: <a href="'.asset('assets/files/'.$product['item']['file']).'" target="_blank">Click here</a><br><br>';
                        if ($prd_type == 'license') {
                            $str .= "PIN: ".$product['license']."<br><br>";
                        } else {
                            $str .= "Format: ".$dataFormat."<br>";
                            $str .= 'Download Link: <a href="'.asset('assets/files/'.$product['item']['file']).'" target="_blank">Click here</a><br><br>';
                        }
                    }
                    
                    $to = $order->customer_email;

                    $subject = 'Product Delivered';
            
                    $msg  = "Hello ".$order->customer_name.",<br><br>";
                    $msg .= "Click link below to download your product.<br><br>";
                    $msg .= $str."<br>";
                    $msg .= "Thank you for trusting us. We look forward to your next visit.<br><br>";
                    $msg .= "All at ProjectShelve <br>";
                    $msg .= "Call/WhatsApp: (+234) 08147801594<br>";
                    $msg .= "E-mail: projectshelve@gmail.com<br>";
                    $msg .= "Website: www.projectshelve.com<br>";
                    
                    $headers  = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= 'From: ProjectShelve <projectshelve@gmail.com>' . "\r\n" .
                        'Reply-To: projectshelve@gmail.com ' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();

                    if ($settings->is_smtp == 1) {

                        $mailer = new GeniusMailer();
                        $mailer->sendCustomMail([
                            'to' => $to,
                            'subject' => $subject,
                            'body' => $msg
                        ]);

                    } else {
                        Mail::send(array(), array(), function ($message) use ($msg,$to,$subject,$headers) {
                            $message->to($to)
                            ->subject($subject)
                            ->setBody($msg,'text/html');
                        });  
                        // mail($to, $subject, $msg, $headers);
                    }

                    if ($vid) {                    
                        $vuser = User::findOrFail($vid);
                        $to = $vuser->email;

                        $subject = 'Product Purchase';
    
                        $msg2 = "Hello ".$vuser->name." <br><br>";
                        $msg2 .="A purchase has been made on your product.<br>";
                        $msg2 .= $mstr;
                        $msg2 .="Kindly login and view details on your dashboard.<br><br>";
                        $msg2 .="Thank you as we look forward for further mutual advantage..<br><br>";  
                        $msg2 .="All at ProjectShelve<br> ";
                        $msg2 .="Call/WhatsApp: (+234) 08147801594<br> ";
                        $msg2 .="E-mail: projectshelve@gmail.com<br>";
                        $msg2 .="Website: www.projectshelve.com<br>";
                        
                        $headers = "MIME-Version: 1.0" . "\r\n";
                        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                        $headers .= "From: ProjectShelve <projectshelve@gmail.com>";
    
                        if ($settings->is_smtp == 1) {
    
                            $mailer = new GeniusMailer();
                            $mailer->sendCustomMail([
                                'to' => $to,
                                'subject' => $subject,
                                'body' => $msg2
                            ]);
    
                        } else {
    
                            $sent =   Mail::send(array(), array(), function ($message) use ($msg2, $to, $subject, $headers) {
                                $message->to($to)
                                ->subject($subject)
                                ->setBody($msg2,'text/html')
                                ->getHeaders()
                                ->addTextHeader($headers, 'true');
                            }); 
                            // mail($to, $subject, $msg2, $headers);
                        }
                    }
                    
                    $msg3  = "Hello ".$order->customer_name.",<br><br>";
                    $msg3 .= "Click link below to download your product.<br><br>";
                    $msg3 .= $str2."<br>";
                    $msg3 .= "Thank you for trusting us. We look forward to your next visit.<br><br>";
                    $msg3 .= "All at ProjectShelve <br>";
                    $msg3 .= "Call/WhatsApp: (+234) 08147801594<br>";
                    $msg3 .= "E-mail: projectshelve@gmail.com<br>";
                    $msg3 .= "Website: www.projectshelve.com<br>";
                    
                    $phone_number = $order->customer_phone;
                    
                    $userReg = User::where('phone', $phone_number)->count();
                    
                    if ($userReg) {
                        $mailer = new GeniusMailer();
                        $mailer->sendWhatsAppMsg($msg3, $phone_number);	
                    }
                    
                    return redirect(action('Front\PaymentController@payreturn'));
        
                }

                else {
                    $payment = Order::where('order_number', $input->tx->txRef)->first();
                    VendorOrder::where('order_id','=',$payment->id)->delete();
                    $payment->delete();
                    Session::forget('cart');
                    return redirect(action('Front\PaymentController@paycancle'));
                }

            } else { // $resp['status'] != "success" || empty($resp['data']['status'])
                $payment = Order::where('order_number', $input->tx->txRef)->first();
                VendorOrder::where('order_id','=',$payment->id)->delete();
                $payment->delete();
                Session::forget('cart');
                return redirect(action('Front\PaymentController@paycancle'));
            }
        }

    }

}
