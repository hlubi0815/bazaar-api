<?php

namespace App\Http\Controllers;

use App\Bazaar;
use App\ReceiptItem;
use App\SaleNumber;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Validator;

class BazaarController extends Controller
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

    public function listAllUsersUpcoming(){
        $bazaar = Bazaar::with('users')->whereDate('bazaardate','>=',Carbon::now()->toDateString())->orderBy('bazaardate','DESC')->first();
        if(!empty($bazaar)){
            $nextFreelistnumber = DB::select('SELECT t1.sale_number+1 AS FREElistnumber FROM sale_numbers AS t1 LEFT JOIN sale_numbers AS t2 ON t1.sale_number+1 = t2.sale_number WHERE t2.sale_number IS NULL ORDER BY t1.sale_number LIMIT 1');
            if(count($nextFreelistnumber)>0)
                $bazaar->nextFreelistnumber = $nextFreelistnumber[0]->FREElistnumber;
            else
                $bazaar->nextFreelistnumber = 200;
           return $bazaar;
        }
    }


    public function listAllUpcoming(){
        $bazaars = Bazaar::all()->sortByDesc('bazaardate');
        $result = array();
        foreach($bazaars as $bazaar){
            array_push($result,$bazaar);
        }


        return $result;
    }

    public function getSales($bazaar_id){
        $bazaar = Bazaar::where('id',$bazaar_id)->findOrFail($bazaar_id);;



        $users = $bazaar->users()->orderBy('sale_number','ASC')->get();

        $result = array();

        $providerCount = 0;
        $providerSupportCount=0;
        $proivderNonSupportCount=0;
        $earnings_fee = 0.0;
        $total = 0.0;
        $earningsNonSupporterPercentag = 0.0;


        foreach ($users as $user){
            //$providerCount = $providerCount + 1;
            $earnings_fee = $earnings_fee + $bazaar->fee;
            $sale_number = $user->pivot->sale_number;
            $sum_sold = ReceiptItem::where('sale_number', $sale_number)->sum('amount');
            $total = $total + $sum_sold;

            if($user->supporter){
                $providerSupportCount = $providerSupportCount + 1;
            } else {
                $proivderNonSupportCount = $proivderNonSupportCount +1;
                $earningsNonSupporterPercentag = $earningsNonSupporterPercentag + ($sum_sold * ($bazaar->percentageoff/100));
            }




       //     $items_sold = ReceiptItem::where('sale_number', $sale_number)->get();
       /*     $sum_sold = ReceiptItem::where('sale_number', $sale_number)->sum('amount');


            $user->items_sold = count($items_sold);
            $user->total = $sum_sold;
            $user->fee = $bazaar->fee;
            $user->percentageoff = $bazaar->percentageoff;
            $user->items = $items_sold;
            array_push($result,$user);
       */
        }
        $bazaar->providerSupportCount = $providerSupportCount;
        $bazaar->proivderNonSupportCount = $proivderNonSupportCount;
        $bazaar->providerCount = $users->count();
        $bazaar->earnings_fee = $earnings_fee;
        $bazaar->total = $total;
        $bazaar->earningsNonSupporterPercentag = $earningsNonSupporterPercentag;
        $bazaar->earningscake=10.0;

        return $bazaar;



/*
        console.log("VALUE RECEIVED: ", res.earnings_fee);
        this.doughnutChartData[0] = res.earnings_fee;
        this.doughnutChartData[1] = res.earningsNonSupporterPercentag;
        this.doughnutChartData[2] = res.earningscake;
        this.earningscake = res.earningscake;
        this.providerCount = res.providerCount;
        this.providerSupportCount = res.providerSupportCount;
        this.proivderNonSupportCount = res.proivderNonSupportCount;
        this.total = res.total;
        this.earnings_fee = res.earnings_fee;
        this.earningsNonSupporterPercentag = res.earningsNonSupporterPercentag;
        this.dataReady = true;
        */

    }

    public function listAllUsers($bazaar_id){
        return Bazaar::with('users')->findOrFail($bazaar_id);
    }

    public function listAllItems($bazaar_id){
        return Bazaar::with('items')->findOrFail($bazaar_id);
    }

    public function listItemsOfUser($bazaar_id,$userid){
        return Bazaar::with([ 'items' => function($query) use ($userid){
            $query->where('user_id', $userid);
        }])->findOrFail($bazaar_id);
    }

    public function listRegisteredSaleNumbers(){
        $upcomingBazaar = Bazaar::whereDate('bazaardate','>=',Carbon::now()->toDateString())->orderBy('bazaardate','DESC')->first();
        $saleNumbers =$upcomingBazaar->saleNumbers()->orderBy('sale_number','DESC')->get();


        $res =  array( );
        foreach ($saleNumbers as $number){
            $res[] = $number->sale_number;
        }

        return $res;


    }


    public function listSaleNumbers(){
        $upcomingBazaar = Bazaar::whereDate('bazaardate','>=',Carbon::now()->toDateString())->orderBy('bazaardate','DESC')->first();

        $bazaar=Bazaar::where('id',$upcomingBazaar->id)->get()->first();

        $start = $bazaar->listnumber_start;
        $end = $bazaar->listnumber_end;

        $bazaar_numbers = range($start, $end);


        foreach (SaleNumber::where('bazaar_id', $upcomingBazaar->id)->get() as $regSaleNumber){
            $del_val = $regSaleNumber->sale_number;
            if (($key = array_search($del_val, $bazaar_numbers)) !== false) {
                unset($bazaar_numbers[$key]);
            }
        }
        $res =  array( );
        foreach ($bazaar_numbers as $number){
            $res[] = ['id' => $number, 'itemName' => $number];
        }

        return $res;
    }


    public function addBazaar()
    {
        $rules = [
            'name' => 'required',
            'bazaarDate' => 'required',
            'listnumber_start' => 'required',
            'listnumber_end' => 'required'
        ];

        $input = Input::only(
            'name',
            'bazaarDate',
            'listnumber_start',
            'listnumber_end',
            'change',
            'fee'

        );

        $messages = [
            'required' => 'Das Feld :attribute wird benötigt!',
            'unique' => ':attribute wurde schon verwendet ! ',
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



        try {

             Bazaar::update(
                [
                    'id' => Input::get('id'),
                    'name' => Input::get('name'),
                    'bazaarDate' => Input::get('bazaarDate'),
                    'listnumber_start' => Input::get('listnumber_start'),
                    'listnumber_end' => Input::get('listnumber_end'),
                    'change'=>Input::get('change'),
                    'fee'=>Input::get('fee'),
                ]);



        } catch (\PDOException $e){
            DB::rollback();
            $error = (string)$e->getBindings()[2] ;
            Log::info('error in transacation:' . $error);
            return response()->json( ["error"=>"Leider gab es ein problem beim anlegen des Bazaar: ".$error . " ","item"=>$error,"issue"=>""], '400');
        }

        DB::commit();

        return response()->json('Bazaar angelegt');
    }


}
