<?php

namespace App\Http\Controllers;

use App\Models\Purchases;
use App\Models\Expenses;
use App\Models\Branch;
use App\Models\Sales;
use App\Models\SalesDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;

class ReportProductController extends Controller
{
    public function index(Request $request)
    {
        $branch = Branch::where('active','1')->pluck('branch_name', 'branch_id');
        $branch_id = Branch::where('active', '1')->first()->branch_id;
        $startDate  = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $endDate  = date('Y-m-d');
        if ($request->has('start_date') && $request->start_date != ""
        && $request->has('end_date') && $request->end_date != "" 
        && $request->has('branch_id') && $request->branch_id) {
        $branch_id = $request->branch_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
    }

        return view('report_product.index', compact('startDate', 'endDate','branch','branch_id'));
    }

    public function getData($branch_id,$start, $end)
    {
            
        $no = 1;
        $data = array();
        $total_purchase_price = 0;
        $total_income = 0;
        $total_sale = 0;
        $sales = SalesDetail::whereBetween(DB::raw('DATE(created_at)'), [$start, $end])
        ->whereHas('product.branch', function ($query) use ($branch_id) {
            $query->where('branch_id', $branch_id);
        })
        ->with('product.branch')
        ->get(['*', 'created_at']);
        foreach($sales as $detail)
        {
            $row = array();
            $row['DT_RowIndex'] = $no++;
            $row['date'] = us_date($detail->created_at, false);
            $row['branch'] = $detail->product->branch->branch_name ?? '';
            $row['product_name'] = $detail->product->product_name.' ('.$detail->product->brand.')';
            $row['quantity'] = $detail->quantity;
            $row['subtotal'] = format_money($detail->subtotal);
            $row['selling_price'] = format_money($detail->product->selling_price);
            $row['purchase_price'] = format_money($detail->quantity * $detail->product->purchase_price);
            $row['income'] = format_money($detail->subtotal - ($detail->quantity * $detail->product->purchase_price));

            $total_sale += $detail->subtotal;
            $total_income += $detail->subtotal - ($detail->quantity * $detail->product->purchase_price);
            $total_purchase_price += $detail->quantity * $detail->product->purchase_price;
            $data[] = $row;
        }
        $data[] = [ 
            'DT_RowIndex' => '',
            'date' => '',
            'branch' => '',
            'product_name' => '',
            'quantity' => 'Total Sales',
            'subtotal' => format_money($total_sale),
            'selling_price' => 'Total Capital & Profit',
            'purchase_price' => format_money($total_purchase_price),
            'income' => format_money($total_income),

        ];
        return $data;
    }

    
    public function data($branch_id,$start, $end)
    {
        
        $data = $this->getData($branch_id,$start, $end);

        return datatables()
            ->of($data)
            ->make(true);
    }

    public function exportPDF($branch_id,$start, $end)
    {
        $branch_name = Branch::where('branch_id',$branch_id)->first()->branch_name;
        $data = $this->getData($branch_id,$start, $end);

        $pdf  = PDF::loadView('report_product.pdf', compact('start', 'end','branch_name','data'));
        $pdf->setPaper('a4', 'potrait');
        
        return $pdf->stream('Product-Income-Report-'. date('Y-m-d-his') .'.pdf');
    }

    
}
