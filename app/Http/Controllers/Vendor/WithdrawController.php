<?php

namespace App\Http\Controllers\Vendor;

use App\Models\User;
use App\Models\Withdraw;
use App\Models\Generalsetting;
use Auth;
use App\Classes\GeniusMailer;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mail;
class WithdrawController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

  	public function index()
    {
        $withdraws = Withdraw::where('user_id','=',Auth::guard('web')->user()->id)->where('type','=','vendor')->orderBy('id','desc')->get();
        $sign = Currency::where('is_default','=',1)->first();        
        return view('vendor.withdraw.index',compact('withdraws','sign'));
    }


    public function create()
    {
        $sign = Currency::where('is_default','=',1)->first();
        return view('vendor.withdraw.create' ,compact('sign'));
    }


    public function store(Request $request)
    {
        $from = User::findOrFail(Auth::guard('web')->user()->id);
        $curr = Currency::where('is_default','=',1)->first(); 
        $withdrawcharge = Generalsetting::findOrFail(1);
        $charge = $withdrawcharge->withdraw_fee;

        if($request->amount > 0){

            $amount = $request->amount;
            $amount = $amount / $curr->value;
            
            if (number_format($from->current_balance, 2) >= $amount){
                $fee = (($amount * $withdrawcharge->withdraw_charge) / 100 ) + $charge;
                $finalamount = $amount - $fee;

                $from->current_balance = $from->current_balance - $amount;
                $from->update();

                $newwithdraw = new Withdraw();
                $newwithdraw['user_id'] = Auth::user()->id;
                $newwithdraw['method'] = $request->methods;
                $newwithdraw['acc_email'] = $request->acc_email;
                $newwithdraw['iban'] = $request->iban;
                $newwithdraw['country'] = $request->acc_country;
                $newwithdraw['acc_name'] = $request->acc_name;
                $newwithdraw['address'] = $request->address;
                $newwithdraw['swift'] = $request->swift;
                $newwithdraw['reference'] = $request->reference;
                $newwithdraw['bank_name'] = $request->bankname;
                $newwithdraw['amount'] = (float) $finalamount;
                $newwithdraw['fee'] = (float) $fee;
                $newwithdraw['type'] = 'vendor';
                $newwithdraw->save();

                $to = $from->email;

                $subject = 'Withdrawal Request';
    
                $total_amount = $request->amount;
    
                $msg = "Hello " . $from->shop_name . ",<br><br>";
                $msg .= "Your withdrawal request of ".$total_amount." has been received. You will receive this amount less transaction fee within 24 hrs.<br><br>";
                $msg .= "Thank you as we look forward for a mutual advantage.<br><br>";
                $msg .= "All at ProjectShelve<br> ";
                $msg .= "Call/WhatsApp: (+234) 08147801594<br>";
                $msg .= "E-mail: info@projectshelve.com<br>";
                $msg .= "Website: www.projectshelve.com<br>";
    
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: ProjectShelve <info@projectshelve.com>";
    
                if ($withdrawcharge->is_smtp == 1) {
    
                    $mailer = new GeniusMailer();
                    $mailer->sendCustomMail([
                        'to' => $to,
                        'subject' => $subject,
                        'body' => $msg
                    ]);
    
                } else {
                    $sent =   Mail::send(array(), array(), function ($message) use ($msg,$to,$subject,$headers) {
                        $message->to($to)
                        ->subject($subject)
                        ->setBody($msg,'text/html');
                    });  
                    // mail($to, $subject, $msg, $headers);
                }

                return response()->json('Withdraw Request Sent Successfully.'); 

            } else {
                
                return response()->json(array('errors' => [ 0 => 'Insufficient Balance.' ])); 
            }
        }
        
        return response()->json(array('errors' => [ 0 => 'Please enter a valid amount.' ]));
    }
}
