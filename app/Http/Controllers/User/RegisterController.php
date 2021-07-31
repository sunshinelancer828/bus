<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Generalsetting;
use App\Models\User;
use App\Classes\GeniusMailer;
use App\Models\Notification;
use Auth;
use Validator;
use Session;
use Mail;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
      // $this->code_image();
      return view('user.register');
    }

    public function register(Request $request)
    {
          
    	$gs = Generalsetting::findOrFail(1);
    	if($gs->is_capcha == 1)
    	{
	        $value = session('captcha_string');
	        // dd($value,$request->codes);
	        if ($request->codes != $value){
	            return response()->json(array('errors' => [ 0 => 'Please enter Correct Capcha Code.' ]));    
	        }    		
    	}

        //--- Validation Section
        $rules = [
			'email'   => 'required|email|unique:users',
			'password' => 'required|confirmed',
			'phone'=>'required',
			'terms_and_condition'=>'required'
        ];
        $validator = Validator::make(Input::all(), $rules);
        
        if ($validator->fails()) {
			return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }        
		//--- Validation Section Ends

		$user = new User;
		$input = $request->all();        
		$input['password'] = bcrypt($request['password']);
		$token = md5(time().$request->name.$request->email);
		$input['verification_link'] = $token;
		$input['affilate_code'] = md5($request->name.$request->email);

		if(!empty($request->vendor))
		{
			//--- Validation Section
			$rules = [
				'shop_name' => 'unique:users',
				'shop_number'  => 'max:10'
					];
			$customs = [
				'shop_name.unique' => 'This Shop Name has already been taken.',
				'shop_number.max'  => 'Shop Number Must Be Less Then 10 Digit.'
			];

			$validator = Validator::make(Input::all(), $rules, $customs);
			if ($validator->fails()) {
				return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
			}

			$input['is_vendor'] = 2;
		}
			  
		$user->fill($input)->save();

		if($gs->is_verification_email == 1)
		{
	        $to = $request->email;
	        $subject = 'Verify your email address.';
	        $msg = "Dear Customer,<br> We noticed that you need to verify your email address. <a href=".url('user/register/verify/'.$token).">Simply click here to verify. </a>";
	        //Sending Email To Customer
	        if($gs->is_smtp == 1)
	        {
				$data = [
					'to' => $to,
					'subject' => $subject,
					'body' => $msg,
				];
				$mailer = new GeniusMailer();
				$mailer->sendCustomMail($data);

	        } else {
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				$headers .= "From: ".$gs->from_name."<".$gs->from_email.">";

				// mail($to,$subject,$msg,$headers);
				$sent =   Mail::send(array(), array(), function ($message) use ($msg,$to,$subject,$headers) {
					$message->to($to)
					->subject($subject)
					->setBody($msg,'text/html');
				});  
	        }

          	return response()->json('We need to verify your email address. We have sent an email to '.$to.' to verify your email address. Please click link in that email to continue.');

		} else {

			$to = $request->email;
			$pwd = $request['password'];

			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= "From: ".$gs->from_name."<".$gs->from_email.">";
			
			$subject = 'Registered Successfully.';

			$msg = "Hello ". empty($request->vendor) ? $request->name : $request->shop_name;
			$msg .= ",<br><br>You have successfully registered to ProjectShelve.com.";
			$msg .= "We wish you have a wonderful experience using our service. <br>";
			$msg .= "Below is your login details. <br>";
			$msg .= "Email: " . $to . "<br>";
			$msg .= "Password: " . $pwd . "<br><br>";
			$msg .= "Thank you.<br><br>";

			$msg .= "All at ProjectShelve<br> ";
			$msg .= "Call/WhatsApp: (+234) 08147801594<br>";
			$msg .= "E-mail: projectshelve@gmail.com<br>";
			$msg .= "Website: www.projectshelve.com<br>";

			$sent =   Mail::send(array(), array(), function ($message) use ($msg,$to,$subject,$headers) {
							$message->to($to)
							->subject($subject)
							->setBody($msg,'text/html');
							});

			$user->email_verified = 'Yes';
			$user->update();

			$notification = new Notification;
			$notification->user_id = $user->id;
			$notification->save();

			Auth::guard('web')->login($user); 

			if (Auth::guard('web')->user()->is_vendor == 2) {
				return response()->json(2);
			}

			return response()->json(1);
		}

    }

    public function token($token)
    {
        $gs = Generalsetting::findOrFail(1);

        if($gs->is_verification_email == 1) {    	

			$user = User::where('verification_link','=',$token)->first();

			if(isset($user))
			{
				$user->email_verified = 'Yes';
				$user->update();
				$notification = new Notification;
				$notification->user_id = $user->id;
				$notification->save();
				Auth::guard('web')->login($user); 
				return redirect()->route('user-dashboard')->with('success','Email Verified Successfully');
			}

		} else {

			return redirect()->back();	
		}
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
            imagesetpixel($image,rand()%200,rand()%50,$pixel);
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
            imagettftext($image, 25, 1, 35+($i*25), 35, $text_color, $font, $letter);
            $word.=$letter;
        }

        $pixels = imagecolorallocate($image, 8, 186, 239);

        for($i=0;$i<500;$i++)
        {
            imagesetpixel($image,rand()%200,rand()%50,$pixels);
        }

        session(['captcha_string' => $word]);
        
        imagepng($image, public_path("assets/images/capcha_code.png"));
    }
}