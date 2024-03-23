<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ url(auth()->user()->picture ?? '') }}" class="img-circle img-profil" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ auth()->user()->name }}</p>
                @if(auth()->user()->level == 1)
                <a href="#"><i class="fa fa-circle text-success"></i> {{ auth()->user()->email }}</a>
                @else
                <a href="#"><i class="fa fa-circle text-success"></i> {{ auth()->user()->branch->branch_name }}</a>
                @endif
            
            </div>
        </div>
        
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li>
                <a href="{{ route('dashboard') }}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>

            @if (auth()->user()->level == 1)
            <li class="header">MASTER</li>
            <li>
                <a href="{{ route('branch.index') }}">
                    <i class="fa fa-cube"></i> <span>Branch</span>
                </a>
            </li>
            <li>
                <a href="{{ route('product.index') }}">
                    <i class="fa fa-cubes"></i> <span>Product</span>
                </a>
            </li>
            <li>
                <a href="{{ route('member.index') }}">
                    <i class="fa fa-id-card"></i> <span>Member</span>
                </a>
            </li>
            <li>
                <a href="{{ route('supplier.index') }}">
                    <i class="fa fa-truck"></i> <span>Supplier</span>
                </a>
            </li>
            <li class="header">TRANSACTION</li>
            <li>
                <a href="{{ route('expenses.index') }}">
                    <i class="fa fa-money"></i> <span>Expenses</span>
                </a>
            </li>
            <li>
                <a href="{{ route('purchases.index') }}">
                    <i class="fa fa-download"></i> <span>Purchase</span>
                </a>
            </li>
            <li>
                <a href="{{ route('sales.index') }}">
                    <i class="fa fa-dollar"></i> <span>Sales List</span>
                </a>
            </li>
            <li>
                <a href="{{ route('transaction.new') }}">
                    <i class="fa fa-cart-plus"></i> <span>New Transaction</span>
                </a>
            </li>
            <li>
                <a href="{{ route('transaction.index') }}">
                    <i class="fa fa-cart-arrow-down"></i> <span>Active Transaction</span>
                </a>
            </li>
            
            <li class="header">REPORT</li>
            <li>
                <a href="{{ route('expenses_report.index') }}">
                    <i class="fa fa-file-pdf-o"></i> <span>Expenses Report</span>
                </a>
            </li>
            <li>
                <a href="{{ route('stock_report.index') }}">
                    <i class="fa fa-file-pdf-o"></i> <span>Inventory Report</span>
                </a>
            </li>
            <li>
                <a href="{{ route('report.index') }}">
                    <i class="fa fa-file-pdf-o"></i> <span>Daily Income</span>
                </a>
            </li>
            <li>
                <a href="{{ route('report_product.index') }}">
                    <i class="fa fa-file-pdf-o"></i> <span>Product Income</span>
                </a>
            </li>
            <li class="header">SYSTEM</li>
            <li>
                <a href="{{ route('user.index') }}">
                    <i class="fa fa-users"></i> <span>User</span>
                </a>
            </li>
            <li>
                <a href="{{ route("setting.index") }}">
                    <i class="fa fa-cogs"></i> <span>Settings</span>
                </a>
            </li>
            @else
            <li>
                <a href="{{ route('transaction.new') }}">
                    <i class="fa fa-cart-plus"></i> <span>New Transaction</span>
                </a>
            </li>
            <li>
                <a href="{{ route('transaction.index') }}">
                    <i class="fa fa-cart-arrow-down"></i> <span>Active Transaction</span>
                </a>
            </li>
            @endif
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>