<?php
/**
 * Created by PhpStorm.
 * User: ShaOn
 * Date: 11/29/2018
 * Time: 12:49 AM
 */

namespace App\Classes;

use App\Models\EmailTemplate;
use App\Models\Generalsetting;
use App\Models\Order;
use PDF;
use Illuminate\Support\Facades\Mail;
use Config;

class GeniusMailer
{

    public function __construct()
    {
        $gs = Generalsetting::findOrFail(1);
        Config::set('mail.driver', $gs->mail_engine);
        Config::set('mail.host', $gs->smtp_host);
        Config::set('mail.port', $gs->smtp_port);
        Config::set('mail.encryption', $gs->email_encryption);
        Config::set('mail.username', $gs->smtp_user);
        Config::set('mail.password', $gs->smtp_pass);
    }

    public function sendAutoOrderMail(array $mailData,$id)
    {
        $setup = Generalsetting::find(1);
        $temp = EmailTemplate::where('email_type','=',$mailData['type'])->first();

        $body = preg_replace("/{customer_name}/", $mailData['cname'] ,$temp->email_body);
        $body = preg_replace("/{order_amount}/", $mailData['oamount'] ,$body);
        $body = preg_replace("/{admin_name}/", $mailData['aname'] ,$body);
        $body = preg_replace("/{admin_email}/", $mailData['aemail'] ,$body);
        $body = preg_replace("/{order_number}/", $mailData['onumber'] ,$body);
        $body = preg_replace("/{website_title}/", $setup->title ,$body);

        $data = [
            'email_body' => $body
        ];

        $objDemo = new \stdClass();
        $objDemo->to = $mailData['to'];
        $objDemo->from = $setup->from_email;
        $objDemo->title = $setup->from_name;
        $objDemo->subject = $temp->email_subject;

        try{
            Mail::send('admin.email.mailbody',$data, function ($message) use ($objDemo,$id) {
                $message->from($objDemo->from,$objDemo->title);
                $message->to($objDemo->to);
                $message->subject($objDemo->subject);
                $order = Order::findOrFail($id);
                $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
                $fileName = public_path('assets/temp_files/').str_random(4).time().'.pdf';
                $pdf = PDF::loadView('print.order', compact('order', 'cart'))->save($fileName);
                $message->attach($fileName);
            });

            $files = glob('assets/temp_files/*'); //get all file names
            foreach($files as $file){
                if(is_file($file))
                unlink($file); //delete file
            }

        }
        catch (\Exception $e){
             //die($e->getMessage());
        }


    }

    public function sendAutoMail(array $mailData)
    {
        $setup = Generalsetting::find(1);

        $temp = EmailTemplate::where('email_type','=',$mailData['type'])->first();
        $body = preg_replace("/{customer_name}/", $mailData['cname'] ,$temp->email_body);
        $body = preg_replace("/{order_amount}/", $mailData['oamount'] ,$body);
        $body = preg_replace("/{admin_name}/", $mailData['aname'] ,$body);
        $body = preg_replace("/{admin_email}/", $mailData['aemail'] ,$body);
        $body = preg_replace("/{order_number}/", $mailData['onumber'] ,$body);
        if (!empty($mailData['damount'])) {
            $body = preg_replace("/{deposit_amount}/", $mailData['damount'] ,$body);
          }
          if (!empty($mailData['wbalance'])) {
            $body = preg_replace("/{wallet_balance}/", $mailData['wbalance'] ,$body);
          }
        $body = preg_replace("/{website_title}/", $setup->title ,$body);

        $data = [
            'email_body' => $body
        ];

        $objDemo = new \stdClass();
        $objDemo->to = $mailData['to'];
        $objDemo->from = $setup->from_email;
        $objDemo->title = $setup->from_name;
        $objDemo->subject = $temp->email_subject;

        try{
            Mail::send('admin.email.mailbody',$data, function ($message) use ($objDemo) {
                $message->from($objDemo->from,$objDemo->title);
                $message->to($objDemo->to);
                $message->subject($objDemo->subject);
            });
        }
        catch (\Exception $e){
            // die($e->getMessage());
        }
    }

    public function sendCustomMail(array $mailData)
    {
        $setup = Generalsetting::find(1);

        $data = [
            'email_body' => $mailData['body']
        ];

        $objDemo = new \stdClass();
        $objDemo->to = $mailData['to'];
        $objDemo->from = $setup->from_email;
        $objDemo->title = $setup->from_name;
        $objDemo->subject = $mailData['subject'];

        try{
            Mail::send('admin.email.mailbody',$data, function ($message) use ($objDemo) {
                $message->from($objDemo->from,$objDemo->title);
                $message->to($objDemo->to);
                $message->subject($objDemo->subject);
            });
        }
        catch (\Exception $e){
            //die($e->getMessage());
            // return $e->getMessage();
        }
        return true;
    }

    public function sendWhatsAppMsg($message, $sendto) 
    {
        $breaks = array("<br />","<br>","<br/>");  		
        $whatsapp_message = str_ireplace($breaks, "\r\n", $message);  		
        $curl = curl_init();		
        $post_fields = json_encode(array("to_number" => $sendto, "type" => "text", "message" => $whatsapp_message));		
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.maytapi.com/api/60c7127c-6764-43f5-9df4-a50bda8532a0/6745/sendMessage",			
            CURLOPT_RETURNTRANSFER => true,			
            CURLOPT_FOLLOWLOCATION => true,			
            CURLOPT_ENCODING => "",			
            CURLOPT_MAXREDIRS => 10,			
            CURLOPT_TIMEOUT => 30,			
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,			
            CURLOPT_CUSTOMREQUEST => "POST",			
            CURLOPT_POSTFIELDS => $post_fields,			
            CURLOPT_HTTPHEADER => [	
                "content-type: application/json",				
                "x-maytapi-key: 5c3f8212-1e11-4175-9bdf-517b8eed91a3",				
                "x-rapidapi-key: e636feb415msh4912a153dcf90c5p1611f3jsn90e62ca36691"
            ]
        ]);		
        
        $response = curl_exec($curl);		
        $err = curl_error($curl);		
        curl_close($curl);

        if ($err) {		

            $api_res =  "cURL Error #:" . $err;		

        } else {	
            		 
            $api_res =  $response;		
        }	
    }
}