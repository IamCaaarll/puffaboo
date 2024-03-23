<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\Request;
use PDF;

class StockReportController extends Controller
{
    public function index(Request $request)
    {
        $branch = Branch::where('active','1')->pluck('branch_name', 'branch_id');
        $branch_id = Branch::where('active', '1')->first()->branch_id;
        if ($request->has('branch_id') && $request->branch_id) {
        $branch_id = $request->branch_id;
    }

        return view('stock_report.index', compact('branch','branch_id'));
    }

    public function getData($branch_id)
    {
            
        $no = 1;
        $data = array();
        $total_purchases = 0;
        $total_stock = 0;
        $product = Product::whereHas('branch', function ($query) use ($branch_id) {
            $query->where('branch_id', $branch_id);
        })
        ->get();

        foreach ($product as $detail) {
            $row = array();
            $row['DT_RowIndex'] = $no++;
            $row['product_name'] = $detail->product_name . ' (' . $detail->brand . ')';
            $row['purchase_price'] = format_money($detail->purchase_price);
            $row['selling_price'] = format_money($detail->selling_price);
            $row['discount'] = format_money($detail->discount);
            $row['stock'] = $detail->stock;
        
            $total_purchases += $detail->purchase_price;
            $total_stock += $detail->stock;
            $data[] = $row;
        }
        $data[] = [ 
            'DT_RowIndex' => '',
            'product_name' => 'Total Purchase',
            'purchase_price' => format_money($total_purchases),
            'selling_price' => '',
            'discount' => 'Total Stock',
            'stock' => format_money($total_stock),

        ];
        return $data;
    }

    
    public function data($branch_id)
    {
        
        $data = $this->getData($branch_id);

        return datatables()
            ->of($data)
            ->make(true);
    }

    public function exportPDF($branch_id)
    {
        $branch_name = Branch::where('branch_id',$branch_id)->first()->branch_name;
        $data = $this->getData($branch_id);

        $pdf  = PDF::loadView('stock_report.pdf', compact('branch_name','data'));
        $pdf->setPaper('a4', 'potrait');
        
        return $pdf->stream('Product-Income-Report-'. date('Y-m-d-his') .'.pdf');
    }

    
}
