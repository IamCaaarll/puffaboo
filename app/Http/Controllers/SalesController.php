<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\SalesDetail;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PDF;

class SalesController extends Controller
{
    public function index()
    {

        Log::info('Response Session Trans ID'. session('sale_id'));

        return view('sales.index');
    }

    public function data()
    {
        $sales = Sales::with('member')->orderBy('sale_id', 'desc')->get();

        return datatables()
            ->of($sales)
            ->addIndexColumn()
            ->addColumn('total_item', function ($sales) {
                return format_money($sales->total_item);
            })
            ->addColumn('total_price', function ($sales) {
                return '₱ '. format_money($sales->total_price);
            })
            ->addColumn('payment', function ($sales) {
                return '₱ '. format_money($sales->payment);
            })
            ->addColumn('date', function ($sales) {
                return us_date($sales->created_at, false);
            })
            ->addColumn('member_code', function ($sales) {
                $member = $sales->member->member_code ?? '';
                return '<span class="label label-success">'. $member .'</span>';
            })
            ->editColumn('diskon', function ($sales) {
                return $sales->discount . '%';
            })
            ->editColumn('cashier', function ($sales) {
                return $sales->user->name ?? '';
            })
            ->addColumn('action', function ($sales) {
                return '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('sales.show', $sales->sale_id) .'`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="deleteData(`'. route('sales.destroy', $sales->sale_id) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['action', 'member_code'])
            ->make(true);
    }
    
    public function create()
    {
        $sales = new Sales();
        $sales->member_id = null;
        $sales->total_item = 0;
        $sales->total_price = 0;
        $sales->discount = 0;
        $sales->payment = 0;
        $sales->received = 0;
        $sales->user_id = auth()->id();
        $sales->save();

        session(['sale_id' => $sales->sale_id]);
        return redirect()->route('transaction.index');
    }

    public function store(Request $request)
    {
        $sales = Sales::findOrFail($request->sale_id);
        $sales->member_id = $request->member_id;
        $sales->total_item = $request->total_item;
        $sales->total_price = $request->total;
        $sales->discount = $request->discount;
        $sales->payment = $request->payment;
        $sales->received = $request->received;
        $sales->update();

        $detail = SalesDetail::where('sale_id', $sales->sale_id)->get();
        foreach ($detail as $item) {
            $item->discount = $request->discount;
            $item->update();

            $product = Product::find($item->product_id);
            $product->stock -= $item->quantity;
            $product->update();
        }

        return redirect()->route('transaction.completed');
    }

    public function show($id)
    {
        $detail = SalesDetail::with('product')->where('sale_id', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('product_code', function ($detail) {
                return '<span class="label label-success">'. $detail->product->product_code .'</span>';
            })
            ->addColumn('product_name', function ($detail) {
                return $detail->product->product_name;
            })
            ->addColumn('selling_price', function ($detail) {
                return '₱ '. format_money($detail->selling_price);
            })
            ->addColumn('quantity', function ($detail) {
                return format_money($detail->quantity);
            })
            ->addColumn('subtotal', function ($detail) {
                return '₱ '. format_money($detail->subtotal);
            })
            ->rawColumns(['product_code'])
            ->make(true);
    }
    
    public function destroy($id)
    {
        $sales = Sales::find($id);
        $detail = SalesDetail::where('sale_id', $sales->sale_id)->get();
        foreach ($detail as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->stock += $item->quantity;
                $product->update();
            }

            $item->delete();
        }

        $sales->delete();

        return response(null, 204);
    }

    public function completed()
    {
        $setting = Setting::first();

        return view('sales.completed', compact('setting'));
    }

    public function smallNote()
    {
        $setting = Setting::first();
        $sales = Sales::find(session('sale_id'));
        if (! $sales) {
            abort(404);
        }
        $detail = SalesDetail::with('product')
            ->where('sale_id', session('sale_id'))
            ->get();
        
        return view('sales.small_note', compact('setting', 'sales', 'detail'));
    }

    public function largeNote()
    {
        $setting = Setting::first();
        $sales = Sales::find(session('sale_id'));
        if (! $sales) {
            abort(404);
        }
        $detail = SalesDetail::with('product')
            ->where('sale_id', session('sale_id'))
            ->get();

        $pdf = PDF::loadView('sales.small_note', compact('setting', 'sales', 'detail'));
        $pdf->setPaper(0,0,609,440, 'potrait');
        return $pdf->stream('Transaction-'. date('Y-m-d-his') .'.pdf');
    }
}
