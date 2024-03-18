<?php

namespace App\Http\Controllers;

use App\Models\Purchases;
use App\Models\Expenses;
use App\Models\Branch;
use App\Models\Sales;
use Illuminate\Http\Request;

use PDF;

class ReportController extends Controller
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

        return view('report.index', compact('startDate', 'endDate','branch','branch_id'));
    }

    public function getData($branch_id,$start, $end)
    {            
        $no = 1;
        $data = array();
        $net_income = 0;
        $gross_income = 0;
        $total_net_income = 0;
        $total_gross_income = 0;

        while (strtotime($start) <= strtotime($end)) {
            $date = $start;
            $start = date('Y-m-d', strtotime("+1 day", strtotime($start)));

            $total_sales = Sales::where('created_at', 'LIKE', "%$date%")
            ->with(['salesDetails.product'])
            ->whereHas('salesDetails.product', function ($query) use ($branch_id) {
                // Filter sales by product's branch_id
                $query->where('branch_id', $branch_id);
            })
            ->get()
            ->sum(function ($sale) {
                // Calculate the sum of subtotal for each sale
                return $sale->salesDetails->sum('subtotal');
            });

            $total_capital = Sales::where('created_at', 'LIKE', "%$date%")
            ->with(['salesDetails.product'])
            ->whereHas('salesDetails.product', function ($query) use ($branch_id) {
                // Filter sales by product's branch_id
                $query->where('branch_id', $branch_id);
            })
            ->get()
            ->sum(function ($sale) {
                // Calculate the sum of purchase_price * quantity for each product in sale details
                return $sale->salesDetails->sum(function ($salesDetail) {
                    // Accessing the purchase_price and quantity of each product
                    return $salesDetail->product->purchase_price * $salesDetail->quantity;
                });
            });

            $total_purchases = Purchases::where('created_at', 'LIKE', "%$date%")
            ->with(['purchasesDetails.product'])
            ->whereHas('purchasesDetails.product', function ($query) use ($branch_id) {
                // Filter sales by product's branch_id
                $query->where('branch_id', $branch_id);
            })
            ->get()
            ->sum(function ($purchases) {
                // Calculate the sum of subtotal for each sale
                return $purchases->purchasesDetails->sum('subtotal');
            });
            $total_expenses = Expenses::where('created_at', 'LIKE', "%$date%")
                ->whereHas('branch', function ($query) use ($branch_id) {
                    // Filter expenses by branch_id
                    $query->where('branch_id', $branch_id);
                })
                ->sum('amount');
            $gross_income =  $total_sales - $total_capital;
            $net_income =  $total_sales -$total_capital- $total_purchases - $total_expenses;
            $total_net_income += $net_income;
            $total_gross_income += $gross_income;

            $row = array();
            $row['DT_RowIndex'] = $no++;
            $row['date'] = us_date($date, false);
            $row['capital'] = format_money($total_capital);
            $row['sales'] = format_money($total_sales);
            $row['purchases'] = format_money($total_purchases);
            $row['expenses'] = format_money($total_expenses);
            $row['gross_income'] = format_money($gross_income);
            $row['net_income'] = format_money($net_income);

            $data[] = $row;
        }
        
        $data[] = [
            'DT_RowIndex' => '',
            'date' => '',
            'capital' => '',
            'sales' => '',
            'purchases' => '',
            'expenses' => 'Total Gross & Net Income',
            'gross_income' => format_money($gross_income),
            'net_income' => format_money($total_net_income),
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
        $pdf  = PDF::loadView('report.pdf', compact('start', 'end','branch_name','data'));
        $pdf->setPaper('a4', 'potrait');
        
        return $pdf->stream('Daily-Report-income-'. date('Y-m-d-his') .'.pdf');
    }

    
}
