<?php

namespace App\Http\Controllers;

use App\Models\Purchases;
use App\Models\PurchasesDetail;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchasesDetailController extends Controller
{
    public function index()
    {
        $purchase_id = session('purchase_id');
        $product = Product::with('branch')->orderBy('product_name')->get();
        $supplier = Supplier::find(session('supplier_id'));
        $discount = Purchases::find($purchase_id)->discount ?? 0;

        if (! $supplier) {
            abort(404);
        }

        return view('purchases_detail.index', compact('purchase_id', 'product', 'supplier', 'discount'));
    }

    public function data($id)
    {
        $detail = PurchasesDetail::with('product')
            ->where('purchase_id', $id)
            ->get();
        $data = array();
        $total = 0;
        $total_item = 0;

        foreach ($detail as $item) {
            $row = array();
            $row['product_code'] = '<span class="label label-success">'. $item->product['product_code'] .'</span';
            $row['product_name'] = $item->product['product_name'];
            $row['purchase_price']  = '₱ '. format_money($item->purchase_price);
            $row['quantity']      = '<input type="number" class="form-control input-sm quantity" data-id="'. $item->purchase_detail_id .'" value="'. $item->quantity .'">';
            $row['subtotal']    = '₱ '. format_money($item->subtotal);
            $row['action']        = '<div class="btn-group">
                                    <button onclick="deleteData(`'. route('purchases_detail.destroy', $item->purchase_detail_id) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                </div>';
            $data[] = $row;

            $total += $item->purchase_price * $item->quantity;
            $total_item += $item->quantity;
        }
        $data[] = [
            'product_code' => '
                <div class="total hide">'. $total .'</div>
                <div class="total_item hide">'. $total_item .'</div>',
            'product_name' => '',
            'purchase_price'  => '',
            'quantity'      => '',
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

        $detail = new PurchasesDetail();
        $detail->purchase_id = $request->purchase_id;
        $detail->product_id = $product->product_id;
        $detail->purchase_price = $product->purchase_price;
        $detail->quantity = 1;
        $detail->subtotal = $product->purchase_price;
        $detail->save();

        return response()->json('Data saved successfully', 200);
    }
    
    public function update(Request $request, $id)
    {
        $detail = PurchasesDetail::find($id);
        $detail->quantity = $request->quantity;
        $detail->subtotal = $detail->purchase_price * $request->quantity;
        $detail->update();
    }

    public function destroy($id)
    {
        $detail = PurchasesDetail::find($id);
        $detail->delete();

        return response(null, 204);
    }

    public function loadForm($discount, $total)
    {
        $payment = $total - ($discount / 100 * $total);
        $data  = [
            'totalrp' => format_money($total),
            'payment' => $payment,
            'paymentrp' => format_money($payment),
            'in_words' => ucwords(toWords($payment). ' Peso')
        ];

        return response()->json($data);
    }
}
