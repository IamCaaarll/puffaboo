<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchases;
use App\Models\PurchasesDetail;
use App\Models\Product;
use App\Models\Supplier;

class PurchasesController extends Controller
{
    public function index()
    {
        
        $supplier = Supplier::orderBy('name')->get();

        return view('purchases.index', compact('supplier'));
    }
    
    public function data()
    {
        $purchases = Purchases::orderBy('purchase_id', 'desc')->get();

        return datatables()
            ->of($purchases)
            ->addIndexColumn()
            ->addColumn('total_item', function ($purchases) {
                return format_money($purchases->total_item);
            })
            ->addColumn('total_price', function ($purchases) {
                return '₱ '. format_money($purchases->total_price);
            })
            ->addColumn('payment', function ($purchases) {
                return '₱ '. format_money($purchases->payment);
            })
            ->addColumn('date', function ($purchases) {
                return us_date($purchases->created_at, false);
            })
            ->addColumn('supplier', function ($purchases) {
                return $purchases->supplier->name;
            })
            ->editColumn('discount', function ($purchases) {
                return $purchases->discount . '%';
            })
            ->addColumn('action', function ($purchases) {
                return '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('purchases.show', $purchases->purchase_id) .'`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="deleteData(`'. route('purchases.destroy', $purchases->purchase_id) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create($id)
    {
        $purchases = new Purchases();
        $purchases->supplier_id = $id;
        $purchases->total_item  = 0;
        $purchases->total_price = 0;
        $purchases->discount      = 0;
        $purchases->payment       = 0;
        $purchases->save();

        session(['purchase_id' => $purchases->purchase_id]);
        session(['supplier_id' => $purchases->supplier_id]);

        return redirect()->route('purchases_detail.index');
    }

    public function store(Request $request)
    {
        $purchases = Purchases::findOrFail($request->purchase_id);
        $purchases->total_item = $request->total_item;
        $purchases->total_price = $request->total;
        $purchases->discount = $request->discount;
        $purchases->payment = $request->payment;
        $purchases->update();

        $detail = PurchasesDetail::where('purchase_id', $purchases->purchase_id)->get();
        foreach ($detail as $item) {
            $product = Product::find($item->product_id);
            $product->stock += $item->quantity;
            $product->update();
        }

        return redirect()->route('purchases.index');
    }

    public function show($id)
    {
        $detail = PurchasesDetail::with('product.branch')->where('purchase_id', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('branch_name', function ($detail) {
                return $detail->product->branch->branch_name ;
            })
            ->addColumn('product_code', function ($detail) {
                return '<span class="label label-success">'. $detail->product->product_code .'</span>';
            })
            ->addColumn('product_name', function ($detail) {
                return $detail->product->product_name.' ('.$detail->product->brand.')';
            })
            ->addColumn('purchase_price', function ($detail) {
                return '₱ '. format_money($detail->purchase_price);
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
        $purchases = Purchases::find($id);
        $detail    = PurchasesDetail::where('purchase_id', $purchases->purchase_id)->get();
        foreach ($detail as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->stock -= $item->quantity;
                $product->update();
            }
            $item->delete();
        }

        $purchases->delete();

        return response(null, 204);
    }
}
