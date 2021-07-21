<?php

namespace App\Http\Controllers\User;

use App\Models\Generalsetting;
use App\Models\User;
use Illuminate\Http\Request;
use App\Classes\GeniusMailer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;

class ForgotController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showForgotForm()
    {
      return view('user.forgot');
    }

    public function forgot(Request $request)
    {
        $gs = Generalsetting::findOrFail(1);
        $input =  $request->all();
        if (User::where('email', '=', $request->email)->count() > 0) {
              // user found
            $admin = User::where('email', '=', $request->email)->firstOrFail();
            $autopass = str_random(8);
            $input['password'] = bcrypt($autopass);
            $admin->update($input);
            $subject = "Reset Password Request";

            $msg = "Your password has been reseted! New password is " . $autopass . " .<br><br>";
            $msg .= "Thanks for your contact and we love to support you anytime.<br><br>";
            $msg .= "All at ProjectShelve<br> ";
            $msg .= "Call/WhatsApp: (+234) 08147801594<br>";
            $msg .= "E-mail: projectshelve@gmail.com<br>";
            $msg .= "Website: www.projectshelve.com<br>";

            if($gs->is_smtp == 1) {
                $data = [
                    'to' => $request->email,
                    'subject' => $subject,
                    'body' => $msg,
                ];
    
                $mailer = new GeniusMailer();
                
                $mailer->sendCustomMail($data);  
                  
            } else {	
                
                $to = $request->email;

                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: ProjectShelve <projectshelve@gmail.com>";
                                  
                // mail($request->email,$subject,$msg,$headers);            
                Mail::send(array(), array(), function ($message) use ($msg, $to, $subject, $headers) {
                    $message->to($to)
                    ->subject($subject)
                    ->setBody($msg,'text/html');
                });
            }
              
            return response()->json('Your Password Reseted Successfully. Please Check your email for new Password.');

        } else {
        // user not found
            return response()->json(array('errors' => [ 0 => 'No Account Found With This Email.' ]));    
        }  
    }

}
