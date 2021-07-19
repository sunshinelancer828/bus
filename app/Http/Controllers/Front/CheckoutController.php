<?php

namespace App\Http\Controllers\Front;

use App\Classes\GeniusMailer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Generalsetting;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderTrack;
use App\Models\Pagesetting;
use App\Models\PaymentGateway;
use App\Models\Pickup;
use App\Models\Product;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\VendorOrder;
use Mail;
use Auth;
use Image;
use DB;

use Session;
use Validator;

class CheckoutController extends Controller
{
    
    public function sendconfirmemail(){
        
        $email = $_GET['uemail'];
        $totalPrice = $_GET['totalcost'];
        $name = $_GET['nametest'];
        $phone_number = $_GET['phone_number'];
              
        $gs = Generalsetting::findOrFail(1);
		$userCart =  Session::get('cart')  ;
        $cartArr =  (array) $userCart;
        $cartJson = json_encode ( $cartArr ) ;
     
        $save_dir =   public_path().'/user-cart/';
        
        if($email) {
       
            $msg ="hello ".$name.",<br><br>";
            $msg .="Thank you for your interest in service. Below is an overview of your order:<br><br>";
            $msg2 = $msg;
            $msg .="<h3>Overview:</h3><br>";
            $msg2 .="Overview:";
            
            $str=''; $mstr = '';
            
           foreach($userCart->items as $product){
    			$dataFormat =      DB::table('products')->where('id','=',$product['item']['id'])->select('name','price','photo','attributes','thumbnail','slug','file_format')->first();
            	$str .= "Product Title: ".$product['item']['name']."<br>";
            	$str .= "Product Code: 000".$product['item']['id']."<br>";
            	$str .= "Price:" .Product::convertPrice($product['item_price'])."<br>";
            	if($product['item']['user_id'] != 0){
            	    $mstr .= $product['item']['name'].'<br>';
            	    $vid = $product['item']['user_id'];
            	}
    			if($dataFormat) {
            	    $str .= "Format: ".$dataFormat->file_format."<br>";
    			}
            	$str .= 'Product Link: <a href="' . url('/')."/item/".$product['item']['slug'].'">' . $product['item']['name'] . '</a><br><br>"';
           }
           
        	if(Session::has('coupon_total')) {
        	    
				if($gs->currency_format == 0){
				    
					$cost = $curr->sign." ".$totalPrice;
					
				} else { 
				    
					$cost = $totalPrice." ".$curr->sign;	
				} 
				
			} elseif (Session::has('coupon_total1')) {
			    
				$cost =  Session::get('coupon_total1');
				
			} else {
			    
				$cost =  Product::convertPrice($totalPrice);
			}
	
              $userCart = Session::get('cart');
 
			$file_uniqe = uniqid().time();
			$file_name = $file_uniqe.'.json';
			$payhere = "";
			$url = url('/');
			$payhere .='<a href="https://projectshelve.com/checkout">PAY HERE</a>';
			$payhere2 = 'pay here '.$url.'?user-cart='.$file_uniqe;

            $msg .= $str."<br><br>";
            $msg .= "Kindly ".$payhere." to conclude your order and get your product instantly<br><br>";
            $msg .= "Thank you for using ProjectShelve.<br><br>";
            $msg .= "All at ProjectShelve <br>";
            $msg .= "Call/WhatsApp: (+234) 08147801594<br>";
            // $msg .= "E-mail: info@projectshelve.com<br>";
            // $msg .= "Website: www.projectshelve.com<br>";
            
    	    $to = $email;
    	    
            $subject = 'Your Order is Confirmed';
			
            $msg1 = $msg;
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: ProjectShelve <info@projectshelve.com>" . "\r\n" .
                'Reply-To: info@projectshelve.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
   
            // $sent =  mail($to,$subject,$msg1,$headers);
                
                         // $sent =   Mail::send(array(), array(), function ($message) use ($msg1) {
                         //      $message->to("sufianahmed14239@gmail.com")
                         //     ->subject("Your Order is Confirmed")
                         //      ->setBody($msg1,'text/html');
                                
                         //    });  
                              $sent =   Mail::send(array(), array(), function ($message) use ($msg1,$to,$subject,$headers) {
                              $message->to($to)
                             ->subject($subject)
                              ->setBody($msg1,'text/html');
                            });         
                   
			 if ($sent) {
				$file = $save_dir . $file_name;
				$fp = fopen($file, "wb");
				fwrite($fp,$cartJson);
				fclose($fp);
			 }
			 
			$msg2 .= $str."<br><br>";
            $msg2 .= "Kindly ".$payhere2." to conclude your order and get your product instantly<br><br>";
            $msg2 .= "Thank you for using ProjectShelve.<br><br>";
            $msg2 .= "All at ProjectShelve <br>";
            $msg2 .= "Call/WhatsApp: (+234) 08147801594<br>";
            $msg2 .= "E-mail: info@projectshelve.com<br>";
            $msg2 .= "Website: www.projectshelve.com<br>";
            
            $userReg = User::where('phone', $phone_number)->count();
              
            // if ($userReg) {
    			$mailer = new GeniusMailer();
    			$mailer->sendWhatsAppMsg($msg2, $phone_number);
            // }
        }
        
    }
    
    public function loadpayment($slug1,$slug2)
    {
        if (Session::has('currency')) {
            $curr = Currency::find(Session::get('currency'));
        }
        else {
            $curr = Currency::where('is_default','=',1)->first();
        }
        $payment = $slug1;
        $pay_id = $slug2;
        $gateway = '';
        if($pay_id != 0) {
            $gateway = PaymentGateway::findOrFail($pay_id);
        }
        return view('load.payment',compact('payment','pay_id','gateway','curr'));
    }

    public function checkout()
    {
        $this->code_image();
        if (!Session::has('cart')) {
            return redirect()->route('front.cart')->with('success',"You don't have any product to checkout.");
        }
        $gs = Generalsetting::findOrFail(1);
        $dp = 0;
        $vendor_shipping_id = 0;
        $vendor_packing_id = 0;
        if (Session::has('currency')) 
        {
          $curr = Currency::find(Session::get('currency'));
        }
        else
        {
            $curr = Currency::where('is_default','=',1)->first();
        }

        if(Auth::guard('web')->check())
        {
                $gateways =  PaymentGateway::where('status','=',1)->get();
                $pickups = Pickup::all();
                $oldCart = Session::get('cart');
                $cart = new Cart($oldCart);
                $products = $cart->items;

                // Shipping Method

                if($gs->multiple_shipping == 1)
                {                        
                    $user = null;
                    foreach ($cart->items as $prod) {
                            $user[] = $prod['item']['user_id'];
                    }
                    $users = array_unique($user);
                   
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
                   
                  // echo "<pre>";print_r($sumtotalcartprice);die;
                    if(count($users) == 1)
                    {

                        $shipping_data  = DB::table('shippings')->where('user_id','=',$users[0])->get();
                        if(count($shipping_data) == 0){
                            $shipping_data  = DB::table('shippings')->where('user_id','=',0)->get();
                        }
                        else{
                            $vendor_shipping_id = $users[0];
                        }
                    }
                    else {
                        $shipping_data  = DB::table('shippings')->where('user_id','=',0)->get();
                    }

                }
                else{
                $shipping_data  = DB::table('shippings')->where('user_id','=',0)->get();
                }

                // Packaging

                if($gs->multiple_packaging == 1)
                {
                    $user = null;
                    foreach ($cart->items as $prod) {
                            $user[] = $prod['item']['user_id'];
                    }
                    $users = array_unique($user);
                    if(count($users) == 1)
                    {
                        $package_data  = DB::table('packages')->where('user_id','=',$users[0])->get();
                        if(count($package_data) == 0){
                            $package_data  = DB::table('packages')->where('user_id','=',0)->get();
                        }
                        else{
                            $vendor_packing_id = $users[0];
                        }
                    }
                    else {
                        $package_data  = DB::table('packages')->where('user_id','=',0)->get();
                    }

                }
                else{
                $package_data  = DB::table('packages')->where('user_id','=',0)->get();
                }


                foreach ($products as $prod) {
                    if($prod['item']['type'] == 'Physical')
                    {
                        $dp = 0;
                        break;
                    }
                }
                if($dp == 1)
                {
                $ship  = 0;                    
                }
                $total = $cart->totalPrice;
                $coupon = Session::has('coupon') ? Session::get('coupon') : 0;
                if($gs->tax != 0)
                {
                    $tax = ($total / 100) * $gs->tax;
                    $total = $total + $tax;
                }
                if(!Session::has('coupon_total'))
                {
                $total = $total - $coupon;     
                $total = $total + 0;               
                }
                else {
                $total = Session::get('coupon_total');  
                $total = $total + round(0 * $curr->value, 2); 
                }
                
          
        return view('front.checkout', ['products' => $cart->items, 'totalPrice' => $total, 'pickups' => $pickups, 'totalQty' => $cart->totalQty, 'gateways' => $gateways, 'shipping_cost' => 0, 'digital' => $dp, 'curr' => $curr,'shipping_data' => $shipping_data,'package_data' => $package_data, 'vendor_shipping_id' => $vendor_shipping_id, 'vendor_packing_id' => $vendor_packing_id]);             
        }

        else

        {
           	if($gs->guest_checkout == 1)
              {
                $gateways =  PaymentGateway::where('status','=',1)->get();
                $pickups = Pickup::all();
                $oldCart = Session::get('cart');
                $cart = new Cart($oldCart);
                $products = $cart->items;

                // Shipping Method

                if($gs->multiple_shipping == 1)
                {
                    $user = null;
                    foreach ($cart->items as $prod) {
                            $user[] = $prod['item']['user_id'];
                    }
                    $users = array_unique($user);
                    if(count($users) == 1)
                    {
                        $shipping_data  = DB::table('shippings')->where('user_id','=',$users[0])->get();

                        if(count($shipping_data) == 0){
                            $shipping_data  = DB::table('shippings')->where('user_id','=',0)->get();
                        }
                        else{
                            $vendor_shipping_id = $users[0];
                        }                        
                    }
                    else {
                        $shipping_data  = DB::table('shippings')->where('user_id','=',0)->get();
                    }

                }
                else{
                $shipping_data  = DB::table('shippings')->where('user_id','=',0)->get();
                }

                // Packaging

                if($gs->multiple_packaging == 1)
                {
                    $user = null;
                    foreach ($cart->items as $prod) {
                            $user[] = $prod['item']['user_id'];
                    }
                    $users = array_unique($user);
                    if(count($users) == 1)
                    {
                        $package_data  = DB::table('packages')->where('user_id','=',$users[0])->get();

                        if(count($package_data) == 0){
                            $package_data  = DB::table('packages')->where('user_id','=',0)->get();
                        }
                        else{
                            $vendor_packing_id = $users[0];
                        }  
                    }
                    else {
                        $package_data  = DB::table('packages')->where('user_id','=',0)->get();
                    }

                }
                else{
                $package_data  = DB::table('packages')->where('user_id','=',0)->get();
                }


                foreach ($products as $prod) {
                    if($prod['item']['type'] == 'Physical')
                    {
                        $dp = 0;
                        break;
                    }
                }
                if($dp == 1)
                {
                $ship  = 0;                    
                }
                $total = $cart->totalPrice;
                $coupon = Session::has('coupon') ? Session::get('coupon') : 0;
                if($gs->tax != 0)
                {
                    $tax = ($total / 100) * $gs->tax;
                    $total = $total + $tax;
                }
                if(!Session::has('coupon_total'))
                {
                $total = $total - $coupon;     
                $total = $total + 0;               
                }
                else {
                $total = Session::get('coupon_total');  
                $total =  str_replace($curr->sign,'',$total) + round(0 * $curr->value, 2); 
                }
                foreach ($products as $prod) {
                    if($prod['item']['type'] != 'Physical')
                    {
                        if(!Auth::guard('web')->check())
                        {
                $ck = 1;
        return view('front.checkout', ['products' => $cart->items, 'totalPrice' => $total, 'pickups' => $pickups, 'totalQty' => $cart->totalQty, 'gateways' => $gateways, 'shipping_cost' => 0, 'checked' => $ck, 'digital' => $dp, 'curr' => $curr,'shipping_data' => $shipping_data,'package_data' => $package_data, 'vendor_shipping_id' => $vendor_shipping_id, 'vendor_packing_id' => $vendor_packing_id]);  
                        }
                    }
                }
        return view('front.checkout', ['products' => $cart->items, 'totalPrice' => $total, 'pickups' => $pickups, 'totalQty' => $cart->totalQty, 'gateways' => $gateways, 'shipping_cost' => 0, 'digital' => $dp, 'curr' => $curr,'shipping_data' => $shipping_data,'package_data' => $package_data, 'vendor_shipping_id' => $vendor_shipping_id, 'vendor_packing_id' => $vendor_packing_id]);                 
               }

// If guest checkout is Deactivated then display pop up form with proper error message

                    else{
                $gateways =  PaymentGateway::where('status','=',1)->get();
                $pickups = Pickup::all();
                $oldCart = Session::get('cart');
                $cart = new Cart($oldCart);
                $products = $cart->items;

                // Shipping Method

                if($gs->multiple_shipping == 1)
                {
                    $user = null;
                    foreach ($cart->items as $prod) {
                            $user[] = $prod['item']['user_id'];
                    }
                    $users = array_unique($user);
                    if(count($users) == 1)
                    {
                        $shipping_data  = DB::table('shippings')->where('user_id','=',$users[0])->get();

                        if(count($shipping_data) == 0){
                            $shipping_data  = DB::table('shippings')->where('user_id','=',0)->get();
                        }
                        else{
                            $vendor_shipping_id = $users[0];
                        }  
                    }
                    else {
                        $shipping_data  = DB::table('shippings')->where('user_id','=',0)->get();
                    }

                }
                else{
                $shipping_data  = DB::table('shippings')->where('user_id','=',0)->get();
                }

                // Packaging

                if($gs->multiple_packaging == 1)
                {
                    $user = null;
                    foreach ($cart->items as $prod) {
                            $user[] = $prod['item']['user_id'];
                    }
                    $users = array_unique($user);
                    if(count($users) == 1)
                    {
                        $package_data  = DB::table('packages')->where('user_id','=',$users[0])->get();

                        if(count($package_data) == 0){
                            $package_data  = DB::table('packages')->where('user_id','=',0)->get();
                        }
                        else{
                            $vendor_packing_id = $users[0];
                        }  
                    }
                    else {
                        $package_data  = DB::table('packages')->where('user_id','=',0)->get();
                    }

                }
                else{
                $package_data  = DB::table('packages')->where('user_id','=',0)->get();
                }


                $total = $cart->totalPrice;
                $coupon = Session::has('coupon') ? Session::get('coupon') : 0;
                if($gs->tax != 0)
                {
                    $tax = ($total / 100) * $gs->tax;
                    $total = $total + $tax;
                }
                if(!Session::has('coupon_total'))
                {
                $total = $total - $coupon;     
                $total = $total + 0;               
                }
                else {
                $total = Session::get('coupon_total');  
                $total = $total + round(0 * $curr->value, 2); 
                }
                $ck = 1;
                return view('front.checkout', ['products' => $cart->items, 'totalPrice' => $total, 'pickups' => $pickups, 'totalQty' => $cart->totalQty, 'gateways' => $gateways, 'shipping_cost' => 0, 'checked' => $ck, 'digital' => $dp, 'curr' => $curr,'shipping_data' => $shipping_data,'package_data' => $package_data, 'vendor_shipping_id' => $vendor_shipping_id, 'vendor_packing_id' => $vendor_packing_id]);                 
            }
        }

    }


    public function cashondelivery(Request $request)
    {
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
            }
            else {
                return redirect()->back()->with('unsuccess',"This Email Already Exist.");  
            }
        }
        
        

        if (!Session::has('cart')) {
            return redirect()->route('front.cart')->with('success',"You don't have any product to checkout.");
        }
            if (Session::has('currency')) 
            {
              $curr = Currency::find(Session::get('currency'));
            }
            else
            {
                $curr = Currency::where('is_default','=',1)->first();
            }
        $gs = Generalsetting::findOrFail(1);
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        
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
        $order = new Order;
        $order['customer_state'] = $request->state;
        $order['shipping_state'] = $request->shipping_state;
        $success_url = action('Front\PaymentController@payreturn');
        $item_name = $gs->title." Order";
        $item_number = str_random(4).time();
        $order['user_id'] = Auth::check() ? Auth::user()->id : $request->user_id;
        $order['cart'] = utf8_encode(bzcompress(serialize($cart), 9)); 
        $order['totalQty'] = $request->totalQty;
        $order['pay_amount'] = $request->total / $curr->value;
        $order['method'] = $request->method;
        $order['shipping'] = $request->shipping;
        $order['pickup_location'] = $request->pickup_location;
        $order['customer_email'] = $request->email;
        $order['customer_name'] = $request->name;
        $order['shipping_cost'] = $request->shipping_cost;
        $order['packing_cost'] = $request->packing_cost;
        $order['shipping_title'] = $request->shipping_title;
        $order['packing_title'] = $request->packing_title;
        $order['tax'] = $request->tax;
        $order['customer_phone'] = $request->phone;
        $order['order_number'] = str_random(4).time();
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
        $order['dp'] = $request->dp;
        $order['payment_status'] = "Pending";
        $order['currency_sign'] = $curr->sign;
        $order['currency_value'] = $curr->value;
        $order['vendor_shipping_id'] = $request->vendor_shipping_id;
        $order['vendor_packing_id'] = $request->vendor_packing_id;
        $order['wallet_price'] = round($request->wallet_price / $curr->value, 2);
            if (Session::has('affilate')) 
            {
                $val = $request->total / $curr->value;
                $val = $val / 100;
                $sub = $val * $gs->affilate_charge;
                $order['affilate_user'] = Session::get('affilate');
                $order['affilate_charge'] = $sub;
            }
        $order->save();
        if(Auth::check()){
            Auth::user()->update(['balance' => (Auth::user()->balance - $order->wallet_price)]);
        }
        $track = new OrderTrack;
        $track->title = 'Pending';
        $track->text = 'You have successfully placed your order.';
        $track->order_id = $order->id;
        $track->save();

        $notification = new Notification;
        $notification->order_id = $order->id;
        $notification->save();
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
                }              
            }
        }

        $notf = null;
        $str='';

        foreach($cart->items as $prod)
        {
            if($prod['item']['user_id'] != 0)
            {
                $vorder =  new VendorOrder;
                $vorder->order_id = $order->id;
                $vorder->user_id = $prod['item']['user_id'];
                $vid =  $vorder->user_id;
                $notf[] = $prod['item']['user_id'];
                $str.=$prod['item']['name']."<br>";
                $vorder->qty = $prod['qty'];
                $vorder->price = $prod['price'];
                $vorder->total_price = $sumtotalcartprice;
                $vorder->order_number = $order->order_number;             
                $vorder->save();
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

            Session::put('temporder',$order);
            Session::put('tempcart',$cart);
            Session::forget('cart');
            Session::forget('already');
            Session::forget('coupon');
            Session::forget('coupon_total');
            Session::forget('coupon_total1');
            Session::forget('coupon_percentage');



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

        //Sending Email To Buyer

        // if($gs->is_smtp == 1)
        // {
        // $data = [
        //     'to' => $request->email,
        //     'type' => "new_order",
        //     'cname' => $request->name,
        //     'oamount' => "",
        //     'aname' => "",
        //     'aemail' => "",
        //     'wtitle' => "",
        //     'onumber' => $order->order_number,
        // ];

        // $mailer = new GeniusMailer();
        // $mailer->sendAutoOrderMail($data,$order->id);            
        // }
        // else
        // {

           /*$to = $request->email;
           $subject = "Your Order Placed!!";
           $msg = "Hello ".$request->name."!\nYou have placed a new order.\nYour order number is ".$order->order_number.".Please wait for your delivery. \nThank you.<br><br>All at ProjectShelve <br> Mobile: (+234) 08147801594 <br>Phone: (+234) 08096221646<br>Email: support@projectshelve.com";
           $headers = "MIME-Version: 1.0" . "\r\n";
           $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
           $headers .= "From: ".$gs->from_name."<".$gs->from_email.">";
           // mail($to,$subject,$msg,$headers);
           Mail::send(array(), array(), function ($message) use ($msg,$to,$subject,$headers) {
                              $message->to($to)
                             ->subject($subject)
                              ->setBody($msg,'text/html');
                            });              */

        // }
        //Sending Email To Admin
        // if($gs->is_smtp == 1)
        // {
        //     $data = [
        //         'to' => Pagesetting::find(1)->contact_email,
        //         'subject' => "New Order Recieved!!",
        //         'body' => "Hello Admin!<br>Your store has received a new order.<br>Order Number is ".$order->order_number.".Please login to your panel to check. <br>Thank you.<br><br>All at ProjectShelve <br> Mobile: (+234) 08147801594 <br>Phone: (+234) 08096221646<br>Email: support@projectshelve.com",
        //     ];

        //     $mailer = new GeniusMailer();
        //     $mailer->sendCustomMail($data);            
        // }
        // else
        // {
           $to = Pagesetting::find(1)->contact_email;
           $subject = "New Order Recieved!!";
           $msg = "Hello Admin!\nYour store has recieved a new order.\nOrder Number is ".$order->order_number.".Please login to your panel to check. \nThank you.<br><br>All at ProjectShelve <br> Mobile: (+234) 08147801594 <br>Phone: (+234) 08096221646<br>Email: support@projectshelve.com";
           $headers = "MIME-Version: 1.0" . "\r\n";
           $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
           $headers .= "From: ".$gs->from_name."<".$gs->from_email.">";
           // mail($to,$subject,$msg,$headers);
           Mail::send(array(), array(), function ($message) use ($msg,$to,$subject,$headers) {
                              $message->to($to)
                             ->subject($subject)
                              ->setBody($msg,'text/html');
                            });  
        // }

        return redirect($success_url);
    }

    public function gateway(Request $request)
    {

        $input = $request->all();
        
        
        
        $rules = [
            //'txn_id4' => 'required',
        ];
        
        
        $messages = [
            'required' => 'The Transaction ID field is required.',
        ];
        
        $validator = Validator::make($input, $rules, $messages);

       if ($validator->fails()) {
            Session::flash('unsuccess', $validator->messages()->first());
            return redirect()->back()->withInput();
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
            }
            else {
                return redirect()->back()->with('unsuccess',"This Email Already Exist.");  
            }
        }

        $gs = Generalsetting::findOrFail(1);
        if (!Session::has('cart')) {
            return redirect()->route('front.cart')->with('success',"You don't have any product to checkout.");
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
        $order = new Order;
        $order['customer_state'] = $request->state;
        $order['shipping_state'] = $request->shipping_state;
        $success_url = action('Front\PaymentController@payreturn');
        $item_name = $settings->title." Order";
        $item_number = str_random(4).time();
        $order['user_id'] = Auth::check() ? Auth::user()->id : $request->user_id;
        $order['cart'] = utf8_encode(bzcompress(serialize($cart), 9));
        $order['totalQty'] = $request->totalQty;
        $order['pay_amount'] = $request->total / $curr->value;
        $order['method'] = $request->method;
        $order['shipping'] = $request->shipping;
        $order['pickup_location'] = $request->pickup_location;
        $order['customer_email'] = $request->email;
        $order['customer_name'] = $request->name;
        $order['shipping_cost'] = $request->shipping_cost;
        $order['packing_cost'] = $request->packing_cost;
        $order['shipping_title'] = $request->shipping_title;
        $order['packing_title'] = $request->packing_title;
        $order['tax'] = $request->tax;
        $order['customer_phone'] = $request->phone;
        $order['order_number'] = str_random(4).time();
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
        $order['txnid'] = $request->txn_id4;
        $order['account_name'] = $request->account_name;
		
		 if ($file = $request->file('screenshot')) {
		
            $name = time().str_replace(' ', '', $file->getClientOriginalName());
            $file->move('assets/order_screenshot',$name);
            $order['screenshot'] = $name;
        }

		
        
        $order['coupon_code'] = $request->coupon_code;
        $order['coupon_discount'] = $request->coupon_discount;
        $order['dp'] = $request->dp;
		if($order['method'] =='Bank Payment' ) {
        $order['payment_status'] = "Pending";
		} else {
			$order['payment_status'] = "Completed";
		}
        $order['currency_sign'] = $curr->sign;
        $order['currency_value'] = $curr->value;
        $order['vendor_shipping_id'] = $request->vendor_shipping_id;
        $order['vendor_packing_id'] = $request->vendor_packing_id;  
        $order['wallet_price'] = round($request->wallet_price / $curr->value, 2);     
        if (Session::has('affilate')) 
        {
            $val = $request->total / $curr->value;
            $val = $val / 100;
            $sub = $val * $gs->affilate_charge;
            $order['affilate_user'] = Session::get('affilate');
            $order['affilate_charge'] = $sub;
        }
        $order->save();
        if(Auth::check()){
            Auth::user()->update(['balance' => (Auth::user()->balance - $order->wallet_price)]);
        }
        $track = new OrderTrack;
        $track->title = 'Pending';
        $track->text = 'You have successfully placed your order.';
        $track->order_id = $order->id;
        $track->save();
        
        $notification = new Notification;
        $notification->order_id = $order->id;
        $notification->save();
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
                }              
            }
        }
        
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
        
        $str = '';

        foreach($cart->items as $prod)
        {
            if($prod['item']['user_id'] != 0)
            {
                $vorder =  new VendorOrder;
                $vorder->order_id = $order->id;
                $vorder->user_id = $prod['item']['user_id'];
                $vid =  $vorder->user_id;
                $notf[] = $prod['item']['user_id'];
                $str.= $prod['item']['name']."<br>";
                $vorder->qty = $prod['qty'];
                $vorder->price = $prod['price'];
                  $vorder->total_price = $sumtotalcartprice;
                $vorder->order_number = $order->order_number;             
                $vorder->save();
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

        Session::put('temporder',$order);
        Session::put('tempcart',$cart);
        Session::forget('cart');
        Session::forget('already');
        Session::forget('coupon');
        Session::forget('coupon_total');
        Session::forget('coupon_total1');
        Session::forget('coupon_percentage');

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

        $all_licence = array();
       if ($order['payment_status'] == 'Completed') {
		  foreach($cart->items as $product) {
			  if($product['license'] !='' ) {
			  array_push($all_licence, $product['license'] );
			  }
		  }
	   }
        //Sending Email To Buyer
        // if($gs->is_smtp == 1)
        // {
        // $data = [
        //     'to' => $request->email,
        //     'type' => "new_order",
        //     'cname' => $request->name,
        //     'oamount' => "",
        //     'aname' => "",
        //     'aemail' => "",
        //     'wtitle' => "",
        //     'onumber' => $order->order_number,
        // ];

        // $mailer = new GeniusMailer();
        // $mailer->sendAutoOrderMail($data,$order->id);            
        // }
        // else
        // {
            
            /*$to = $request->email;
            $subject = "Your Order Placed!!";
            if(count($all_licence) > 0 ) {
            $msg = "Hello ".$request->name."!\nYou have placed a new order.\nYour order number is ".$order->order_number.".Please wait for your delivery. \nThank you.<br>
            License Key :  ".implode (',',$all_licence)."<br>
            <br><br>All at ProjectShelve <br> Mobile: (+234) 08147801594 <br>Phone: (+234) 08096221646<br>Email: support@projectshelve.com";
            } else {
                    $msg = "Hello ".$request->name."!\nYou have placed a new order.\nYour order number is ".$order->order_number.".Please wait for your delivery. \nThank you.<br><br>All at ProjectShelve <br> Mobile: (+234) 08147801594 <br>Phone: (+234) 08096221646<br>Email: support@projectshelve.com";
            }
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: ".$gs->from_name."<".$gs->from_email.">";
            // mail($to,$subject,$msg,$headers); 
            Mail::send(array(), array(), 
                function ($message) 
                use ($msg,$to,$subject,$headers) {
                    $message->to($to)
                    ->subject($subject)
                    ->setBody($msg,'text/html');
                });             
                */
        // }

        //Sending Email To Admin
        // if($gs->is_smtp == 1)
        // {
        //     $data = [
        //         'to' => Pagesetting::find(1)->contact_email,
        //         'subject' => "New Order Recieved!!",
        //         'body' => "Hello Admin!<br>Your store has received a new order.<br>Order Number is ".$order->order_number.".Please login to your panel to check. <br>Thank you.<br><br>All at ProjectShelve <br> Mobile: (+234) 08147801594 <br>Phone: (+234) 08096221646<br>Email: support@projectshelve.com",
        //     ];

        //     $mailer = new GeniusMailer();
        //     $mailer->sendCustomMail($data);            
        // }
        // else
        // {
           $to = Pagesetting::find(1)->contact_email;
           $subject = "New Order Recieved!!";
           $msg = "Hello Admin!\nYour store has recieved a new order.\nOrder Number is ".$order->order_number.".Please login to your panel to check. \nThank you.<br><br>All at ProjectShelve <br> Mobile: (+234) 08147801594 <br>Phone: (+234) 08096221646<br>Email: support@projectshelve.com";
           $headers = "MIME-Version: 1.0" . "\r\n";
           $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
           $headers .= "From: ".$gs->from_name."<".$gs->from_email.">";
           // mail($to,$subject,$msg,$headers);
            Mail::send(array(), array(), function ($message) use ($msg,$to,$subject,$headers) {
                              $message->to($to)
                             ->subject($subject)
                              ->setBody($msg,'text/html');
                            });      
        // }

        return redirect($success_url);
    }


    public function wallet(Request $request)
    {
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
            }
            else {
                return redirect()->back()->with('unsuccess',"This Email Already Exist.");  
            }
        }

        if (!Session::has('cart')) {
            return redirect()->route('front.cart')->with('success',"You don't have any product to checkout.");
        }
        if (Session::has('currency')) 
        {
            $curr = Currency::find(Session::get('currency'));
        }
        else
        {
            $curr = Currency::where('is_default','=',1)->first();
        }
        $gs = Generalsetting::findOrFail(1);
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
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
        $order = new Order;
        $order['customer_state'] = $request->state;
        $order['shipping_state'] = $request->shipping_state;
        $success_url = action('Front\PaymentController@payreturn');
        $item_name = $gs->title." Order";
        $item_number = str_random(4).time();
        $order['user_id'] = Auth::check() ? Auth::user()->id : $request->user_id;
        $order['cart'] = utf8_encode(bzcompress(serialize($cart), 9)); 
        $order['totalQty'] = $request->totalQty;
        $order['pay_amount'] = $request->total / $curr->value;
        $order['method'] = 'Wallet';
        $order['shipping'] = $request->shipping;
        $order['pickup_location'] = $request->pickup_location;
        $order['customer_email'] = $request->email;
        $order['customer_name'] = $request->name;
        $order['shipping_cost'] = $request->shipping_cost;
        $order['packing_cost'] = $request->packing_cost;
        $order['shipping_title'] = $request->shipping_title;
        $order['packing_title'] = $request->packing_title;
        $order['tax'] = $request->tax;
        $order['customer_phone'] = $request->phone;
        $order['order_number'] = str_random(4).time();
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
        $order['dp'] = $request->dp;
        $order['payment_status'] = "Completed";
        $order['currency_sign'] = $curr->sign;
        $order['currency_value'] = $curr->value;
        $order['vendor_shipping_id'] = $request->vendor_shipping_id;
        $order['vendor_packing_id'] = $request->vendor_packing_id;
        $order['wallet_price'] = round($request->wallet_price / $curr->value, 2);
        if($order['dp'] == 1)
        {
            $order['status'] = 'completed';
        }
            if (Session::has('affilate')) 
            {

                $val = $request->total / $curr->value;
                $val = $val / 100;
                $sub = $val * $gs->affilate_charge;
                $user = User::find(Session::get('affilate'));
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

        $track = new OrderTrack;
        $track->title = 'Pending';
        $track->text = 'You have successfully placed your order.';
        $track->order_id = $order->id;
        $track->save();

        $notification = new Notification;
        $notification->order_id = $order->id;
        $notification->save();
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
                }              
            }
        }
        
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
        $str = '';

        foreach($cart->items as $prod)
        {
            if($prod['item']['user_id'] != 0)
            {
                $vorder =  new VendorOrder;
                $vorder->order_id = $order->id;
                $vorder->user_id = $prod['item']['user_id'];
                $vid = $vorder->user_id;
                $notf[] = $prod['item']['user_id'];
                $str.=$prod['item']['name']."<br>";
                $vorder->qty = $prod['qty'];
                $vorder->price = $prod['price'];
                $vorder->total_price = $sumtotalcartprice;
                $vorder->order_number = $order->order_number;             
                $vorder->save();
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

        Session::put('temporder',$order);
        Session::put('tempcart',$cart);

        Session::forget('cart');

            Session::forget('already');
            Session::forget('coupon');
            Session::forget('coupon_total');
            Session::forget('coupon_total1');
            Session::forget('coupon_percentage');


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

        //Sending Email To Buyer

        // if($gs->is_smtp == 1)
        // {
        // $data = [
        //     'to' => $request->email,
        //     'type' => "new_order",
        //     'cname' => $request->name,
        //     'oamount' => "",
        //     'aname' => "",
        //     'aemail' => "",
        //     'wtitle' => "",
        //     'onumber' => $order->order_number,
        // ];

        // $mailer = new GeniusMailer();
        // $mailer->sendAutoOrderMail($data,$order->id);            
        // }
        // else
        // {

           $to = $request->email;
           $subject = "Your Order Placed!!";
           $msg = "Hello ".$request->name."!\nYou have placed a new order.\nYour order number is ".$order->order_number.".Please wait for your delivery. \nThank you.<br><br>All at ProjectShelve <br> Mobile: (+234) 08147801594 <br>Phone: (+234) 08096221646<br>Email: support@projectshelve.com";
           $headers = "MIME-Version: 1.0" . "\r\n";
           $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
           $headers .= "From: ".$gs->from_name."<".$gs->from_email.">";
           // mail($to,$subject,$msg,$headers);            
        // }
        // //Sending Email To Admin
        // if($gs->is_smtp == 1)
        // {
        //     $data = [
        //         'to' => Pagesetting::find(1)->contact_email,
        //         'subject' => "New Order Recieved!!",
        //         'body' => "Hello Admin!<br>Your store has received a new order.<br>Order Number is ".$order->order_number.".Please login to your panel to check. <br>Thank you.<br><br>All at ProjectShelve <br> Mobile: (+234) 08147801594 <br>Phone: (+234) 08096221646<br>Email: support@projectshelve.com",
        //     ];

        //     $mailer = new GeniusMailer();
        //     $mailer->sendCustomMail($data);            
        // }
        // else
        // {
           $to = Pagesetting::find(1)->contact_email;
           $subject = "New Order Recieved!!";
           $msg = "Hello Admin!\nYour store has recieved a new order.\nOrder Number is ".$order->order_number.".Please login to your panel to check. \nThank you.<br><br>All at ProjectShelve <br> Mobile: (+234) 08147801594 <br>Phone: (+234) 08096221646<br>Email: support@projectshelve.com";
           $headers = "MIME-Version: 1.0" . "\r\n";
           $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
           $headers .= "From: ".$gs->from_name."<".$gs->from_email.">";
           // mail($to,$subject,$msg,$headers);
            Mail::send(array(), array(), function ($message) use ($msg,$to,$subject,$headers) {
                              $message->to($to)
                             ->subject($subject)
                              ->setBody($msg,'text/html');
                            });      
        // }/

        return redirect($success_url);
    }


    // Capcha Code Image
    private function  code_image()
    {
        $actual_path = str_replace('project','',base_path());
		$actual_path =  '/home/projectshelvecom/public_html/';
        $image = imagecreatetruecolor(200, 50);
        $background_color = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image,0,0,200,50,$background_color);

        $pixel = imagecolorallocate($image, 0,0,255);
        for($i=0;$i<500;$i++)
        {
            // imagesetpixel($image,rand()%200,rand()%50,$pixel);
        }

        $font = public_path('assets/front/fonts/NotoSans-Bold.ttf');
        $allowed_letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $length = strlen($allowed_letters);
        $letter = $allowed_letters[rand(0, $length-1)];
        $word='';
        //$text_color = imagecolorallocate($image, 8, 186, 239);
        $text_color = imagecolorallocate($image, 0, 0, 0);
        $cap_length=6;// No. of character in image
        for ($i = 0; $i< $cap_length;$i++)
        {
            $letter = $allowed_letters[rand(0, $length-1)];
            // imagettftext($image, 25, 1, 35+($i*25), 35, $text_color, $font, $letter);
            $word.=$letter;
        }
        $pixels = imagecolorallocate($image, 8, 186, 239);
        for($i=0;$i<500;$i++)
        {
            imagesetpixel($image,rand()%200,rand()%50,$pixels);
        }
        session(['captcha_string' => $word]);
        // imagepng($image, public_path("assets/images/capcha_code.png"));
    }

}
