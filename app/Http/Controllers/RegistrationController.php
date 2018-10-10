<?php

namespace App\Http\Controllers;

use Log;
use App\Bazaar;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\SaleNumber;
use Illuminate\Support\Facades\Input;
use Validator;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    public function sendEmail($email)
    {
        //$user = User::where('email', $email)->first();

        Mail::send('emails.hello', array('name' => 'matthias'), function($message)
        {
            $message->to('hlubi0815@gmail.com', 'Matthias')->subject('Welcome!');
        });
    }

    public function createUserInUpcomingBasar(){
        $rules = [
            'firstName' => 'required',
            'lastName' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'salenumber' => 'required|array|between:1,3'
        ];

        $input = Input::only(
            'firstName',
            'lastName',
            'phone',
            'email',
            'salenumber',
            'supporter'
        );

        $messages = [
            'salenumber.required' => 'Bitte wählen Sie min. 1 Basarnummer',
            'required' => 'Das Feld :attribute wird benötigt!',
            'unique' => ':attribute wurde schon verwendet !',
            'email' => 'Die email ist nicht gültig',
        ];

        $validator = Validator::make($input, $rules, $messages);

        if($validator->fails())
        {
            $errors = $validator->errors();
            $error_message = '';
            foreach ($errors->all() as $message) {
                if ($message )
                    $error_message = $error_message . " ". $message;
            }
            return response()->json( ["error"=>$error_message], '400');
        } else {
            $user = User::where('email', Input::get('email'))->first();
            if ($user == null )
            {
                $user =User::create([
                    'firstName' => Input::get('firstName'),
                    'lastName' => Input::get('lastName'),
                    'phone' => Input::get('phone'),
                    'email' => Input::get('email'),
                    'data_deletion'=>1,
                    'data_usage'=>1,
                    'next_bazaar'=>0,
                    'supporter' =>Input::get('supporter'),
                    'registration_channel' => 0
                ]);
            }

            $upcomingBazaar = Bazaar::whereDate('bazaardate','>=',Carbon::now()->toDateString())->orderBy('bazaardate','DESC')->first();

            foreach ( Input::get('salenumber') as $number ){
                SaleNumber::create([
                    'bazaar_id' => $upcomingBazaar->id,
                    'user_id' => $user->id,
                    'sale_number' => $number
                ]);

                $upcomingBazaar->users()->attach($user->id, ['sale_number' => $number]);
            }

            Mail::send('emails.registered',array('salenumber' => Input::get('salenumber')), function($message) {
                $message->to(Input::get('email'), Input::get('name'))
                    ->subject('Basar Registrierung abgeschlossen');
            });
        }
        $nextFreelistnumber = DB::select('SELECT t1.sale_number+1 AS FREElistnumber FROM sale_numbers AS t1 LEFT JOIN sale_numbers AS t2 ON t1.sale_number+1 = t2.sale_number WHERE t2.sale_number IS NULL ORDER BY t1.sale_number LIMIT 1');


        return response()->json(['nextFreelistnumber'=>$nextFreelistnumber[0]->FREElistnumber]);
    }

    public function changeUserInUpcomingBasar(){
        $rules = [
            'firstName' => 'required',
            'lastName' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'salenumber' => 'required|array|between:1,1'
        ];

        $input = Input::only(
            'firstName',
            'lastName',
            'phone',
            'email',
            'salenumber',
            'old_salenumber',
            'supporter'
        );

        $messages = [
            'salenumber.required' => 'Bitte wählen Sie min. 1 Basarnummer',
            'required' => 'Das Feld :attribute wird benötigt!',
            'unique' => ':attribute wurde schon verwendet !',
            'email' => 'Die email ist nicht gültig',
        ];

        $validator = Validator::make($input, $rules, $messages);

        if($validator->fails())
        {
            $errors = $validator->errors();
            $error_message = '';
            foreach ($errors->all() as $message) {
                if ($message )
                    $error_message = $error_message . " ". $message;
            }
            return response()->json( $error_message, '400');
        } else {
            $user = User::where('email',Input::get('email'))->first();
            $user->firstName = Input::get('firstName');
            $user->lastName = Input::get('lastName');
            $user->phone = Input::get('phone');
            $user->email = Input::get('email');
            $user->supporter = Input::get('supporter');
            $user->save();

            $upcomingBazaar = Bazaar::whereDate('bazaardate','>=',Carbon::now()->toDateString())->orderBy('bazaardate','DESC')->first();
            $oldSn =Input::get('old_salenumber')[0];
            $newSn = Input::get('salenumber')[0];
            $saleNumber = SaleNumber::where('bazaar_id', $upcomingBazaar->id)->where('sale_number', $oldSn)->first();
            $saleNumber->sale_number = $newSn;
            $saleNumber->save();


            $pivot_saleNumber = $upcomingBazaar->users()->wherePivot('sale_number',$oldSn)->updateExistingPivot($user->id, ['sale_number' => $newSn]);

        }
        return response()->json("Verkäufer Daten geändert");
    }

    public function deleteUserInUpcomingBasar($salenumber){


        $upcomingBazaar = Bazaar::whereDate('bazaardate','>=',Carbon::now()->toDateString())->orderBy('bazaardate','DESC')->first();
        $sn =$upcomingBazaar->saleNumbers()->where('sale_number',$salenumber)->first();
        $sn->delete();


        $upcomingBazaar->users()->wherePivot('sale_number',$salenumber)->detach();
        $nextFreelistnumber = DB::select('SELECT t1.sale_number+1 AS FREElistnumber FROM sale_numbers AS t1 LEFT JOIN sale_numbers AS t2 ON t1.sale_number+1 = t2.sale_number WHERE t2.sale_number IS NULL ORDER BY t1.sale_number LIMIT 1');


        return response()->json(['nextFreelistnumber'=>$nextFreelistnumber[0]->FREElistnumber]);
    }



    public function registerUser()
    {
        $rules = [
            'firstName' => 'required',
            'lastName' => 'required',
            'phone' => 'required',
            'email' => 'required|email|unique:users',
            # 'password' => 'required|confirmed|min:6',
            'salenumber' => 'required|array|between:1,3'
        ];

        $input = Input::only(
            'firstName',
            'lastName',
            'phone',
            'email',
            #'password',
            #'password_confirmation',
            'data_deletion',
            'data_usage',
            'next_bazaar',
            'salenumber',
            'bazaarid'

        );

        $messages = [
            'salenumber.required' => 'Bitte wählen Sie min. 1 Basarnummer',
            'required' => 'Das Feld :attribute wird benötigt!',
            'unique' => ':attribute wurde schon verwendet ! ',
            'email' => 'Die email ist nicht gültig',
            'confirmed' => 'Die Password Bestätigung stimmt nicht überein mit dem Password',
        ];

        $validator = Validator::make($input, $rules, $messages);

        if($validator->fails())
        {
            $errors = $validator->errors();
            $error_message = '';
            foreach ($errors->all() as $message) {
                if ($message )
                    $error_message = $error_message . "  ". $message;
            }
            return response()->json( ["error"=>$error_message], '400');
        }



        DB::beginTransaction();
        $confirmation_code = str_random(30);

        $user =User::create([
                'firstName' => Input::get('firstName'),
                'lastName' => Input::get('lastName'),
                'phone' => Input::get('phone'),
                'email' => Input::get('email'),
                #'password' => app('hash')->make(Input::get('password')),
                'confirmation_code' => $confirmation_code,
                'data_deletion'=>Input::get('data_deletion'),
                'data_usage'=>Input::get('data_usage'),
                'next_bazaar'=>Input::get('next_bazaar'),
                'registration_channel' => 1
            ]);

            try {

                $bazaar = Bazaar::where('id', Input::get('bazaarid'))->first();
                foreach (Input::get('salenumber') as $number) {
                    Log::info('Adding user: '.Input::get('firstName').' '.Input::get('lastName').' - '.Input::get('phone'). ' - ' .Input::get('email') .' registered number: '.$number['id']);
                    SaleNumber::create([
                        'bazaar_id' => $bazaar->id,
                        'user_id' => $user->id,
                        'sale_number' => $number['id']
                    ]);

                    $bazaar->users()->attach($user->id, ['sale_number' => $number['id']]);
                }



            } catch (\PDOException $e){
                DB::rollback();
                $error = (string)$e->getBindings()[2] ;
                Log::info('error in transacation:' . $error);
                return response()->json( ["error"=>"Leider gab es ein problem bei der Reservierung von Nummer: ".$error . " diese scheint schon vergeben zu sein bitte eine andere auswählen!","item"=>$error,"issue"=>"duplicate_salenumber"], '400');
            }

        DB::commit();

        Mail::send('emails.verify',array('confirmation_code' => $confirmation_code, 'salenumber' => Input::get('salenumber')), function($message) {
            $message->to(Input::get('email'), Input::get('name'))
                ->subject('Basar Registrierung abschliessen');
        });
        return response()->json('Vielen Dank für die Registrierung, bitte checken Sie Ihre email um die Registrierung abzuschliessen');
    }

    public function confirm($confirmation_code)
    {
        if( ! $confirmation_code)
        {
            return view('registration.error');
        }

        $user = User::whereConfirmationCode($confirmation_code)->first();

        if ( ! $user)
        {
            return view('registration.error');
        }

        $user->confirmed = 1;
        $user->confirmation_code = null;
        $user->save();

        $upcomingBazaar = Bazaar::whereDate('bazaardate','>=',Carbon::now()->toDateString())->orderBy('bazaardate','DESC')->first();
        $salenumbers=SaleNumber::where('user_id',$user->id)->where('bazaar_id',$upcomingBazaar->id)->get();

        return view('registration.confirmation', ['salenumber'=>$salenumbers]);


    }
}
