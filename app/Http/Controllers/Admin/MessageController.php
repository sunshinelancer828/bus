<?php

namespace App\Http\Controllers\Admin;

use App\Classes\GeniusMailer;
use Datatables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;
use App\Models\AdminUserConversation;
use App\Models\AdminUserMessage;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\Generalsetting;
use Auth;
use DB,Mail;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    //*** JSON Request
    public function datatables($type)
    {
        if($type == 'Message'){
            
          $datas = DB::table('conversations')->orderBy('created_at', 'DESC')->get();
          foreach($datas as $k => $v){
              
            $name = DB::table('users')->where('id','=',$v->sent_user)->pluck('name');
            $datas1[$k]['name'] = $name[0];
            $datas1[$k]['id'] = $v->id;
            $datas1[$k]['subject'] = $v->subject;
            $datas1[$k]['sent_user'] = $v->sent_user;
            $datas1[$k]['recieved_user'] = $v->recieved_user;
            $datas1[$k]['created_at'] = $v->created_at;
            $datas1[$k]['message'] = $v->message;
          }
          
         
           return Datatables::of($datas1)->toJson();
                         
        } else {
            
             $datas = AdminUserConversation::where('type','=',$type)->orderBy('created_at', 'DESC')->get();
            
            return Datatables::of($datas)
                            ->editColumn('created_at', function(AdminUserConversation $data) {
                                $date = $data->created_at->diffForHumans();
                                return  $date;
                            })
                            ->addColumn('name', function(AdminUserConversation $data) {
                                $name = $data->user->name;
                                return  $name;
                            })
                            ->addColumn('action', function(AdminUserConversation $data) {
                                return '<div class="action-list"><a href="' . route('admin-message-show',$data->id) . '"> <i class="fas fa-eye"></i> Details</a><a href="javascript:;" data-href="' . route('admin-message-delete',$data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i></a></div>';
                            }) 
                            ->rawColumns(['action'])
                            ->toJson(); //--- Returning Json Data To Client Side
                            
        }
    }
    

    //*** GET Request
    public function index()
    {
        return view('admin.message.index');            
    }
    
     //*** GET Request
    public function messages()
    {
        return view('admin.message.message');            
    }

    //*** GET Request
    public function disputes()
    {
        return view('admin.message.dispute');            
    }

    //*** GET Request
    public function message($id)
    {
        $conv = AdminUserConversation::findOrfail($id);
        return view('admin.message.create',compact('conv'));                 
    }   

    //*** GET Request
    public function messageshow($id)
    {
        $conv = AdminUserConversation::findOrfail($id);
        return view('load.message',compact('conv'));                 
    }   

    //*** GET Request
    public function messagedelete($id)
    {
        $conv = AdminUserConversation::findOrfail($id);
        if($conv->messages->count() > 0)
         {
           foreach ($conv->messages as $key) {
            $key->delete();
            }
         }
          $conv->delete();
        //--- Redirect Section     
        $msg = 'Data Deleted Successfully.';
        return response()->json($msg);      
        //--- Redirect Section Ends               
    }

    //*** POST Request
    public function postmessage(Request $request)
    {
        $msg = new AdminUserMessage();
        $input = $request->all();  
        $msg->fill($input)->save();
        //--- Redirect Section     
        $msg = 'Message Sent!';
        return response()->json($msg);      
        //--- Redirect Section Ends    
    }

    //*** POST Request
    public function usercontact(Request $request)
    {
        $data = 1;
        $admin = Auth::guard('admin')->user();
        $user = User::where('email','=',$request->to)->first();
        if(empty($user))
        {
            $data = 0;
            return response()->json($data);   
        }
        $to = $request->to;
        $subject = $request->subject;
        $from = $admin->email;

        $msg = "Hello " . $user->name . ", <br><br>";
        $msg .= $request->message;
        $msg .= "<br><br>";
        $msg .="All at ProjectShelve<br> ";
        $msg .="Call/WhatsApp: (+234) 08147801594<br> ";
        $msg .="E-mail: projectshelve@gmail.com<br>";
        $msg .="Website: www.projectshelve.com<br>";	 

        $gs = Generalsetting::findOrFail(1);
        // if($gs->is_smtp == 1)
        // {

        // $datas = [
        //     'to' => $to,
        //     'subject' => $subject,
        //     'body' => $msg,
        // ];
        // $mailer = new GeniusMailer();
        //  $mailer->sendCustomMail($datas);
        // }
        // else
        // {
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: ".$gs->from_name."<".$gs->from_email.">";
        // mail($to,$subject,$msg,$headers);    
        Mail::send(array(), array(), function ($message) use ($msg, $headers, $to, $subject) {
                              $message->to($to)
                             ->subject($subject)                            
                              ->setBody($msg,'text/html');
                            });         
        // }

        if($request->type == 'Ticket'){
            $conv = AdminUserConversation::where('type','=','Ticket')->where('user_id','=',$user->id)->where('subject','=',$subject)->first(); 
        }
        else{
            $conv = AdminUserConversation::where('type','=','Dispute')->where('user_id','=',$user->id)->where('subject','=',$subject)->first(); 
        }
        if(isset($conv)){
            $msg = new AdminUserMessage();
            $msg->conversation_id = $conv->id;
            $msg->message = $request->message;
            $msg->save();
            return response()->json($data);   
        }
        else{
            $message = new AdminUserConversation();
            $message->subject = $subject;
            $message->user_id= $user->id;
            $message->message = $request->message;
            $message->order_number = $request->order;
            $message->type = $request->type;
            $message->save();
            $msg = new AdminUserMessage();
            $msg->conversation_id = $message->id;
            $msg->message = $request->message;
            $msg->save();
            return response()->json($data);   
        }
        
       
    }
}