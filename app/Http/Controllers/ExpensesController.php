<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expenses;

class ExpensesController extends Controller
{
    public function index()
    {
        return view('expenses.index');
    }

    public function data()
    {
        $expenses = Expenses::orderBy('expense_id', 'desc')->get();

        return datatables()
            ->of($expenses)
            ->addIndexColumn()
            ->addColumn('created_at', function ($expenses) {
                return us_date($expenses->created_at, false);
            })
            ->addColumn('amount', function ($expenses) {
                return format_money($expenses->amount);
            })
            ->addColumn('action', function ($expenses) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`'. route('expenses.update', $expenses->expense_id) .'`)" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-pencil"></i></button>
                    <button type="button" onclick="deleteData(`'. route('expenses.destroy', $expenses->expense_id) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $expenses = Expenses::create($request->all());

        return response()->json('Data saved successfully', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $expenses = Expenses::find($id);

        return response()->json($expenses);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $expenses = Expenses::find($id)->update($request->all());

        return response()->json('Data saved successfully', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $expenses = Expenses::find($id)->delete();

        return response(null, 204);
    }
}
