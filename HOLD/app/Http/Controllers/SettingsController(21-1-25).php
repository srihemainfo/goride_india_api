<?php



namespace App\Http\Controllers\crm;

use App\Http\Controllers\Controller;

use Illuminate\Database\QueryException;

use Illuminate\Http\Request;

use App\Models\{

    Generalsetting,

    Partners,

    CareFareAirport,

    Faredetail,

    Bookingrestriction,

    Googlecallender

};

use Illuminate\Support\Str;

use Carbon\Carbon;

use Illuminate\Support\Facades\{File, DB, Hash, Validator, Log, Storage, Session};

use Exception;

use App\Services\Permissions\PermissionHelperService;

use PHPMailer\PHPMailer\PHPMailer;

// use Illuminate\Http\Request;



class SettingsController extends Controller

{

    private $module = 'CAR_FARE_MODULE';

    private $permission;



    public function __construct()

    {

        $this->permission = new PermissionHelperService;

        $this->Bookingrestriction = new Bookingrestriction;

        $this->Googlecallender = new Googlecallender;

    }



    ///general settings



    public function generalsetting()

    {



        try {

            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['index'], $check[0]->db_key);

            // $loginn_id=$check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;



            // dd($loginn_id);

            //UI permissions array destructured

            [

                'CREATE' => $IS_CREATABLE,

                'UPDATE' => $IS_UPDATABLE,

                'DELETE' => $IS_DELETABLE

            ] = $this->permission->ui_permissions($this->module, $check[0]->db_key);



            $data = DB::connection($check[0]->db_key)

                ->table('gentral_setting')

                ->where('partner_id', $loginn_id)

                ->get();



            if ($data) {

                return response()->json(['status' => 200, 'data' => $data]);

            } else {

                return response()->json(['status' => 404, 'message' => "Data Not Found"]);

            }



        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getmessage()]);

        }

    }



    public function generalsettingcurrency()

    {



        try {

            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['index'], $check[0]->db_key);

            // $loginn_id=$check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;



            // dd($loginn_id);

            //UI permissions array destructured

            [

                'CREATE' => $IS_CREATABLE,

                'UPDATE' => $IS_UPDATABLE,

                'DELETE' => $IS_DELETABLE

            ] = $this->permission->ui_permissions($this->module, $check[0]->db_key);



            $data = DB::connection($check[0]->db_key)

                ->table('bookingsetting')

                ->where('partner_id', $loginn_id)

                ->first();





            if ($data) {

                return response()->json(['status' => 200, 'data' => $data]);

            } else {

                return response()->json(['status' => 404, 'message' => "Data Not Found"]);

            }



        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getmessage()]);

        }

    }









    public function getArticleList(Request $request)

    {



        try {



            $data = [];

            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['index'], $check[0]->db_key);

            // $loginn_id=$check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;

            //  dd('Test');



            //UI permissions array destructured

            [

                'CREATE' => $IS_CREATABLE,

                'UPDATE' => $IS_UPDATABLE,

                'DELETE' => $IS_DELETABLE

            ] = $this->permission->ui_permissions($this->module, $check[0]->db_key);





$webSiteID = $request->webSiteID ?? null;



            // $articles = DB::connection($check[0]->db_key)->table('articles as a')

            //     ->join('gentral_setting as g', 'g.id', '=', 'a.gentralID')

            //     ->whereNotNull('a.url')

            //     ->select('a.*', 'g.company_name')

            //     ->get();





$articles = DB::connection($check[0]->db_key)->table('articles as a')

    ->join('gentral_setting as g', 'g.id', '=', 'a.gentralID')

    ->whereNotNull('a.url')

    ->when($webSiteID, function ($query) use ($webSiteID) {

        return $query->where('a.gentralID', '=', $webSiteID);

    })

    ->select('a.*', 'g.company_name')

    ->get();





            if ($articles->count() > 0) {



                $data = $articles;



            }





            $response = ['status' => 200, 'message' => 'Article list collected successfully!', 'data' => $data];

            goto returnFVI;





            returnFVI:

            return response()->json($response);

        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getmessage()]);

        }

    }





    public function saveArticle(Request $request)

    {



        try {





            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['index'], $check[0]->db_key);

            // $loginn_id=$check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;

            //  dd('Test');



            //UI permissions array destructured

            [

                'CREATE' => $IS_CREATABLE,

                'UPDATE' => $IS_UPDATABLE,

                'DELETE' => $IS_DELETABLE

            ] = $this->permission->ui_permissions($this->module, $check[0]->db_key);





          





            if ($request->id != '' && $request->id != null && $request->id != 'null') {

                $articleCheck = DB::connection($check[0]->db_key)->table('articles')

                    // ->where('gentralID', $request->gentralID)

                    // ->where('status', 'active')

                    ->where('id', $request->id)

                    ->orderBy('id', 'desc')

                    ->limit(1)

                    ->get();





                // dd($articleCheck);





                if ($articleCheck->count() < 1) {

                    $response = [

                        'status' => 'failed',

                        'message' => 'Article not found.',

                        'error' => 'The article could not be found.'

                    ];













                    goto returnFVI;

                }

                

                

                

                

         $updateArr = [];



if (!empty($request->gentralID)) {

    $updateArr['gentralID'] = $request->gentralID;

}





if (!empty($request->content_summary)) {

    $updateArr['description'] = $request->content_summary;

}



$updateArr['status'] = 'active';  



if (!empty($request->url)) {

    $updateArr['url'] = $request->url;

}



$updateArr['updated_at'] = now();  



if (!empty($request->status)) {

    $updateArr['status'] = $request->status;

}









if (!empty($request->linkTitle)) {

    $updateArr['title'] = $request->linkTitle;

}













// if (!empty($request->title)) {

//     $updateArr['meta_title'] = $request->title;

// }



// if (!empty($request->keyword)) {

//     $updateArr['meta_keyword'] = $request->keyword;

// }



// if (!empty($request->description)) {

//     $updateArr['meta_desp'] = $request->description;

// }











    $updateArr['meta_title'] = $request->title ?? null;





    $updateArr['meta_keyword'] = $request->keyword ?? null;



    $updateArr['meta_desp'] = $request->description ?? null;







                $articlesIns = DB::connection($check[0]->db_key)->table('articles')

                    ->where('id', $request->id)

                    ->update($updateArr

                        

                        

                        

                    //     [

                    //     'gentralID' => $request->gentralID ?? null,

                    //     // 'p_menu' => null,

                    //     // 'page_open' => null,

                    //     'meta_title' => $request->title ?? null,

                    //     'meta_keyword' => $request->keyword ?? null,

                    //     'meta_desp' => $request->description ?? null,

                    //     // 'title' => null,

                    //     // 'canonical' => null,

                    //     'description' => $request->content_summary ?? null,

                    //     'status' => 'active',

                    //     // 'datetime' => now(),

                    //     // 'top' => null,

                    //     // 's_top' => null,

                    //     // 'bottom' => null,

                    //     // 'left' => null,

                    //     // 'landing' => null,

                    //     // 'order' => null,

                    //     'url' => $request->url ?? null,

                    //     // 'image' => null,

                    //     // 'stars' => 0,

                    //     // 'website' => null,

                    //     // 'fromarea' => null,

                    //     // 'toarea' => null,

                    //     'updated_at' => now(),

                    //     // 'deleted_at' => null,

                    // ]

                    

                    );







            } else {

                

                

                  if ($request->gentralID == '' || $request->gentralID == null || $request->gentralID == 'null') {

                $response = ['status' => 'failed', 'message' => 'Please provide a valid website ID!', 'error' => 'Please provide a valid website ID!'];



                goto returnFVI;

            }







            if ($request->url == '' || $request->url == null || $request->url == 'null') {

                $response = ['status' => 'failed', 'message' => 'Please provide a valid url!', 'error' => 'Please provide a valid url!'];



                goto returnFVI;

            }





  if ($request->content_summary == '' || $request->content_summary == null || $request->content_summary == 'null') {

                $response = ['status' => 'failed', 'message' => 'Please provide a valid content!', 'error' => 'Please provide a valid content!'];



                goto returnFVI;

            }





  if ($request->linkTitle == '' || $request->linkTitle == null || $request->linkTitle == 'null') {

                $response = ['status' => 'failed', 'message' => 'Please provide a valid title!', 'error' => 'Please provide a valid title!'];



                goto returnFVI;

            }









// dd($request->title);

            if (isset($request->title) && ($request->title == '' || $request->title == null || $request->title == 'null')) {

                $response = ['status' => 'failed', 'message' => 'Please provide a valid meta title!', 'error' => 'Please provide a valid meta title!'];



                goto returnFVI;

            }





          



            if (isset($request->description) && ($request->description == '' || $request->description == null || $request->description == 'null')) {

                $response = ['status' => 'failed', 'message' => 'Please provide a valid description!', 'error' => 'Please provide a valid description!'];



                goto returnFVI;

            }





            if (isset($request->keyword) && ($request->keyword == '' || $request->keyword == null || $request->keyword == 'null')) {

                $response = ['status' => 'failed', 'message' => 'Please provide a valid meta keyword!', 'error' => 'Please provide a valid meta keyword!'];



                goto returnFVI;

            }





// $request->





                $articleCheck = DB::connection($check[0]->db_key)->table('articles')

                    ->where('gentralID', $request->gentralID)

                    ->where('status', 'active')

                    ->where('url', $request->url)

                    ->orderBy('id', 'desc')

                    ->limit(1)

                    ->get();





                if ($articleCheck->count() > 0) {

                    $response = ['status' => 'failed', 'message' => 'The URL already exists for this website.', 'error' => 'The URL already exists for this website.'];





                    goto returnFVI;

                }







                // dd($request);



                $articlesIns = DB::connection($check[0]->db_key)->table('articles')->insert([

                    'id' => null,

                    'gentralID' => $request->gentralID ?? '1',

                    'p_menu' => null,

                    'page_open' => null,

                    'meta_title' => $request->title ?? null,

                    'meta_keyword' => $request->keyword ?? null,

                    'meta_desp' => $request->description ?? null,

                    'title' => $request->linkTitle ?? '',

                    'canonical' => null,

                    'description' => $request->content_summary ?? null,

                    'status' => 'active',

                    'datetime' => now(),

                    'top' => null,

                    's_top' => null,

                    'bottom' => null,

                    'left' => null,

                    'landing' => null,

                    'order' => null,

                    'url' => $request->url ?? null,

                    'image' => null,

                    'stars' => 0,

                    'website' => null,

                    'fromarea' => null,

                    'toarea' => null,

                    'created_at' => now(),

                    'updated_at' => now(),

                    'deleted_at' => null,

                ]);

            }





            // dd($articlesIns);



            if ($articlesIns) {

                // return response()->json(['status' => 200, 'data' => $data]);



                $response = ['status' => 200, 'message' => (($request->id != '' && $request->id != null && $request->id != 'null') ? 'Article has been successfully updated!' : 'Article has been successfully created!'), 'error' => (($request->id != '' && $request->id != null && $request->id != 'null') ? 'Article has been successfully updated!' : 'Article has been successfully created!')];

                goto returnFVI;

            } else {

                // return response()->json(['status' => 404, 'message' => "Data Not Found"]);



                $response = ['status' => 404, 'message' => 'Article creation process failed!', 'error' => 'Insert query failed.'];



                goto returnFVI;

            }





            returnFVI:

            return response()->json($response);

        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getmessage()]);

        }

    }

















    public function generalstore(Request $request)

    {



        try {



            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store'], $check[0]->db_key);

            // $loginn_id = $check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;



            // Validation

            // $validator = Validator::make($request->all(), [

            //     "add_company_name" => 'required',

            //     // "add_trading_name" => 'required',

            //     "add_contact_number" => 'required',

            //     "website_prefix" => 'required',

            //      "domain_Prefix" => 'required',

            //     "add_email" => 'required|email',

            //     "add_txtLogo" => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            //     "add_favicon" => 'nullable'

            // ]);



            // if ($validator->fails()) {

            //     return response()->json(['status' => 400, 'errors' => $validator->errors()]);

            // }



$cweburl = $request->domain_Prefix . '.goride.run';



$checkWebUrl = DB::table('gentral_setting')

            ->where('status', '0')

            ->whereNotNull('cweburl')

            ->where('cweburl', $cweburl)

            ->orderByDesc('id')

            ->get();



if($checkWebUrl->count() > 0){

     return response()->json(['status' => 500, 'error' => 'The domain prefix already exist!']);

}









            // Initialize variables to null

            $imageUrl = '';

            $imageUrl1 = '';



            // Handling txtLogo image upload

            if ($request->hasFile('add_txtLogo')) {

                $image = $request->file('add_txtLogo');

                $filePath = 'generalsetting/' . $image->getClientOriginalName();

                Storage::disk('s3')->put($filePath, file_get_contents($image));

                $imageUrl = Storage::disk('s3')->url($filePath);

            }

            // dd($imageUrl, $imageUrl1);

            // Handling favicon image upload

            if ($request->hasFile('add_favicon')) {

                $image = $request->file('add_favicon');

                $filePath = 'generalsetting/' . $image->getClientOriginalName();

                Storage::disk('s3')->put($filePath, file_get_contents($image));

                $imageUrl1 = Storage::disk('s3')->url($filePath);

            }



            // if (!$request->generalid) {

            // Insert data partner database table 

            $random_key_id = Str::random(16);

            $url_website = "https://customer.goride.run/{$random_key_id}";

            $data_partner_id = DB::connection($check[0]->db_key)

                ->table('gentral_setting')

                ->insertGetId([

                    'cweburl' => $cweburl,

                    'domain_name' => $request->add_domain_name ?? null,

                    'company_name' => $request->add_company_name ?? null,

                    'trading_name' => $request->add_trading_name ?? null,

                    'topbar_footer_bgcolor' => $request->add_bgColorTopFooter ?? null,

                    'menu_background_color' => $request->add_bgColorMenu ?? null,

                    'menu_text_color' => $request->add_textColorMenu ?? null,

                    'google_api_key' => $request->add_google_api_key ?? null,

                    'google_translate' => $request->add_google_translate ?? '',

                    'site_currency' => $request->add_site_currencies ?? '',

                    'random_key' => $random_key_id ?? null,

                    'cookie_consent' => $request->add_cookieConsent ?? '',

                    'topbar_footer_text_color' => $request->add_textColorTopFooter ?? null,

                    'contact_number' => $request->hidden_phoneCode . $request->add_contact_number ?? null,

                    'email' => $request->add_email ?? null,

                    'company_address' => $request->add_company_address ?? null,

                    'partner_id' => $loginn_id ?? null,

                    'license_number' => $request->add_licencenumber ?? '',

                    'licensed_by' => $request->add_lincenceedby ?? '',

                    'license_referrer_link' => $request->add_lincenceedby ?? '',

                    'logo' => $imageUrl ?? '',

                    'favicon' => $imageUrl1 ?? '',

                    'website_url' => $url_website ?? '',

                    'whatsapp_number' => $request->add_whatsapp_number ?? '',

                    'website_prefix' => $request->website_prefix ?? '',

                    'country' => $request->add_site_country ?? '',

                ]);





            // Insert data partner database table   

            $data_main = DB::table('gentral_setting')->insert([

                   'cweburl' => $cweburl,

                'company_name' => $request->add_company_name ?? '',

                'domain_name' => $request->add_domain_name ?? '',

                'trading_name' => $request->add_trading_name ?? '',

                'topbar_footer_bgcolor' => $request->add_bgColorTopFooter ?? '',

                'menu_background_color' => $request->add_bgColorMenu ?? '',

                'menu_text_color' => $request->add_textColorMenu ?? '',

                'google_api_key' => $request->add_google_api_key ?? '',

                'google_translate' => $request->add_google_translate ?? '',

                'site_currency' => $request->add_site_currencies ?? '',

                'cookie_consent' => $request->add_cookieConsent ?? '',

                'topbar_footer_text_color' => $request->add_textColorTopFooter ?? '',

                'contact_number' => $request->add_contact_number ?? '',

                'email' => $request->add_email ?? '',

                'company_address' => $request->add_company_address ?? '',

                'partner_id' => $loginn_id ?? '',

                'license_number' => $request->add_licencenumber ?? '',

                'licensed_by' => $request->add_lincenceedby ?? '',

                'license_referrer_link' => $request->add_licenseReferrerLink ?? '',

                // Only insert logo and favicon if they are not null

                'logo' => $imageUrl ? $imageUrl : '',

                'favicon' => $imageUrl1 ? $imageUrl1 : '',

                'random_key' => $random_key_id ?? '',

                'general_setting_ref_id' => $data_partner_id ?? '',

                'website_url' => $url_website ?? '',

                'whatsapp_number' => $request->add_whatsapp_number ?? '',

                'website_prefix' => $request->website_prefix ?? '',

                'country' => $request->add_site_country ?? '',

            ]);







            $message = 'Website created successfully';



            return response()->json(['status' => 200, 'message' => $message]);



        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getMessage()]);

        }

    }



    public function generaldelete(Request $request)

    {

        try {



            $check = Session::get('check');

            // $loginn_id = $check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;

            $data = DB::connection($check[0]->db_key)

                ->table('gentral_setting')

                ->where('id', $request->emp_id)

                ->delete();



            $updateData = [

                'status' => 1,

            ];



            $data1 = DB::table('gentral_setting')

                ->where('partner_id', $loginn_id)

                ->where('general_setting_ref_id', $request->emp_id)

                ->update($updateData);

            return response()->json($data && $data1 ? ['status' => 200, 'isDeleted' => true] : ['status' => 400, 'isDeleted' => false]);

        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getmessage()]);

        }

    }



    public function generalupdate(Request $request)

    {

        try {



            //return $request->all();

            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store'], $check[0]->db_key);

            // $loginn_id = $check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;



            // Validation

            $validator = Validator::make($request->all(), [

                "edit_company_name" => 'required',

                // "edit_trading_name" => 'required',

                "edit_contact_number" => 'required',

                "edit_email" => 'required|email',

                "edit_txtLogo" => 'nullable',

                "edit_favicon" => 'nullable'

            ]);



            if ($validator->fails()) {

                return response()->json(['status' => 400, 'errors' => $validator->errors()]);

            }



            // Initialize variables to null

            $imageUrl = null;

            $imageUrl1 = null;



            // Handling txtLogo image upload

            if ($request->hasFile('edit_txtLogo')) {

                $image = $request->file('edit_txtLogo');

                $filePath = 'generalsetting/' . $image->getClientOriginalName();

                Storage::disk('s3')->put($filePath, file_get_contents($image));

                $imageUrl = Storage::disk('s3')->url($filePath);

            }



            // Handling favicon image upload

            if ($request->hasFile('favicon')) {

                $image = $request->file('favicon');

                $filePath = 'generalsetting/' . $image->getClientOriginalName();

                Storage::disk('s3')->put($filePath, file_get_contents($image));

                $imageUrl1 = Storage::disk('s3')->url($filePath);

            }



            // Update data

            $updateData = [

                'company_name' => $request->edit_company_name,

                'trading_name' => $request->edit_trading_name,

                'topbar_footer_bgcolor' => $request->edit_bgColorTopFooter,

                'menu_background_color' => $request->edit_bgColorMenu,

                'menu_text_color' => $request->edit_textColorMenu,

                'google_api_key' => $request->edit_google_api_key,

                'google_translate' => $request->edit_google_translate,

                'site_currency' => $request->edit_site_currencies,

                'cookie_consent' => $request->edit_cookieConsent,

                'topbar_footer_text_color' => $request->edit_textColorTopFooter,

                'contact_number' => $request->edit_contact_number,

                'email' => $request->edit_email,

                'company_address' => $request->edit_company_address,

                'partner_id' => $loginn_id,

                'license_number' => $request->edit_licencenumber,

                'licensed_by' => $request->edit_lincenceedby,

                'license_referrer_link' => $request->edit_lincenceedby,

                'whatsapp_number' => $request->edit_whatsapp_number,

                'website_prefix' => $request->edit_website_prefix,

                'country' => $request->edit_site_country,

                'site_currency' => '',

                'cookie_consent' => '',

                'license_number' => '',

                'license_referrer_link' => '',

            ];



            // Conditionally add logo and favicon if present

            if ($imageUrl) {

                $updateData['logo'] = $imageUrl;

            }

            if ($imageUrl1) {

                $updateData['favicon'] = $imageUrl1;

            }



            // Update partner database table

            $partner_update = DB::connection($check[0]->db_key)

                ->table('gentral_setting')

                ->where('partner_id', $loginn_id)

                ->where('id', $request->edit_model_id)

                ->update($updateData);



            // return $partner_update;

            // Update main database table

            $maindb_update = DB::table('gentral_setting')

                ->where('partner_id', $loginn_id)

                ->where('general_setting_ref_id', $request->edit_model_id)

                ->update($updateData);

            // return $maindb_update;

            // Return success message if both updates succeed

            if ($partner_update !== false && $maindb_update !== false) {

                return response()->json(['status' => 200, 'message' => 'Website updated successfully']);

            } else {

                return response()->json(['status' => 500, 'message' => 'Failed to update data']);

            }



        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getMessage()]);

        }

    }





    public function generalsettingedit(Request $request)

    {



        try {

            $check = Session::get('check');

            // $loginn_id=$check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;

            $country = DB::connection($check[0]->db_key)->table('bookingsetting')->value('country'); 

            if ($country == 'India') {
                 $phoneCode = '+91';
             } elseif ($country == 'Canada' || $country == 'United States') {
                 $phoneCode = '+1';
             } elseif ($country == 'United Kingdom') {
                 $phoneCode = '+44';
             } elseif ($country == 'Kuwait') {
                 $phoneCode = '+965';
             } else {
                 return response()->json(['status' => 404, 'message' => 'Country code not found for the given country']);
             }
                            
 

            $data = DB::connection($check[0]->db_key)

                ->table('gentral_setting')

                ->where('partner_id', $loginn_id)

                ->where('id', $request->emp_id)

                ->first();

                $data->phone_code = $phoneCode;

            if ($data) {

                return response()->json(['status' => 200, 'data' => $data]);

            } else {

                return response()->json(['status' => 404, 'message' => "Data Not Found"]);

            }



        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getmessage()]);

        }

    }



    ///booking settings



    public function bookingsetting()

    {



        try {

            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['index'], $check[0]->db_key);

            // $loginn_id=$check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;



            // dd($loginn_id);

            //UI permissions array destructured

            [

                'CREATE' => $IS_CREATABLE,

                'UPDATE' => $IS_UPDATABLE,

                'DELETE' => $IS_DELETABLE

            ] = $this->permission->ui_permissions($this->module, $check[0]->db_key);



            $data = DB::connection($check[0]->db_key)

                ->table('bookingsetting')

                ->where('partner_id', $loginn_id)

                ->first();



            if ($data) {

                return response()->json(['status' => 200, 'data' => $data]);

            } else {

                return response()->json(['status' => 404, 'message' => "Data Not Found"]);

            }



        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getmessage()]);

        }

    }



    public function bookingstore(Request $request)

    {





        try {

            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store'], $check[0]->db_key);

            // $loginn_id = $check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;



            // Validation

            $validator = Validator::make($request->all(), [

                "country" => 'required',

                "timezone" => 'required',

                "currency" => 'required',

                "distance_unit" => 'required',

                // "order_prefix" => 'required'

            ]);



            if ($validator->fails()) {

                return response()->json(['status' => 400, 'errors' => $validator->errors()]);

            }





            if (!$request->bokingsettingid) {

                DB::connection($check[0]->db_key)

                    ->table('bookingsetting')

                    ->insert([

                        'country' => $request->country,

                        'partner_id' => $loginn_id,

                        'timezone' => $request->timezone,

                        'currency' => $request->currency,

                        'additional_drop_offs' => $request->additional_drop_offs,

                        'google_map_api_key_browser' => $request->google_map_api_key_browser,

                        'google_map_api_key_server' => $request->google_map_api_key_server,

                        'route' => $request->route,

                        'distance_unit' => $request->distance_unit,

                        // 'order_prefix' => $request->order_prefix,

                        'auto_customer_registration' => $request->auto_customer_registration,

                        'auto_booking_accept' => $request->auto_booking_accept,

                        'hourl_package' => $request->hourl_package,

                        'advance_booking_minium' => $request->advance_booking_minium,

                        'advance_booking_minium_type' => $request->advance_booking_minium_type,

                        'advance_booking_maximum_type' => $request->advance_booking_maximum_type,

                        'advance_booking_maximum' => $request->advance_booking_maximum,

                        'cancel_booking' => $request->cancel_booking,

                        'cancel_booking_type' => $request->cancel_booking_type,

                        'txtCancelBookingRestrictType' => $request->txtCancelBookingRestrictType,

                    ]);



                $message = 'Data has been inserted successfully';

            } else {

                // Update data

                $updateData = [

                    'country' => $request->country,

                    'partner_id' => $loginn_id,

                    'timezone' => $request->timezone,

                    'currency' => $request->currency,

                    'additional_drop_offs' => $request->additional_drop_offs,

                    'google_map_api_key_browser' => $request->google_map_api_key_browser,

                    'google_map_api_key_server' => $request->google_map_api_key_server,

                    'route' => $request->route,

                    'distance_unit' => $request->distance_unit,

                    // 'order_prefix' => $request->order_prefix,

                    'auto_customer_registration' => $request->auto_customer_registration,

                    'auto_booking_accept' => $request->auto_booking_accept,

                    'hourl_package' => $request->hourl_package,

                    'advance_booking_minium' => $request->advance_booking_minium,

                    'advance_booking_minium_type' => $request->advance_booking_minium_type,

                    'advance_booking_maximum_type' => $request->advance_booking_maximum_type,

                    'advance_booking_maximum' => $request->advance_booking_maximum,

                    'cancel_booking' => $request->cancel_booking,

                    'cancel_booking_type' => $request->cancel_booking_type,

                    'txtCancelBookingRestrictType' => $request->txtCancelBookingRestrictType,

                ];



                DB::connection($check[0]->db_key)

                    ->table('bookingsetting')

                    ->where('partner_id', $loginn_id)

                    ->update($updateData);



                $message = 'Data has been updated successfully';

            }



            return response()->json(['status' => 200, 'message' => $message]);



        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getMessage()]);

        }

    }



    // Email settings



    public function emailsetting()

    {





        try {

            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['index'], $check[0]->db_key);

            // $loginn_id=$check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;



            // dd($loginn_id);

            //UI permissions array destructured

            [

                'CREATE' => $IS_CREATABLE,

                'UPDATE' => $IS_UPDATABLE,

                'DELETE' => $IS_DELETABLE

            ] = $this->permission->ui_permissions($this->module, $check[0]->db_key);



            $data = DB::connection($check[0]->db_key)

                ->table('emailsetting')

                ->where('partner_id', $loginn_id)

                ->first();



            if ($data) {

                return response()->json(['status' => 200, 'data' => $data]);

            } else {

                return response()->json(['status' => 404, 'message' => "Data Not Found"]);

            }



        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getmessage()]);

        }

    }





    public function emailsettingstore(Request $request)

    {

        try {

            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store'], $check[0]->db_key);

            // $loginn_id = $check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;



            // Validation

            $validator = Validator::make($request->all(), [

                // "from_email" => 'required',

                "from_name" => 'required',

                "mailer_type" => 'required'

                // "smtp_host" => 'required',

                // "smtp_port" => 'required',

                // "encryption_type" => 'required',

                // "smtp_user_name" => 'required',

                // "smtp_password" => 'required'

            ]);



            if ($validator->fails()) {

                return response()->json(['status' => 400, 'errors' => $validator->errors()]);

            }



            if ($request->mailer_type == 'GoRide') {



                $from_Mail = 'noreply@airportrides.co';

                $smtp_host = 'smtp.zeptomail.in';

                $smtp_port = '587';

                $encryption_type = 'TLS';

                $smtp_user_name = 'emailapikey';

                $smtp_password = 'PHtE6r0MQezujGIspBdTtv65QMSmZ40qruw0LwURsd1LDKUKH00Aq4ovwDW/r0p/UvVDE/XNyoJt4OnK5+6MJDztNmdNWmqyqK3sx/VYSPOZsbq6x00asVUdckbfUYPndtFj3SzRupI=';



                $mail = new PHPMailer();

                $mail->Encoding = "base64";

                $mail->SMTPAuth = true;

                $mail->Host = "smtp.zeptomail.in";

                $mail->Port = 587;

                $mail->Username = "emailapikey";

                $mail->Password = 'PHtE6r0MQezujGIspBdTtv65QMSmZ40qruw0LwURsd1LDKUKH00Aq4ovwDW/r0p/UvVDE/XNyoJt4OnK5+6MJDztNmdNWmqyqK3sx/VYSPOZsbq6x00asVUdckbfUYPndtFj3SzRupI=';

                $mail->SMTPSecure = 'TLS';

                $mail->isSMTP();

                $mail->IsHTML(true);

                $mail->CharSet = "UTF-8";

                $mail->From = "noreply@airportrides.co";

                $mail->addAddress('info@airportrides.co', $request->from_name);

                $mail->Body = "Test Confirmation mail for mail configuration.";

                $mail->Subject = "Test Email";

                $mail->SMTPDebug = 0;

                $mail->Debugoutput = null;

                if (!$mail->Send()) {

                    $message = 'Email Configuration Failed.';

                    return response()->json(['status' => 400, 'message' => $mail->ErrorInfo]);

                } else {

                    $getEmail = DB::connection($check[0]->db_key)

                        ->table('emailsetting')

                        ->where(['partner_id' => $loginn_id])->first();



                    if (!$getEmail) {

                        DB::connection($check[0]->db_key)

                            ->table('emailsetting')

                            ->insert([

                                'from_email' => $from_Mail,

                                'partner_id' => $loginn_id,

                                'from_name' => $request->from_name,

                                'mailer_type' => $request->mailer_type,

                                'smtp_host' => $smtp_host,

                                'smtp_port' => $smtp_port,

                                'encryption_type' => $encryption_type,

                                'smtp_user_name' => $smtp_user_name,

                                'smtp_password' => $smtp_password,

                            ]);



                        $message = 'Email Configured Successfully';

                    } else {

                        // Update data

                        $updateData = [

                            'from_email' => $from_Mail,

                            'from_name' => $request->from_name,

                            'mailer_type' => $request->mailer_type,

                            'smtp_host' => $smtp_host,

                            'smtp_port' => $smtp_port,

                            'encryption_type' => $encryption_type,

                            'smtp_user_name' => $smtp_user_name,

                            'smtp_password' => $smtp_password,



                        ];



                        DB::connection($check[0]->db_key)

                            ->table('emailsetting')

                            ->where(['partner_id' => $loginn_id])

                            ->update($updateData);



                        $message = 'Email Configuration Updated successfully';

                    }

                }



            } elseif ($request->mailer_type == 'SMTP') {



                $from_Mail = $request->from_email;

                $smtp_host = $request->smtp_host;

                $smtp_port = $request->smtp_port;

                $encryption_type = $request->encryption_type;

                $smtp_user_name = $request->smtp_user_name;

                $smtp_password = $request->smtp_password;

                $from_name = $request->from_name;



                $mail = new PHPMailer();

                $mail->Encoding = "base64";

                $mail->SMTPAuth = true;

                $mail->Host = $smtp_host;

                $mail->Port = $smtp_port;

                $mail->Username = $smtp_user_name;

                $mail->Password = $smtp_password;

                $mail->SMTPSecure = $encryption_type;

                $mail->isSMTP();

                $mail->IsHTML(true);

                $mail->CharSet = "UTF-8";

                $mail->From = $from_Mail;

                $mail->addAddress('info@airportrides.co', $from_name);

                $mail->Body = 'Test Confirmation mail for mail configuration.';

                $mail->Subject = "Test Email";

                $mail->SMTPDebug = 0;  // Enable debugging to get detailed error messages

                $mail->Debugoutput = null;

                if (!$mail->Send()) {

                    $message = 'Email Configuration Failed.';

                    return response()->json(['status' => 400, 'message' => $mail->ErrorInfo]);

                } else {

                    $getEmail = DB::connection($check[0]->db_key)

                        ->table('emailsetting')

                        ->where(['partner_id' => $loginn_id])->first();



                    if (!$getEmail) {

                        DB::connection($check[0]->db_key)

                            ->table('emailsetting')

                            ->insert([

                                'from_email' => $from_Mail,

                                'partner_id' => $loginn_id,

                                'from_name' => $request->from_name,

                                'mailer_type' => $request->mailer_type,

                                'smtp_host' => $smtp_host,

                                'smtp_port' => $smtp_port,

                                'encryption_type' => $encryption_type,

                                'smtp_user_name' => $smtp_user_name,

                                'smtp_password' => $smtp_password,

                            ]);



                        $message = 'Email Configured Successfully';

                    } else {

                        // Update data

                        $updateData = [

                            'from_email' => $from_Mail,

                            'from_name' => $request->from_name,

                            'mailer_type' => $request->mailer_type,

                            'smtp_host' => $smtp_host,

                            'smtp_port' => $smtp_port,

                            'encryption_type' => $encryption_type,

                            'smtp_user_name' => $smtp_user_name,

                            'smtp_password' => $smtp_password,



                        ];



                        DB::connection($check[0]->db_key)

                            ->table('emailsetting')

                            ->where(['partner_id' => $loginn_id])

                            ->update($updateData);



                        $message = 'Email Configuration Updated successfully';

                    }

                }

            }

            return response()->json(['status' => 200, 'message' => $message]);



        } catch (Exception $e) {

            // dd($e->getMessage());

            return response()->json(['status' => 500, 'error' => $e->getMessage()]);

        }

    }



    public function emailTest(Request $request)

    {



        if (empty($request->txtSendTo)) {

            return response()->json(['status' => 400, 'message' => 'Email field is required.']);

        }



        $check = Session::get('check');

        $this->permission->check_privilege($this->module, self::ACTION_TYPE['store'], $check[0]->db_key);

        // $loginn_id = $check[0]->id;

        $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;

        $log_name = Partners::where('db_key', $check[0]->db_key)->select('name')->first()->name ?? null;

        $getEmail = DB::connection($check[0]->db_key)

            ->table('emailsetting')

            ->where('partner_id', $loginn_id)->first();

        if ($getEmail) {



            $from_Mail = $getEmail->from_email;

            $smtp_host = $getEmail->smtp_host;

            $smtp_port = $getEmail->smtp_port;

            $encryption_type = $getEmail->encryption_type;

            $smtp_user_name = $getEmail->smtp_user_name;

            $smtp_password = $getEmail->smtp_password;

            $from_name = $getEmail->from_name;





            $mail = new PHPMailer();

            $mail->Encoding = "base64";

            $mail->SMTPAuth = true;

            $mail->Host = $smtp_host;

            $mail->Port = $smtp_port;

            $mail->Username = $smtp_user_name;

            $mail->Password = $smtp_password;

            $mail->SMTPSecure = $encryption_type;

            $mail->isSMTP();

            $mail->IsHTML(true);

            $mail->CharSet = "UTF-8";

            $mail->setFrom($from_Mail, $from_name);

            $mail->addAddress($request->txtSendTo, $log_name);

            $mail->Body = 'Test Mail Send Successfully.';

            $mail->Subject = "Email Testing";

            $mail->SMTPDebug = 0;

            $mail->Debugoutput = null;

            if (!$mail->Send()) {

                $message = 'Email Configuration Failed.';

                return response()->json(['status' => 400, 'message' => $message]);

            }



            $message = 'Test Email Send Successfully.';

            return response()->json(['status' => 200, 'message' => $message]);

        } else {

            return response()->json(['status' => 400, 'message' => 'First Configure the Email Setting.']);

        }





    }



    // zone settings

    public function zonesetting()

    {





        try {



            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['index'], $check[0]->db_key);

            // $loginn_id=$check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;



            // dd($loginn_id);

            //UI permissions array destructured

            [

                'CREATE' => $IS_CREATABLE,

                'UPDATE' => $IS_UPDATABLE,

                'DELETE' => $IS_DELETABLE

            ] = $this->permission->ui_permissions($this->module, $check[0]->db_key);



            $data = DB::connection($check[0]->db_key)

                ->table('emailsetting')

                ->where('partner_id', $loginn_id)

                ->first();



            if ($data) {

                return response()->json(['status' => 200, 'data' => $data]);

            } else {

                return response()->json(['status' => 404, 'message' => "Data Not Found"]);

            }



        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getmessage()]);

        }

    }



    // payment options 



    public function paymentoption()

    {

        try {

            // Retrieve the session data

            $check = Session::get('check');



            // UI permissions array destructured

            [

                'CREATE' => $IS_CREATABLE,

                'UPDATE' => $IS_UPDATABLE,

                'DELETE' => $IS_DELETABLE

            ] = $this->permission->ui_permissions($this->module, $check[0]->db_key);



            // Fetch payment settings data

            $data = DB::connection($check[0]->db_key)

                ->table('payment_setting')

                ->first();



            // Return response based on data existence

            if ($data) {

                return response()->json(['status' => 200, 'data' => $data]);

            } else {

                return response()->json(['status' => 404, 'message' => 'Data Not Found']);

            }



        } catch (Exception $e) {

            // Return error response with proper message

            return response()->json(['status' => 500, 'error' => $e->getMessage()]);

        }

    }





    public function paymentstore(Request $request)

    {

        try {

            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store'], $check[0]->db_key);

            // $loginn_id = $check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;

            $currentDateTime = now();

            // return $request->paymentsetting_id;

            // Insert data



            if (!$request->paymentsetting_id) {



                DB::connection($check[0]->db_key)

                    ->table('payment_setting')

                    ->insert([

                        'cash_title' => $request->cash_title,

                        'partner_id' => $loginn_id,

                        'paypal_live_check' => $request->paypal_live_check,

                        'paypal_title' => $request->paypal_title,

                        'paypal_id' => $request->paypal_id,

                        'paypal_identify_token' => $request->paypal_identify_token,

                        'stripe_live_check' => $request->stripe_live_check,

                        'stripe_title' => $request->stripe_title,

                        'stripePublishableKey' => $request->stripePublishableKey,

                        'stripeSecretKey' => $request->stripeSecretKey,

                        'stripeWebhookUrl' => $request->stripeWebhookUrl,

                        'stripeWebhookEvent' => $request->stripeWebhookEvent,

                        'stripeWebhookSecretKey' => $request->stripeWebhookSecretKey,

                        'square_live_check' => $request->square_live_check,

                        'square_title' => $request->square_title,

                        'txtsquare_accessToken' => $request->txtsquare_accessToken,

                        'txt_square_appId' => $request->txt_square_appId,

                        'txt_square_locationId' => $request->txt_square_locationId,

                        'created_at' => $currentDateTime,

                        'cash_check' => $request->cashPayment,

                    ]);



                $message = 'Data has been inserted successfully';

                return response()->json(['status' => 200, 'message' => $message]);



            } else {



                DB::connection($check[0]->db_key)

                    ->table('payment_setting')

                    ->where('id', $request->paymentsetting_id)

                    ->update([

                        'cash_title' => $request->cash_title,

                        'partner_id' => $loginn_id,

                        'paypal_live_check' => $request->paypal_live_check,

                        'paypal_title' => $request->paypal_title,

                        'paypal_id' => $request->paypal_id,

                        'paypal_identify_token' => $request->paypal_identify_token,

                        'stripe_live_check' => $request->stripe_live_check,

                        'stripe_title' => $request->stripe_title,

                        'stripePublishableKey' => $request->stripePublishableKey,

                        'stripeSecretKey' => $request->stripeSecretKey,

                        'stripeWebhookUrl' => $request->stripeWebhookUrl,

                        'stripeWebhookEvent' => $request->stripeWebhookEvent,

                        'stripeWebhookSecretKey' => $request->stripeWebhookSecretKey,

                        'square_live_check' => $request->square_live_check,

                        'square_title' => $request->square_title,

                        'txtsquare_accessToken' => $request->txtsquare_accessToken,

                        'txt_square_appId' => $request->txt_square_appId,

                        'txt_square_locationId' => $request->txt_square_locationId,

                        'created_at' => $currentDateTime,

                        'cash_check' => $request->cashPayment,

                    ]);





                $message = 'Data has been update successfully';

                return response()->json(['status' => 200, 'message' => $message]);







            }









        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getMessage()]);

        }

    }

    // booking restriction setting



    public function bookingrestriction()

    {

        try {

            // Retrieve the session data

            $check = Session::get('check');



            // UI permissions array destructured

            [

                'CREATE' => $IS_CREATABLE,

                'UPDATE' => $IS_UPDATABLE,

                'DELETE' => $IS_DELETABLE

            ] = $this->permission->ui_permissions($this->module, $check[0]->db_key);



            // Fetch payment settings data

            $data = DB::connection($check[0]->db_key)

                ->table('booking_restriction_setting')

                ->whereNull('deleted_at')

                ->get();







            // Return response based on data existence

            if ($data) {

                return response()->json(['status' => 200, 'data' => $data]);

            } else {

                return response()->json(['status' => 404, 'message' => 'Data Not Found']);

            }



        } catch (Exception $e) {

            // Return error response with proper message

            return response()->json(['status' => 500, 'error' => $e->getMessage()]);

        }

    }



    public function bookingrestrictionstore(Request $request)

    {

        try {

            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store'], $check[0]->db_key);

            // $loginn_id = $check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;

            $currentDateTime = now();

            if (!$request->edit_employee_id) {

                $fromdate = $request->txtDateFrom;

                $todate = $request->txtDateTo;

                $formattedDateTime = date('Y-m-d H:i:s', strtotime($fromdate));

                $toformattedDateTime = date('Y-m-d H:i:s', strtotime($todate));

                // Insert data

                DB::connection($check[0]->db_key)

                    ->table('booking_restriction_setting')

                    ->insert([

                        'caption' => $request->caption,

                        'partner_id' => $loginn_id,

                        'recurring' => $request->recurring,

                        'from' => $formattedDateTime,

                        'to' => $toformattedDateTime,

                        'created_at' => $currentDateTime,

                    ]);



                $message = 'Data has been inserted successfully';

            } else {



                // Update data

                $updateData = [

                    'caption' => $request->caption,

                    'partner_id' => $loginn_id,

                    'recurring' => $request->recurring,

                    'from' => $formattedDateTime,

                    'to' => $toformattedDateTime,

                    'created_at' => $currentDateTime,

                ];





                $update = DB::connection($check[0]->db_key)

                    ->table('booking_restriction_setting')

                    ->where('id', $request->edit_employee_id)

                    ->update($updateData);



                $message = 'Data has been updated successfully';

            }



            return response()->json(['status' => 200, 'message' => $message]);



        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getMessage()]);

        }

    }



    public function bookingrestrictionfilter(Request $request)

    {

        try {

            $check = Session::get('check');

            $employer = new Bookingrestriction();

            $data = $employer->bookingrestrictionfilter($check[0]->db_key, $request->all());

            return response()->json($data->count() > 0 ? ['status' => 200, 'data' => $data] : ['status' => 400, 'message' => 'Data Not Found']);

        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getmessage()]);

        }

    }



    public function bookingrestrictionedit(Request $request)

    {

        try {

            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['edit'], $check[0]->db_key);

            $locationrange = Bookingrestriction::on($check[0]->db_key)->find($request->emp_id);

            return response()->json($locationrange ? ['status' => 200, 'data' => $locationrange] : ['status' => 400, 'data' => NULL]);

        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getmessage()]);

        }

    }



    public function bookingrestrictionupdate(Request $request)

    {

        try {

            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['update'], $check[0]->db_key);

            $currentDateTime = now();

            $validator = Validator::make($request->all(), [

                "edit_caption" => "required",

                'edit_from' => "required",

                "edit_recurring" => "required",

                "edit_to" => "required"

            ]);



            if ($validator->fails()) {

                return response()->json([

                    'status' => 400,

                    'errors' => $validator->errors()

                ]);

            } else {

                $data = Bookingrestriction::on($check[0]->db_key)->where('id', $request->edit_employee_id)->update([

                    'caption' => $request->edit_caption,

                    'recurring' => $request->edit_recurring,

                    'from' => $request->edit_from,

                    'to' => $request->edit_to,

                    'updated_at' => $currentDateTime,

                ]);



                return response()->json($data == true ? ['status' => 200, 'message' => 'Booking Restriction has been updated succcessfully'] : ['status' => 400, 'message' => 'Failed']);

            }

        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getmessage()]);

        }

    }





    public function bookingrestrictiondelete(Request $request)

    {

        try {

            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['destroy'], $check[0]->db_key);

            $data = $this->Bookingrestriction->on($check[0]->db_key)->where('id', $request->emp_id)->delete();

            return response()->json($data ? ['status' => 200, 'isDeleted' => true] : ['status' => 400, 'isDeleted' => false]);

        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getmessage()]);

        }

    }



    //google callender settings



    public function googlecallender(Request $request)

    {

        try {

            // Retrieve the session data

            $check = Session::get('check');



            // UI permissions array destructured

            [

                'CREATE' => $IS_CREATABLE,

                'UPDATE' => $IS_UPDATABLE,

                'DELETE' => $IS_DELETABLE

            ] = $this->permission->ui_permissions($this->module, $check[0]->db_key);



            // Fetch payment settings data

            $data = DB::connection($check[0]->db_key)

                ->table('google_callender_setting')

                ->whereNull('deleted_at')

                ->first();







            // Return response based on data existence

            if ($data) {

                return response()->json(['status' => 200, 'data' => $data]);

            } else {

                return response()->json(['status' => 404, 'message' => 'Data Not Found']);

            }



        } catch (Exception $e) {

            // Return error response with proper message

            return response()->json(['status' => 500, 'error' => $e->getMessage()]);

        }

    }



    public function googlecallenderstore(Request $request)

    {

        try {

            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store'], $check[0]->db_key);

            // $loginn_id = $check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;

            $currentDateTime = now();

            if (!$request->google_data_id) {

                $fromdate = $request->txtDateFrom;

                $todate = $request->txtDateTo;

                $formattedDateTime = date('Y-m-d H:i:s', strtotime($fromdate));

                $toformattedDateTime = date('Y-m-d H:i:s', strtotime($todate));

                // Insert data

                DB::connection($check[0]->db_key)

                    ->table('google_callender_setting')

                    ->insert([

                        'google_callender_check' => $request->googlecallender_check,

                        'partner_id' => $loginn_id,

                        'google_callender_id' => $request->googlecallender_id,

                        'google_callender_json' => $request->googlecallender_json,

                        'created_at' => $currentDateTime,

                    ]);



                $message = 'Data has been inserted successfully';

            } else {



                // Update data

                $updateData = [

                    'google_callender_check' => $request->googlecallender_check,

                    'partner_id' => $loginn_id,

                    'google_callender_id' => $request->googlecallender_id,

                    'google_callender_json' => $request->googlecallender_json,

                    'updated_at' => $currentDateTime,

                ];





                $update = DB::connection($check[0]->db_key)

                    ->table('google_callender_setting')

                    ->where('id', $request->google_data_id)

                    ->update($updateData);



                $message = 'Data has been updated successfully';

            }



            return response()->json(['status' => 200, 'message' => $message]);



        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getMessage()]);

        }

    }



    //review setting



    public function reviewlist(Request $request)

    {

        try {

            // Retrieve the session data

            $check = Session::get('check');



            // UI permissions array destructured

            [

                'CREATE' => $IS_CREATABLE,

                'UPDATE' => $IS_UPDATABLE,

                'DELETE' => $IS_DELETABLE

            ] = $this->permission->ui_permissions($this->module, $check[0]->db_key);



            // Fetch payment settings data

            $data = DB::connection($check[0]->db_key)

                ->table('review_setting')

                ->whereNull('deleted_at')

                ->first();







            // Return response based on data existence

            if ($data) {

                return response()->json(['status' => 200, 'data' => $data]);

            } else {

                return response()->json(['status' => 404, 'message' => 'Data Not Found']);

            }



        } catch (Exception $e) {

            // Return error response with proper message

            return response()->json(['status' => 500, 'error' => $e->getMessage()]);

        }

    }





    public function reviewstore(Request $request)

    {

        try {

            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['store'], $check[0]->db_key);

            // $loginn_id = $check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;

            $currentDateTime = now();

            if (!$request->review_id) {

                // Insert data

                DB::connection($check[0]->db_key)

                    ->table('review_setting')

                    ->insert([

                        'review_send_setting' => $request->review_send_setting,

                        'review_send_after_pickup_time' => $request->review_send_after_pickup_time,

                        'partner_id' => $loginn_id,

                        'review_subject' => $request->review_subject,

                        'review_template' => $request->review_request_template,

                        'created_at' => $currentDateTime,

                    ]);



                $message = 'Data has been inserted successfully';

            } else {



                // Update data

                $updateData = [

                    'review_send_setting' => $request->review_send_setting,

                    'review_send_after_pickup_time' => $request->review_send_after_pickup_time,

                    'partner_id' => $loginn_id,

                    'review_subject' => $request->review_subject,

                    'review_template' => $request->review_request_template,

                    'updated_at' => $currentDateTime,

                ];





                $update = DB::connection($check[0]->db_key)

                    ->table('review_setting')

                    ->where('id', $request->review_id)

                    ->update($updateData);



                $message = 'Data has been updated successfully';

            }



            return response()->json(['status' => 200, 'message' => $message]);



        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getMessage()]);

        }

    }





    public function countrylists()

    {



        try {

            $check = Session::get('check');

            $this->permission->check_privilege($this->module, self::ACTION_TYPE['index'], $check[0]->db_key);

            // $loginn_id=$check[0]->id;

            $loginn_id = Partners::where('db_key', $check[0]->db_key)->select('id')->first()->id ?? null;



            // dd($loginn_id);

            //UI permissions array destructured

            [

                'CREATE' => $IS_CREATABLE,

                'UPDATE' => $IS_UPDATABLE,

                'DELETE' => $IS_DELETABLE

            ] = $this->permission->ui_permissions($this->module, $check[0]->db_key);



            $data = DB::connection($check[0]->db_key)

                ->table('countrylists')

                ->get();



            if ($data) {

                return response()->json(['status' => 200, 'data' => $data]);

            } else {

                return response()->json(['status' => 404, 'message' => "Data Not Found"]);

            }



        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getmessage()]);

        }

    }


    public function countryWebsites(Request $request)

    {

        try {

            $check = Session::get('check');

            $search = $request->search;



            if ($search == '') {

                $clients = DB::connection($check[0]->db_key)->table('gentral_setting')->select('id', 'website_prefix', 'country')->whereNull('deleted_at')->orderby('website_prefix', 'asc')->select('id', 'website_prefix', 'country')->limit(6)->get();

            } else {

                $clients = DB::connection($check[0]->db_key)->table('gentral_setting')->select('id', 'website_prefix', 'country')->whereNull('deleted_at')->orderby('website_prefix', 'asc')->select('id', 'website_prefix', 'country')->where('website_prefix', 'like', '%' . $search . '%')->limit(6)->get();

            }



            $response = [];

            foreach ($clients as $client) {

                $response[] = array(

                    "id" => $client->id,

                    "text" => $client->website_prefix . " (" . $client->country . ")",

                    'country' => $client->country

                );

            }



            return response()->json(['data' => $response]);

            // return response()->json($response);



        } catch (Exception $e) {

            return response()->json(['status' => 500, 'error' => $e->getMessage()]);

        }

    }



    public function query_change(Request $request)

    {

        try {

            // Fetch the list of database keys

            $dbkeys = DB::connection('mysql')->table('partnerlists')->pluck('db_key'); // Use pluck for a simpler array

            return $dbkeys;

            foreach ($dbkeys as $dbkey) {

                // Ensure the connection exists before trying to use it

                if (config("database.connections.$dbkey")) {

                    try {

                        // Perform the SQL query on the current database

                        DB::connection($dbkey)->statement('ALTER TABLE gentral_setting MODIFY COLUMN trading_name INT');

                    } catch (\Exception $innerException) {

                        // Log individual database errors but continue the loop

                        \Log::error("Error changing column in $dbkey: " . $innerException->getMessage());

                    }

                } else {

                    \Log::warning("Database connection for key $dbkey not found.");

                }

            }



            return response()->json(['message' => 'Column name and type change attempted for all databases.']);



        } catch (\Exception $e) {

            return response()->json(['message' => 'Error during operation: ' . $e->getMessage()], 500);

        }

    }







}

