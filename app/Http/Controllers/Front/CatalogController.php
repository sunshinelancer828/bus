<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Childcategory;
use App\Models\Comment;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductClick;
use App\Models\Rating;
use App\Models\Reply;
use App\Models\Report;
use App\Models\User;
use App\Models\Subcategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Auth;
use Session;
use Validator;


class CatalogController extends Controller
{


    
    // CATEGORIES SECTOPN

    public function categories()
    {
        return view('front.categories');
    }

    // -------------------------------- CATEGORY SECTION ----------------------------------------
    //
    // public function filteredProducts(Request $request, $slug=null, $slug1=null, $slug2=null)
    // {
    //
    //
    //   return $products;
    // }

    // -------------------------------- CATEGORY SECTION ----------------------------------------

    public function category(Request $request, $slug=null, $slug1=null, $slug2=null) {

        if (Session::has('currency')) {
            $curr = Currency::find(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default','=',1)->first();
        }
        $minprice = $request->min;
        $maxprice = $request->max;
        $minprice = round(($minprice / $curr->value),2);
        $maxprice = round(($maxprice / $curr->value),2);

        $cat = null;
        $subcat = null;
        $childcat = null;
        $sort = $request->sort;
        $search = $request->search;

        //echo "<pre>";print_r(gettype($search));die;

        if (!empty($slug)) {
            $cat = Category::where('slug', $slug)->firstOrFail();
            $data['cat'] = $cat;
        }

        if (!empty($slug1)) {
            $subcat = Subcategory::where('slug', $slug1)->firstOrFail();
            $data['subcat'] = $subcat;
        }

        if (!empty($slug2)) {
            $childcat = Childcategory::where('slug', $slug2)->firstOrFail();
            $data['childcat'] = $childcat;
        }

        $prods = Product::select('user_id','category_id','product_type','affiliate_link','sku', 'subcategory_id', 
        'childcategory_id', 'attributes', 'name', 'photo', 'size','size_qty','size_price', 'color','price',
        'previous_price','stock','policy','status', 'views','tags','featured','best','top','hot','latest','big',
        'trending','sale','features','colors','product_condition','ship','meta_tag','meta_description','youtube',
        'type','file','license','license_qty','link','platform','region','licence_type','measure','discount_date',
        'is_discount','whole_sell_qty','thumbnail','whole_sell_discount','catalog_id','slug','product_code','pages',
        'chapters','file_format','rejected')
        ->when($cat, function ($query, $cat) {
            return $query->where('category_id', $cat->id);
        })
        ->when($subcat, function ($query, $subcat) {
            return $query->where('subcategory_id', $subcat->id);
        })
        ->when($childcat, function ($query, $childcat) {
            return $query->where('childcategory_id', $childcat->id);
        })
        ->when($minprice, function($query, $minprice) {
            return $query->where('price', '>=', $minprice);
        })
        ->when($maxprice, function($query, $maxprice) {
            return $query->where('price', '<=', $maxprice);
        })
        ->when($sort, function ($query, $sort) {
            if ($sort=='date_desc') {
                return $query->orderBy('id', 'DESC');
            } else if ($sort=='date_asc') {
                return $query->orderBy('id', 'ASC');
            } else if ($sort=='price_desc') {
                return $query->orderBy('price', 'DESC');
            } else if($sort=='price_asc') {
                return $query->orderBy('price', 'ASC');
            }
        });  
          
    
        if ($search) {
            $search = str_replace(array('(', ')', '"', '\'', '<', '>', '+', '-', '~', '*'), 
            '', $search);
            $prods = $prods->when($search, function ($query, $search) {
                return $query->whereRaw("MATCH(name) AGAINST('" . $search . "' IN BOOLEAN MODE )")
                ->where('status','=',1);
            });
        } 
        

        $data['prods'] = $prods->paginate(30);

        if($request->ajax()) {
            $data['ajax_check'] = 1;
            return view('includes.product.filtered-products', $data);
        }
        return view('front.category', $data);
    }


    public function getsubs(Request $request) {

        $category = Category::where('slug', $request->category)->firstOrFail();
        $subcategories = Subcategory::where('category_id', $category->id)->get();
        return $subcategories;
    }


    // -------------------------------- PRODUCT DETAILS SECTION ----------------------------------------

    public function report(Request $request)
    {

        //--- Validation Section
            $rules = [
                   'note' => 'max:400',
                    ];
            $customs = [
                   'note.max' => 'Note Must Be Less Than 400 Characters.',
                       ];
            $validator = Validator::make(Input::all(), $rules, $customs);
            if ($validator->fails()) {
              return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
            }
        //--- Validation Section Ends

        //--- Logic Section
        $data = new Report;
        $input = $request->all();
        $data->fill($input)->save();
        //--- Logic Section Ends

        //--- Redirect Section
        $msg = 'New Data Added Successfully.';
        return response()->json($msg);
        //--- Redirect Section Ends

    }

    public function product($slug)
    {
        $this->code_image();

        $prod = Product::where('slug','=',$slug)->firstOrFail();
        $prod->views += 1;
        $prod->update();
        
        if (Session::has('currency')) {
            $curr = Currency::find(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default','=',1)->first();
        }

        $product_click =  new ProductClick;
        $product_click->product_id = $prod->id;
        $product_click->date = Carbon::now()->format('Y-m-d');
        $product_click->save();

        if($prod->user_id != 0) {
            $vendors = Product::where('status','=',1)->where('user_id','=',$prod->user_id)->take(8)->get();
        } else {
            $vendors = Product::where('status','=',1)->where('user_id','=',0)->take(8)->get();
        }

        $search = str_replace(array('(', ')', '"', '\'', '<', '>', '+', '-', '~', '*'), 
            '', $prod->name);
        $related = Product::whereRaw("MATCH(name) AGAINST('" . $search . "' IN BOOLEAN MODE )")
        ->where('status','=',1)
        ->where('id','!=',$prod->id)
        ->distinct()
        ->take(8)->get();

        return view('front.product', compact('prod', 'curr', 'vendors', 'related'));
    }


    // Capcha Code Image
    private function  code_image()
    {
        // $actual_path = str_replace('project','',base_path());
        // $actual_path =  '/home/projectshelvecom/public_html/';
        // $image = imagecreatetruecolor(200, 50);
        // $background_color = imagecolorallocate($image, 255, 255, 255);
        // imagefilledrectangle($image,0,0,200,50,$background_color);

        // $pixel = imagecolorallocate($image, 0,0,255);
        // for($i=0;$i<500;$i++)
        // {
        //     imagesetpixel($image,rand()%200,rand()%50,$pixel);
        // }

        // $font = $actual_path.'assets/front/fonts/NotoSans-Bold.ttf';
        // $allowed_letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        // $length = strlen($allowed_letters);
        // $letter = $allowed_letters[rand(0, $length-1)];
        // $word='';
        // //$text_color = imagecolorallocate($image, 8, 186, 239);
        // $text_color = imagecolorallocate($image, 0, 0, 0);
        // $cap_length=6;// No. of character in image
        // for ($i = 0; $i< $cap_length;$i++)
        // {
        //     $letter = $allowed_letters[rand(0, $length-1)];
        //     imagettftext($image, 25, 1, 35+($i*25), 35, $text_color, $font, $letter);
        //     $word.=$letter;
        // }
        // $pixels = imagecolorallocate($image, 8, 186, 239);
        // for($i=0;$i<500;$i++)
        // {
        //     imagesetpixel($image,rand()%200,rand()%50,$pixels);
        // }
        // session(['captcha_string' => $word]);
        // imagepng($image, $actual_path."assets/images/capcha_code.png");
        
        return true;
    }

    public function quick($id)
    {
        $product = Product::findOrFail($id);
        if (Session::has('currency'))
        {
            $curr = Currency::find(Session::get('currency'));
        }
        else
        {
            $curr = Currency::where('is_default','=',1)->first();
        }
        return view('load.quick',compact('product','curr'));

    }

    public function affProductRedirect($slug)
    {
        $product = Product::where('slug','=',$slug)->first();
//        $product->views+=1;
//        $product->update();

        return redirect($product->affiliate_link);

    }
    // -------------------------------- PRODUCT DETAILS SECTION ENDS----------------------------------------



    // -------------------------------- PRODUCT COMMENT SECTION ----------------------------------------

        public function comment(Request $request)
        {
            $comment = new Comment;
            $input = $request->all();
            $comment->fill($input)->save();
            $comments = Comment::where('product_id','=',$request->product_id)->get()->count();
            $data[0] = $comment->user->photo ? url('assets/images/users/'.$comment->user->photo):url('assets/images/noimage.png');
            $data[1] = $comment->user->name;
            $data[2] = $comment->created_at->diffForHumans();
            $data[3] = $comment->text;
            $data[4] = $comments;
            $data[5] = route('product.comment.delete',$comment->id);
            $data[6] = route('product.comment.edit',$comment->id);
            $data[7] = route('product.reply',$comment->id);
            $data[8] = $comment->user->id;
            return response()->json($data);
        }

        public function commentedit(Request $request,$id)
        {
            $comment =Comment::findOrFail($id);
            $comment->text = $request->text;
            $comment->update();
            return response()->json($comment->text);
        }

        public function commentdelete($id)
        {
            $comment =Comment::findOrFail($id);
            if($comment->replies->count() > 0)
            {
                foreach ($comment->replies as $reply) {
                    $reply->delete();
                }
            }
            $comment->delete();
        }

    // -------------------------------- PRODUCT COMMENT SECTION ENDS ----------------------------------------

    // -------------------------------- PRODUCT REPLY SECTION ----------------------------------------

        public function reply(Request $request,$id)
        {
            $reply = new Reply;
            $input = $request->all();
            $input['comment_id'] = $id;
            $reply->fill($input)->save();
            $data[0] = $reply->user->photo ? url('assets/images/users/'.$reply->user->photo):url('assets/images/noimage.png');
            $data[1] = $reply->user->name;
            $data[2] = $reply->created_at->diffForHumans();
            $data[3] = $reply->text;
            $data[4] = route('product.reply.delete',$reply->id);
            $data[5] = route('product.reply.edit',$reply->id);
            return response()->json($data);
        }

        public function replyedit(Request $request,$id)
        {
            $reply = Reply::findOrFail($id);
            $reply->text = $request->text;
            $reply->update();
            return response()->json($reply->text);
        }

        public function replydelete($id)
        {
            $reply =Reply::findOrFail($id);
            $reply->delete();
        }

    // -------------------------------- PRODUCT REPLY SECTION ENDS----------------------------------------
   

    // ------------------ Rating SECTION --------------------

        public function reviewsubmit(Request $request)
        {
            $ck = 0;
            $orders = Order::where('user_id','=',$request->user_id)->where('status','=','completed')->get();

            foreach($orders as $order)
            {
            $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
                foreach($cart->items as $product)
                {
                    if($request->product_id == $product['item']['id'])
                    {
                        $ck = 1;
                        break;
                    }
                }
            }
            if($ck == 1)
            {
                $user = Auth::guard('web')->user();
                $prev = Rating::where('product_id','=',$request->product_id)->where('user_id','=',$user->id)->get();
                if(count($prev) > 0)
                {
                return response()->json(array('errors' => [ 0 => 'You Have Reviewed Already.' ]));
                }
                $Rating = new Rating;
                $Rating->fill($request->all());
                $Rating['review_date'] = date('Y-m-d H:i:s');
                $Rating->save();
                $data[0] = 'Your Rating Submitted Successfully.';
                $data[1] = Rating::rating($request->product_id);
                return response()->json($data);
            }
            else{
                return response()->json(array('errors' => [ 0 => 'Buy This Product First' ]));
            }
        }


        public function reviews($id){
            $productt = Product::find($id);
            return view('load.reviews',compact('productt','id'));

        }

    // ------------------ Rating SECTION ENDS --------------------
}
