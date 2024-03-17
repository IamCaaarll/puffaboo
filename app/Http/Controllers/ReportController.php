<?php

namespace App\Http\Controllers;

use App\Models\Purchases;
use App\Models\Expenses;
use App\Models\Sales;
use Illuminate\Http\Request;
use PDF;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate  = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $endDate  = date('Y-m-d');

        if ($request->has('start_date') && $request->start_date != "" && $request->has('end_date') && $request->end_date) {
            $startDate  = $request->start_date;
            $endDate  = $request->end_date;
        }

        return view('report.index', compact('startDate', 'endDate'));
    }

    public function getData($start, $end)
    {
        $no = 1;
        $data = array();
        $income = 0;
        $total_income = 0;

        while (strtotime($start) <= strtotime($end)) {
            $date = $start;
            $start = date('Y-m-d', strtotime("+1 day", strtotime($start)));

            $total_sales = Sales::where('created_at', 'LIKE', "%$date%")->sum('payment');
            $total_purchases = Purchases::where('created_at', 'LIKE', "%$date%")->sum('payment');
            $total_expenses = Expenses::where('created_at', 'LIKE', "%$date%")->sum('amount');

            $income = $total_sales - $total_purchases - $total_expenses;
            $total_income += $income;

            $row = array();
            $row['DT_RowIndex'] = $no++;
            $row['date'] = us_date($date, false);
            $row['sales'] = format_money($total_sales);
            $row['purchases'] = format_money($total_purchases);
            $row['expenses'] = format_money($total_expenses);
            $row['income'] = format_money($income);

            $data[] = $row;
        }
        
        $data[] = [
            'DT_RowIndex' => '',
            'date' => '',
            'sales' => '',
            'purchases' => '',
            'expenses' => 'Total Income',
            'income' => format_money($total_income),
        ];

        return $data;
    }

    public function data($start, $end)
    {
        $data = $this->getData($start, $end);

        return datatables()
            ->of($data)
            ->make(true);
    }

    public function exportPDF($start, $end)
    {
        $data = $this->getData($start, $end);
        $pdf  = PDF::loadView('report.pdf', compact('start', 'end', 'data'));
        $pdf->setPaper('a4', 'potrait');
        
        return $pdf->stream('Report-income-'. date('Y-m-d-his') .'.pdf');
    }
}
