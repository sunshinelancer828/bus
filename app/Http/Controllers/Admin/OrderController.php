<?php

namespace App\Http\Controllers\Admin;

use App\Classes\GeniusMailer;
use App\Http\Controllers\Controller;
use App\Models\Generalsetting;
use App\Models\Order;
use App\Models\OrderTrack;
use App\Models\User;
use App\Models\VendorOrder;
use App\Models\Product;
use Datatables;
use App\Models\Currency;
use Illuminate\Http\Request;
use Session,Mail;
use DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    //*** JSON Request
    public function datatables($status)
    {
        if($status == 'pending'){
            $datas = Order::where('status','=','pending')->get()->sortByDesc('id');
        }
        elseif($status == 'processing') {
            $datas = Order::where('status','=','processing')->get()->sortByDesc('id');
        }
        elseif($status == 'completed') {
            $datas = Order::where('status','=','completed')->get()->sortByDesc('id');
        }
        elseif($status == 'declined') {
            $datas = Order::where('status','=','declined')->get()->sortByDesc('id');
        }
        else{
          $datas = Order::orderBy('id','desc')->get();  
        }
         
         //--- Integrating This Collection Into Datatables
         return Datatables::of($datas)
                            ->editColumn('id', function(Order $data) {
                                $id = '<a href="'.route('admin-order-invoice',$data->id).'">'.$data->order_number.'</a>';
                                return $id;
                            })
                            ->editColumn('pay_amount', function (Order $data) {
                                return $data->currency_sign . round($data->pay_amount * $data->currency_value, 2);
                            })
                            ->addColumn('action', function(Order $data) {
                                $orders = '<a href="javascript:;" data-href="'. route('admin-order-edit',$data->id) .'" class="delivery" data-toggle="modal" data-target="#modal1"><i class="fas fa-dollar-sign"></i> Delivery Status</a>';
                                return '<div class="godropdown"><button class="go-dropdown-toggle"> Actions<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-order-show',$data->id) . '" > <i class="fas fa-eye"></i> Details</a><a href="javascript:;" class="send" data-email="'. $data->customer_email .'" data-toggle="modal" data-target="#vendorform"><i class="fas fa-envelope"></i> Send</a><a href="javascript:;" data-href="'. route('admin-order-track',$data->id) .'" class="track" data-toggle="modal" data-target="#modal1"><i class="fas fa-truck"></i> Track Order</a>'.$orders.'</div></div>';
                            }) 
                            ->rawColumns(['id','action'])
                            ->toJson(); //--- Returning Json Data To Client Side
    }
    public function index()
    {
        return view('admin.order.index');
    }

    public function edit($id)
    {
        $data = Order::find($id);
        return view('admin.order.delivery',compact('data'));
    }

    public function update(Request $request, $id)
    { 
        $input = $request->all();
       
        $data = Order::findOrFail($id);

        $gs = Generalsetting::findOrFail(1);

        $str = ''; $str2 = ''; $mstr = ''; $vid = 0;

        $cart = unserialize(bzdecompress(utf8_decode($data->cart)));

        foreach ($cart->items as $product) {
            
			$dataFormat = DB::table('products')->where('id','=',$product['item']['id'])->get();
			if(is_array($dataFormat)){
			    $dataFormat = $dataFormat[0]->file_format;
			}else{
			    $dataFormat = '';
			}
			
            $str .= "Find below your ".$product['item']['name'].".<br><br>";
            $str .= "Product Title: ".$product['item']['name']."<br>";
        	$str .= "Product Code: 000".$product['item']['id']."<br>";
        	$str .= "Price:" .Product::convertPrice($product['item_price'])."<br>";
        	if($product['item']['user_id'] != 0){
        	    $mstr .= $product['item']['name'].'<br>';
        	    $vid = $product['item']['user_id'];
        	}
			if($dataFormat) {
        	    $str .= "Format: ".$dataFormat."<br>";
            }
            $str2 .= $str;
            $str2 .= 'Download Link: '.asset('assets/files/'.$product['item']['file']).'<br><br>';
            // $str .= 'Download Link: <a href="'.asset('assets/files/'.$product['item']['file']).'" target="_blank">Click here</a><br><br>';
            $str .= "PIN: ".$product['license']."<br><br>";
        }

        $msg  = "Hello ".$data->customer_name.",<br><br>";
        $msg .= $str."<br>";
        $msg .= "Thank you for trusting us. We look forward to your next visit.<br><br>";
        $msg .= "All at ProjectShelve <br>";
        $msg .= "Call/WhatsApp: (+234) 08147801594<br>";
        $msg .= "E-mail: projectshelve@gmail.com<br>";
        $msg .= "Website: www.projectshelve.com<br>";
       
        if ($data->status == "completed") {

            $input['status'] = "completed";

            $data->update($input);  

            return response()->json('Status Updated Successfully.');   

        } else {

            if ($input['status'] == "completed") {
    
                foreach($data->vendororders as $vorder) {
                    $uprice = User::findOrFail($vorder->user_id);
                    $uprice->current_balance = $uprice->current_balance + $vorder->total_price;
                    $uprice->update();
                    $vid = $vorder->user_id;  
                }

                if (User::where('id', $data->affilate_user)->exists()){
                    $auser = User::where('id', $data->affilate_user)->first();
                    $auser->affilate_income += $data->affilate_charge;
                    $auser->update();
                }

                $all_licence = [];
                
                foreach($cart->items as $product) {
					if($product['license'] !='' ) {
					    array_push($all_licence, $product['license'] );
					}
				}
			 
                $to = $data->customer_email;

                $subject = 'Product Delivered';

				if (count($all_licence) > 0 ) {

                    $msg1 = $msg; // "Hello ".$data->customer_name.","."\n Thank you for shopping with us. We are looking forward to your next visit.<br>License Key :  ".implode (',',$all_licence)."<br><br><br>All at ProjectShelve<br>Mobile: (+234) 08147801594<br>Phone: (+234) 08096221646<br>Email: support@projectshelve.com";
                
                } else {

					$msg1 = $msg; // "Hello ".$data->customer_name.",<br>"."\n Thank you for your interest in service. Below is an overview of your order:<br><br>Overview:<br>Product Title:".$cart['name']."<br>Product Code:000".$cart['id']." <br><br>We are looking forward to your next visit.<br><br>All at ProjectShelve<br>Mobile: (+234) 08147801594<br>Phone: (+234) 08096221646<br>Email: support@projectshelve.com";  
                }
                
                $headers  = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From: ProjectShelve <projectshelve@gmail.com>' . "\r\n" .
                    'Reply-To: projectshelve@gmail.com ' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();

                if ($gs->is_smtp == 1) {

                    $mailer = new GeniusMailer();
                    $mailer->sendCustomMail([
                        'to' => $to,
                        'subject' => $subject,
                        'body' => $msg1
                    ]);

                } else {
                    // mail($to, $subject, $msg1, $headers);
                    $sent =   Mail::send(array(), array(), function ($message) use ($msg1, $to, $subject, $headers) {
                        $message->to($to)
                        ->subject($subject)
                        ->setBody($msg1,'text/html')
                        ->getHeaders()
                        ->addTextHeader($headers, 'true');
                    });    
                }
        
                if (!empty($vid)) {
                    
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

                    if ($gs->is_smtp == 1) {

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

                $msg3  = "Hello ".$data->customer_name.",<br><br>";
                $msg3 .= "Click link below to download your product.<br><br>";
                $msg3 .= $str2."<br>";
                $msg3 .= "Thank you for trusting us. We look forward to your next visit.<br><br>";
                $msg3 .= "All at ProjectShelve <br>";
                $msg3 .= "Call/WhatsApp: (+234) 08147801594<br>";
                $msg3 .= "E-mail: projectshelve@gmail.com<br>";
                $msg3 .= "Website: www.projectshelve.com<br>";
				  
                $phone_number = $data->customer_phone;
                
                $userReg = User::where('phone', $phone_number)->count();
                
                if ($userReg) {
                    $mailer = new GeniusMailer();
                    $mailer->sendWhatsAppMsg($msg3, $phone_number);	
                }
            }

            if ($input['status'] == "declined") {

                if ($data->user_id != 0) {

                    if ($data->wallet_price != 0) {

                        $user = User::find($data->user_id);

                        if ( $user ) {
                            $user->balance = $user->balance + $data->wallet_price;
                            $user->save();
                        }
                    }
                }

                foreach($cart->items as $prod) {

                    $x = (string)$prod['stock'];

                    if ($x != null) {
        
                        $product = Product::findOrFail($prod['item']['id']);
                        $product->stock = $product->stock + $prod['qty'];
                        $product->update();               
                    }
                }

                foreach($cart->items as $prod) {

                    $x = (string)$prod['size_qty'];

                    if (!empty($x)) {

                        $product = Product::findOrFail($prod['item']['id']);
                        $x = (int)$x;
                        $temp = $product->size_qty;
                        $temp[$prod['size_key']] = $x;
                        $temp1 = implode(',', $temp);
                        $product->size_qty =  $temp1;
                        $product->update();               
                    }
                }

                $to = $data->customer_email;

                $subject = 'Your order '.$data->order_number.' is Declined!';

                $msg = "Hello ".$data->customer_name.","."\n We are sorry for the inconvenience caused. We are looking forward to your next visit.<br><br>All at ProjectShelve<br>Mobile: (+234) 08147801594<br>Phone: (+234) 08096221646<br>Email: support@projectshelve.com";

                // if ($gs->is_smtp == 1) {

                //     $maildata = [
                //         'to' => $to,
                //         'subject' => $subject,
                //         'body' => $msg
                //     ];

                //     $mailer = new GeniusMailer();
                //     $mailer->sendCustomMail($maildata);

                // } else {

                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= "From: ".$gs->from_name."<".$gs->from_email.">";

                    Mail::send(array(), array(), function ($message) use ($msg,$to,$subject,$headers) {
                        $message->to($to)
                        ->subject($subject)
                        ->setBody($msg,'text/html');
                    });      
                   // Mail::send($to, $subject, $msg, $headers);
                // }
            }

            $data->update($input);

            if ($request->track_text) {

                $title = ucwords($request->status);
                $ck = OrderTrack::where('order_id', '=', $id)->where('title', '=', $title)->first();
                
                if ($ck) { 

                    $ck->order_id = $id;
                    $ck->title = $title;
                    $ck->text = $request->track_text;
                    $ck->update();   

                } else {

                    $data = new OrderTrack;
                    $data->order_id = $id;
                    $data->title = $title;
                    $data->text = $request->track_text;
                    $data->save();            
                }
            } 

            $order = VendorOrder::where('order_id', '=', $id)->update(['status' => $input['status']]);   
            
            return response()->json('Status Updated Successfully.');    
        }
        
        return response()->json('Status Updated Successfully.');
    }

    public function pending()
    {
        return view('admin.order.pending');
    }
    public function processing()
    {
        return view('admin.order.processing');
    }
    public function completed()
    {
        return view('admin.order.completed');
    }
    public function declined()
    {
        return view('admin.order.declined');
    }
    public function show($id)
    {
        $order = Order::findOrFail($id);
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        return view('admin.order.details',compact('order','cart'));
    }
    public function invoice($id)
    {
        $order = Order::findOrFail($id);
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        return view('admin.order.invoice',compact('order','cart'));
    }
    public function emailsub(Request $request)
    {
        $gs = Generalsetting::findOrFail(1);
        //   if($gs->is_smtp == 1)
        // {
        //     $data = 0;
        //     $datas = [
        //             'to' => $request->to,
        //             'subject' => $request->subject,
        //             'body' => $request->message,
        //     ];
             
        //     $mailer = new GeniusMailer();
        //     $mail = $mailer->sendCustomMail($datas);
        //     if($mail) {
        //         $data = 1;
        //     }
        // }
        // else
        // {
            $data = 0;
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: ".$gs->from_name."<".$gs->from_email.">";
            $mail = mail($request->to,$request->subject,$request->message,$headers);
            $msg =$request->message;
            $to =$request->to;
            $subject =$request->subject;

            $mail =   Mail::send(array(), array(), function ($message) use ($msg,$headers,$to,$subject) {
                              $message->to($to)
                             ->subject($subject)
                              ->setBody($msg);
                            }); 

            if($mail==null) {
                $data = 1;
            }
        // }

        return response()->json($data);
    }

    public function printpage($id)
    {
        $order = Order::findOrFail($id);
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        return view('admin.order.print',compact('order','cart'));
    }

    public function license(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        $cart->items[$request->license_key]['license'] = $request->license;
        $order->cart = utf8_encode(bzcompress(serialize($cart), 9));
        $order->update();       
        $msg = 'Successfully Changed The License Key.';
        return response()->json($msg);
    }

    public function status($id,$status)
    {
        $mainorder = Order::findOrFail($id);

    }
}