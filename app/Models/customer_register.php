<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class customer_register extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'customer_register';
    protected $filllable = ['user','pass','password','roll_id','created_by','referred_by','ref_his_id','name','dialCode',
            'mobile','mobile_verify','email','email_verify','dob','passport','passport_expiry','img_url','deletes','status','otp','created_at',
            't_point','f_points','t_earning','cash_points','bonus_points','account_name','acctype','account_no','bank_name','bank_address',
            'branch_code','branch_name','swift_code','IBAN_code','currency_code','my_referral_code','building_name','city','address','nationality',
            'customer_id','residinglocation','updated_at','ip','delete_req','delete_request','ticket_purchased','ticket_count','lastlogin',
            'deviceType','exchangeid','is_whatsapp', 'isBidding','isOpenjob', 'owner_id'];


}