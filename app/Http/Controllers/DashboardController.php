<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Member;
use App\Models\Purchases;
use App\Models\Expenses;
use App\Models\Sales;
use App\Models\Product;
use App\Models\Supplier;

class DashboardController extends Controller
{
    public function index()
    {
        $branch = Branch::count();
        $product = Product::count();
        $supplier = Supplier::count();
        $member = Member::count();
        $sales = Sales::sum('received');
        $expenses = Expenses::sum('amount');
        $purchases = Purchases::sum('payment');

        $start_date = date('Y-m-01');
        $end_date = date('Y-m-d');

        $date_data = array();
        $income_data = array();

        while (strtotime($start_date) <= strtotime($end_date)) {
            $date_data[] = (int) substr($start_date, 8, 2);

            $total_sales = Sales::where('created_at', 'LIKE', "%$start_date%")->sum('payment');
            $total_purchases = Purchases::where('created_at', 'LIKE', "%$start_date%")->sum('payment');
            $total_expenses = Expenses::where('created_at', 'LIKE', "%$start_date%")->sum('amount');

            $pendapatan = $total_sales - $total_purchases - $total_expenses;
            $income_data[] += $pendapatan;

            $start_date = date('Y-m-d', strtotime("+1 day", strtotime($start_date)));
        }

        $start_date = date('Y-m-01');

        if (auth()->user()->level == 1) {
            return view('admin.dashboard', compact('branch', 'product', 'supplier', 'member', 'sales', 'expenses', 'purchases', 'start_date', 'end_date', 'date_data', 'income_data'));
        } else {
            return view('cashier.dashboard');
        }
    }
}
