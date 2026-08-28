<?php



namespace App\Http\Middleware;



use Closure;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\{Cookie, DB, Auth, Gate, Log, Session, Http};

use Illuminate\Support\Arr;

use Illuminate\Support\Facades\View;

// use Symfony\Component\HttpFoundation\Response;

use App\Models\{Partners, EmployeesLogin, User};

use ReflectionClass;





class SessionCheck
{

    public function handle(Request $request, Closure $next)
    {



        $domain = preg_replace('/^www\./', '', $request->getHost());

        //  dd($domain);



        $checkDomainState = Http::withHeaders([

            'Content-Type' => 'application/json',

        ])->post(env('API_GORIDE'), [

                    'mainDomain' => $domain

                ]);



        // dd($checkDomainState->json());


        if (!$checkDomainState->successful() || !$checkDomainState->json()) {

            return redirect('/expiry');

        }
        



        $checkDomainState = $checkDomainState->json();
        
        // dd($checkDomainState);

        if ($checkDomainState['data']['crmDetails']['active_status'] == 1) {
            setcookie('crm_active', $checkDomainState['data']['crmDetails']['fullDomain'], time() + 3600, '/');
            return redirect('/expiry');
        }
        
        setcookie('crm_active', '', time() - 3600, '/');

        if ($checkDomainState['status'] == 'failed') {

            return redirect('/expiry');

        }
        // dd($checkDomainState['status']);
        $plan_details = null;
        if ($checkDomainState['status'] == 'success') {

            // dd($checkDomainState['data']['subDetails']['pStatus'] ?? '');

            $plan_details = $checkDomainState['data']['subDetails']['cart']['productDetails'] ?? '';
            Session::put('plan_details', $plan_details);
            $plan = session('plan_details');
            
            $stats = $checkDomainState['data']['subDetails']['pStatus'] ?? '';

            // dd($plan_details);
            if (empty($stats) || strtolower($stats) != 'active') {

                return redirect('/expiry');

            }

        }






        if ($request->is('login') || $request->is('register')) {

            return $next($request);

        }


        

        $path = $request->path();

       

        if (Arr::has($_COOKIE, 'd_token')) {



            $check = Partners::where('email', $_COOKIE['user_email'])->where('domain_name', $_COOKIE['domainName'])

                ->select('db_key', 'email')

                ->first();

                // dd(  $check );

            if (!$check) {


         


                $check = EmployeesLogin::where('employees.email', $_COOKIE['user_email'])

                    ->join('partnerlists', 'employees.partner_id', '=', 'partnerlists.id')

                    ->select('partnerlists.db_key', 'partnerlists.id', 'employees.email')

                    ->first();

                // dd($path);


            }

 
            if (!$check) {

                // dd('hello');
                
                foreach ($_COOKIE as $key => $value) {
                    Cookie::queue(Cookie::forget($key));
                }


                session()->forget('check');

                session(['redirect_url' => '/login']);

                return redirect('/login');

            } else {

        
                // dd('hello');
                // dd('hii');

                session(['check' => $check]);



                $url = env('API_URL') . 'gate_get';

                $get_parrent_menu = [];


             

                $current_uri = $_SERVER['REQUEST_URI'];

                $data = [

                    'device_id' => '0',

                    'token' => $_COOKIE['d_token'],

                    'db_key' => $check->db_key ?? '',

                    'email' => $check->email ?? '',
                    
                    'plan_details' => $plan ?? '',

                    'current_uri' => $current_uri ?? null,



                ];



                $result = Http::post($url, $data);


            
                $result = $result->json();
                // dd(  $result ,$_COOKIE['d_token'] , $data);


                if ($result) {

                    $permissions = [];

                    $message = $result ? ($result['message'] != 'Unauthorized' ? $result['message'] : []) : [];

                    foreach ($message as $module => $actions) {

                        // dd($actions);

                        foreach ($actions as $action => $allowed) {

                            $permissions["{$module}_{$action}"] = $allowed;

                        }

                        Session::put('new_permission', $permissions);

                        Session::save();

                    }



                    $check_status = $result ? $result['status'] : null;

                    if ($check_status) {

                        $redirectUrl = $request;

                        $url = env('API_URL') . 'condition_check';

                        $result = (object) $this->postHttp($url, $data);


                        // dd($result);
                        
                        if ($result->status == true) {
                            
                            $book_sets = $result->book_sets;
                            $countries = $result->countries??[];
                            $myCountry = $result->myCountry??'';
                            $myDial = $result->myDial??'';
                            $myNation = $result->myNation??'';
                            $mySymbol = $result->mySymbol??'';

                            session(['redirect_url' => $result->url]);
                            
                            if (isset($result->driver_count)) {
                                $vehicle_count = str_pad($result->driver_count + 1 , 6, '0', STR_PAD_LEFT);
                            } else {
                                $vehicle_count = '000000';
                            }
                            
                            View::share(['book_sets' => $book_sets, 'allCountries' => $countries, 'myCountry' => $myCountry, 'myDial' => $myDial, 'myNation' => $myNation, 'mySymbol' => $mySymbol]);
                            
                            session(['driver_count' => $vehicle_count ?? 0]);
                            // Session::put('plan_details', $plan_details);

                            return $next($request);

                        }





                    } else {

                        foreach ($_COOKIE as $key => $value) {

                            Cookie::queue(Cookie::forget($key));

                        }

                        session()->forget('check');

                        session()->forget('new_permission');

                        session(['redirect_url' => '/login']);

                        return redirect('/login');

                    }



                }



            }

        } else {
            
            foreach ($_COOKIE as $key => $value) {

                Cookie::queue(Cookie::forget($key));

            }

            session()->forget('check');

            session()->forget('new_permission');

            session(['redirect_url' => '/login']);

            return redirect('/login');

        }





    }






    public function postHttp($url, $data)
    {



        $response = Http::post($url, $data);

        // return $response->json()  ;

        if ($response->successful()) {

            // dd($response->json());

            $result = $response->json() ?? [];

        } elseif ($response->clientError()) {

            $result = [];

        } elseif ($response->serverError()) {

            $result = [];

        } else {

            $result = [];

        }

        return $result;



    }

}

