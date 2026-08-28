<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Api\PlayController;
class ValidateToken
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
        $inputData = $request->all();
        // Validate the token
        if ($this->isValidToken($token)) {
            $userDetails = null;
            // $walletTicketGenerate = $onlineTicket->dashboard($walletTicketReq);
            $walletTicketGenerate = \Http::withHeaders([
                'Authorization' => "Bearer " . $token,
            ])->post(url('/api/dashboard'));
            // dd($walletTicketGenerate);
            if ($walletTicketGenerate->successful()) {
                $walletTicketData = $walletTicketGenerate->json(); // Decode JSON response
                if (isset($walletTicketData['status']) && $walletTicketData['status'] === 'success') {
                    $userDetails = $walletTicketData['data'] ?? null;
                }
            }
            view()->share('userDetails', $userDetails);
            return $next($request);
        }
        setrawcookie("sessionToken", "", time() - 3600, "/");
        
        unset($_COOKIE['sessionToken']);
        // dd( $inputData);
        return redirect('/login')->with('error', 'Invalid session token.')->withInput($inputData);
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



        // Check if the token is expired
        $expiresAt = $tokenRecord->created_at->addMinutes(config('sanctum.expiration')); // Assuming expiration is set in config
        return Carbon::now()->lt($expiresAt);
    }
}
