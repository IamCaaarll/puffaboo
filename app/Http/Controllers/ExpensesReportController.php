<?php

namespace App\Http\Controllers;
use App\Models\Expenses;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class ExpensesReportController extends Controller
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

        return view('expenses_report.index', compact('startDate', 'endDate','branch','branch_id'));
    }

    public function getData($branch_id,$start, $end)
    {
            
        $no = 1;
        $data = array();
        $total_expenses = 0;
        
        $expenses = Expenses::whereBetween(DB::raw('DATE(created_at)'), [$start, $end])
        ->whereHas('branch', function ($query) use ($branch_id) {
            $query->where('branch_id', $branch_id);
        })
        ->with('branch')
        ->get(['*', 'created_at']);

        foreach($expenses as $detail)
        {
            $row = array();
            $row['DT_RowIndex'] = $no++;
            $row['date'] = us_date($detail->created_at, false);
            $row['description'] = $detail->description ?? '';
            $row['amount'] = format_money($detail->amount);

           $total_expenses += $detail->amount;
            $data[] = $row;
        }
        $data[] = [ 
            'DT_RowIndex' => '',
            'date' => '',
            'description' => 'Total Amount',
            'amount' => format_money($total_expenses)

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

        $pdf  = PDF::loadView('expenses_report.pdf', compact('start', 'end','branch_name','data'));
        $pdf->setPaper('a4', 'potrait');
        
        return $pdf->stream('Expenses-Report-'. date('Y-m-d-his') .'.pdf');
    }

    
}
