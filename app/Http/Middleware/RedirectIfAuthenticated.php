<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $_COOKIE['sessionToken'] ?? '';
// dd($token);
        // Validate the token
        if ($this->isValidToken($token)) {
            

            return redirect('/dashboard');
        }


        setrawcookie("sessionToken", "", time() - 3600, "/");
            unset($_COOKIE['sessionToken']);
            
        return $next($request);
        // return redirect('/dashboard')->with('error', 'Invalid session token.');
    }

    /**
     * Validate the token.
     *
     * @param  string|null  $token
     * @return bool
     */
    protected function isValidToken(?string $token): bool
    {
        if (!$token) {
            return false;
        }

        // Retrieve the token record
        $tokenRecord = PersonalAccessToken::find($token);



        if (!$tokenRecord) {
      
            return false;
        }


 $user = $tokenRecord->tokenable; 
//  dd($user);
        if(!isset($user) || $user == null || $user == ''){
          
              
               return false;
        }

        // $user_id = $user->id ?? null;

       
        $expiresAt = $tokenRecord->created_at->addMinutes(config('sanctum.expiration')); // Assuming expiration is set in config
        return Carbon::now()->lt($expiresAt);
    }
}
