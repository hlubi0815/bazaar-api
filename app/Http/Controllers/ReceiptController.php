<?php

namespace App\Http\Controllers;

use App\Bazaar;
use App\Receipt;
use App\ReceiptItem;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;


class ReceiptController extends Controller
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

    public function createReceipt()
    {
        $bazaar = Bazaar::whereDate('bazaardate','>=',Carbon::now()->toDateString())->orderBy('bazaardate','DESC')->first();

        $receipt = Receipt::create(array('bazaar_id'=>$bazaar->id));


        return $receipt;
    }

    public function addItem($receipt_id)
    {
        $input = Input::only(
            'sale_number',
            'item_number',
            'amount'
        );


        $receipt = Receipt::find($receipt_id);
        $receipt_item = ReceiptItem::create(array(
            'receipt_id' => $receipt->id,
            'sale_number'=>Input::get('sale_number'),
            'amount'=>Input::get('amount'),
            'item_number'=>Input::get('item_number'),
        ));

        return $receipt_item;
    }

    public function delItem($receiptId, $itemId){
        $receipt = Receipt::find($receiptId);
        $receiptItem = $receipt->receipt_items()->where('id','=',$itemId)->first();
        $receiptItem->delete();
        return ['status'=>'success'];
    }

    public function editItem($receiptId, $itemId){
        $input = Input::only(
            'sale_number',
            'item_number',
            'amount'
        );


        $receipt = Receipt::find($receiptId);
        $receiptItem = $receipt->receipt_items()->where('id','=',$itemId)->first();
        $receiptItem->sale_number = Input::get('sale_number');
        $receiptItem->item_number = Input::get('item_number');
        $receiptItem->amount = Input::get('amount');
        $receiptItem->save();
        return ['status'=>'success'];
    }

    public function getAllReceipts(){
        $bazaar = Bazaar::whereDate('bazaardate','>=',Carbon::now()->toDateString())->orderBy('bazaardate','DESC')->first();

        $result = array();

        foreach ($bazaar->receipts()->where('settled','=',true)->get() as $receipt){
            $sum = $receipt->receipt_items()->sum('amount');
            $receipt->sum = $sum;
            array_push($result,$receipt);
        }
        return $result;
    }

    public function getReceiptItems($receiptId){
        $receipt = Receipt::find($receiptId);

        return $receipt->receipt_items()->get();
    }

    public function settleReceipt($receiptId){
        $receipt = Receipt::find($receiptId);
        $receipt->settled = true;
        $receipt->save();

        return ['status'=>'settled'];
    }

    public function processSettlement(){
        $bazaar = Bazaar::whereDate('bazaardate','>=',Carbon::now()->toDateString())->orderBy('bazaardate','DESC')->first();

        $users = $bazaar->users()->orderBy('sale_number','ASC')->get();

        $result = array();

        foreach ($users as $user){
            $sale_number = $user->pivot->sale_number;
            $items_sold =0;
            $sum_sold = 0.0;
            $itemsArray = DB::table('receipt_items')
                ->join('receipts', 'receipts.id' ,'=', 'receipt_items.receipt_id')
                ->where([['receipts.settled','=','1'],['receipt_items.sale_number','=',$sale_number]])->get();





            //$items_sold = ReceiptItem::where('sale_number', $sale_number)->get();
            foreach($itemsArray as $receipt_item){
                $items_sold = $items_sold + 1;
                $sum_sold = $sum_sold + $receipt_item->amount;
            }

            $user->items_sold = $items_sold;
            $user->total = $sum_sold;
            $user->fee = $bazaar->fee;
            $user->percentageoff = $bazaar->percentageoff;
            $user->items = $itemsArray;
            array_push($result,$user);
        }

        return $result;
    }




}
