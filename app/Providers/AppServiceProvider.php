<?php

namespace App\Providers;

use App;
use Session;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Category;
use App\Classes\GeniusMailer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $admin_lang = DB::table('admin_languages')->where('is_default','=',1)->first();
        App::setlocale($admin_lang->name);
        User::chekValidation();
        
        view()->composer('*',function($settings){
            $settings->with('gs', DB::table('generalsettings')->find(1));
            $settings->with('seo', DB::table('seotools')->find(1));
            $settings->with('categories', Category::select('id', 'name', 'slug', 'photo', 'image')->where('status', '=', 1)->get());   
            if (Session::has('language')) 
            {
                $data = DB::table('languages')->find(Session::get('language'));
                $data_results = file_get_contents(public_path().'/assets/languages/'.$data->file);
                $lang = json_decode($data_results);
                $settings->with('langg', $lang);
            }
            else
            {
                $data = DB::table('languages')->where('is_default','=',1)->first();
                $data_results = file_get_contents(public_path().'/assets/languages/'.$data->file);
                $lang = json_decode($data_results);
                $settings->with('langg', $lang);
            }  

            if (!Session::has('popup')) 
            {
                $settings->with('visited', 1);
            }
            Session::put('popup' , 1);
             
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        Collection::macro('paginate', function($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });

    }
}
