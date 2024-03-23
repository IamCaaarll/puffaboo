<?php

use App\Http\Controllers\{
    DashboardController,
    BranchController,
    ReportController,
    ReportProductController,
    ExpensesReportController,
    StockReportController,
    ProductController,
    MemberController,
    ExpensesController,
    PurchasesController,
    PurchasesDetailController,
    SalesController,
    SalesDetailController,
    SettingController,
    SupplierController,
    UserController,
};
use App\Models\Expenses;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(['middleware' => 'level:1'], function () {
        Route::get('/branch/data', [BranchController::class, 'data'])->name('branch.data');
        Route::resource('/branch', BranchController::class);

        Route::get('/product/data', [ProductController::class, 'data'])->name('product.data');
        Route::post('/product/delete-selected', [ProductController::class, 'deleteSelected'])->name('product.delete_selected');
        Route::post('/product/print-barcode', [ProductController::class, 'printBarcode'])->name('product.print_barcode');
        Route::resource('/product', ProductController::class);

        Route::get('/member/data', [MemberController::class, 'data'])->name('member.data');
        Route::post('/member/print-member', [MemberController::class, 'printMember'])->name('member.print_member');
        Route::resource('/member', MemberController::class);

        Route::get('/supplier/data', [SupplierController::class, 'data'])->name('supplier.data');
        Route::resource('/supplier', SupplierController::class);

        Route::get('/expenses/data', [ExpensesController::class, 'data'])->name('expenses.data');
        Route::resource('/expenses', ExpensesController::class);

        Route::get('/purchases/data', [PurchasesController::class, 'data'])->name('purchases.data');
        Route::get('/purchases/{id}/create', [PurchasesController::class, 'create'])->name('purchases.create');
        Route::resource('/purchases', PurchasesController::class)
            ->except('create');

        Route::get('/purchases_detail/{id}/data', [PurchasesDetailController::class, 'data'])->name('purchases_detail.data');
        Route::get('/purchases_detail/loadform/{discount}/{total}', [PurchasesDetailController::class, 'loadForm'])->name('purchases_detail.load_form');
        Route::resource('/purchases_detail', PurchasesDetailController::class)
            ->except('create', 'show', 'edit');

        Route::get('/sales/data', [SalesController::class, 'data'])->name('sales.data');
        Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
        Route::get('/sales/{id}', [SalesController::class, 'show'])->name('sales.show');
        Route::delete('/sales/{id}', [SalesController::class, 'destroy'])->name('sales.destroy');
    });

    Route::group(['middleware' => 'level:1,2'], function () {
        Route::get('/transaction/new', [SalesController::class, 'create'])->name('transaction.new');
        Route::post('/transaction/save', [SalesController::class, 'store'])->name('transaction.save');
        Route::get('/transaction/completed', [SalesController::class, 'completed'])->name('transaction.completed');
        Route::get('/transaction/small-note', [SalesController::class, 'smallNote'])->name('transaction.small_note');
        Route::get('/transaction/large-note', [SalesController::class, 'largeNote'])->name('transaction.large_note');

        Route::get('/transaction/{id}/data', [SalesDetailController::class, 'data'])->name('transaction.data');
        Route::get('/transaction/loadform/{discount}/{total}/{received}', [SalesDetailController::class, 'loadForm'])->name('transaction.load_form');
        Route::resource('/transaction', SalesDetailController::class)
            ->except('create', 'show', 'edit');
    });

    Route::group(['middleware' => 'level:1'], function () {
        Route::get('/report', [ReportController::class, 'index'])->name('report.index');
        Route::get('/report/data/{branch_id}/{start}/{end}', [ReportController::class, 'data'])->name('report.data');
        Route::get('/report/pdf/{branch_id}/{start}/{end}', [ReportController::class, 'exportPDF'])->name('report.export_pdf');
        
        Route::get('/expenses_report', [ExpensesReportController::class, 'index'])->name('expenses_report.index');
        Route::get('/expenses_report/data/{branch_id}/{start}/{end}', [ExpensesReportController::class, 'data'])->name('expenses_report.data');
        Route::get('/expenses_report/pdf/{branch_id}/{start}/{end}', [ExpensesReportController::class, 'exportPDF'])->name('expenses_report.export_pdf');

        Route::get('/report_product', [ReportProductController::class, 'index'])->name('report_product.index');
        Route::get('/report_product/data/{branch_id}/{start}/{end}', [ReportProductController::class, 'data'])->name('report_product.data');
        Route::get('/report_product/pdf/{branch_id}/{start}/{end}', [ReportProductController::class, 'exportPDF'])->name('report_product.export_pdf');

        Route::get('/stock_report', [StockReportController::class, 'index'])->name('stock_report.index');
        Route::get('/stock_report/data/{branch_id}', [StockReportController::class, 'data'])->name('stock_report.data');
        Route::get('/stock_report/pdf/{branch_id}', [StockReportController::class, 'exportPDF'])->name('stock_report.export_pdf');

        Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
        Route::resource('/user', UserController::class);

        Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
        Route::get('/setting/first', [SettingController::class, 'show'])->name('setting.show');
        Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');
    });
 
    Route::group(['middleware' => 'level:1,2'], function () {
        Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
        Route::post('/profile', [UserController::class, 'updateProfile'])->name('user.update_profile');
    });
});