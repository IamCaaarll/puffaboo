<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Sales;
use App\Models\SalesDetail;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class SalesDetailController extends Controller
{
    public function index()
    {
        $product = Product::orderBy('product_name')->get();
        $member = Member::orderBy('name')->get();
        $discount = Setting::first()->discount ?? 0;

        // Check whether there are any transactions in progress
        if ($sale_id = session('sale_id')) {
            $sales = Sales::find($sale_id);
            $memberSelected = $sales->member ?? new Member();

            return view('sales_detail.index', compact('product', 'member', 'discount', 'sale_id', 'sales', 'memberSelected'));
        } else {
            if (auth()->user()->level == 1) {
                return redirect()->route('transaction.new');
            } else {
                return redirect()->route('home');
            }
        }
    }

    public function data($id)
    {
        $detail = SalesDetail::with('product')
            ->where('sale_id', $id)
            ->get();

        $data = array();
        $total = 0;
        $total_item = 0;

        foreach ($detail as $item) {
            $row = array();
            $row['product_code'] = '<span class="label label-success">'. $item->product['product_code'] .'</span';
            $row['product_name'] = $item->product['product_name'];
            $row['selling_price']  = '₱ '. format_money($item->selling_price);
            $row['quantity']      = '<input type="number" class="form-control input-sm quantity" data-id="'. $item->sales_detail_id .'" value="'. $item->quantity .'">';
            $row['discount']      = $item->discount . '%';
            $row['subtotal']    = '₱ '. format_money($item->subtotal);
            $row['action']        = '<div class="btn-group">
                                    <button onclick="deleteData(`'. route('transaction.destroy', $item->sales_detail_id) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                </div>';
            $data[] = $row;

            $total += $item->selling_price * $item->quantity - (($item->discount * $item->quantity) / 100 * $item->selling_price);;
            $total_item += $item->quantity;
        }
        $data[] = [
            'product_code' => '
                <div class="total hide">'. $total .'</div>
                <div class="total_item hide">'. $total_item .'</div>',
            'product_name' => '',
            'selling_price'  => '',
            'quantity'      => '',
            'discount'      => '',
            'subtotal'    => '',
            'action'        => '',
        ];

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->rawColumns(['action', 'product_code', 'quantity'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $product = Product::where('product_id', $request->product_id)->first();
        if (! $product) {
            return response()->json('Data failed to save', 400);
        }

        $detail = new SalesDetail();
        $detail->sale_id = $request->sale_id;
        $detail->product_id = $product->product_id;
        $detail->selling_price = $product->selling_price;
        $detail->quantity = 1;
        $detail->discount = $product->discount;
        $detail->subtotal = $product->selling_price - ($product->discount / 100 * $product->selling_price);;
        $detail->save();

        return response()->json('Data saved successfully', 200);
    }
    
    public function update(Request $request, $id)
    {
        $detail = SalesDetail::find($id);
        $detail->quantity = $request->quantity;
        $detail->subtotal = $detail->selling_price * $request->quantity - (($detail->discount * $request->quantity) / 100 * $detail->selling_price);;
        $detail->update();
    }

    public function destroy($id)
    {
        $detail = SalesDetail::find($id);
        $detail->delete();

        return response(null, 204);
    }

    public function loadForm($discount = 0, $total = 0, $received = 0)
    {
        $payment = $total - ($discount / 100 * $total);
        $change = ($received != 0) ? $received - $payment : 0;
        $data = [
            'totalrp' => format_money($total),
            'payment' => $payment,
            'paymentrp' => format_money($payment),
            'in_words' => ucwords(toWords($payment) . ' Pesos'),
            'change_rp' => format_money($change),
            'change_in_words' => ucwords(toWords($change) . ' Pesos')
        ];


        return response()->json($data);
    }
}
