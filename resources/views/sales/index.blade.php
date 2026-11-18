
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales History | GG Billiards & Autocare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body>
    <div id="salesPage">
        <!-- Sidebar -->
        <aside id="dashboardSidebar">
            <div id="dashboardSidebarBranding">
                <img src="{{ asset('images/logo.png') }}" alt="GG Billiards & Autocare Logo" id="dashboardSidebarLogo">
                <p id="dashboardSidebarBusinessName">GG BILLIARDS & AUTOCARE</p>
            </div>
            <nav id="dashboardSidebarNavigation" aria-label="Main navigation">
                @can('DashboardView')<a href="{{ route('dashboard') }}" class="dashboardNavLink">
                    <span class="material-symbols-outlined" aria-hidden="true">dashboard</span>
                    <span>Dashboard</span>
                </a>@endcan
                @can('PosAccess')<a href="{{ route('pos.index') }}" class="dashboardNavLink">
                    <span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span>
                    <span>Record Sale</span>
                </a>@endcan
                @can('ProductsView')<a href="{{ route('products.index') }}" class="dashboardNavLink">
                    <span class="material-symbols-outlined" aria-hidden="true">deployed_code</span>
                    <span>Products</span>
                </a>@endcan
                @can('CustomersView')<a href="{{ route('customers.index') }}" class="dashboardNavLink">
                    <span class="material-symbols-outlined" aria-hidden="true">groups</span>
                    <span>Customers</span>
                </a>@endcan
                @can('StaffView')<a href="{{ route('staff.index') }}" class="dashboardNavLink">
                    <span class="material-symbols-outlined" aria-hidden="true">account_box</span>
                    <span>Staff</span>
                </a>@endcan
                @canany(['SalesViewAll', 'SalesViewOwn'])<a href="{{ route('sales.index') }}" class="dashboardNavLink active" aria-current="page">
                    <span class="material-symbols-outlined" aria-hidden="true">receipt_long</span>
                    <span>Sales History</span>
                </a>@endcanany
                @can('ReportsView')<a href="{{ route('reports.index') }}" class="dashboardNavLink">
                    <span class="material-symbols-outlined" aria-hidden="true">monitoring</span>
                    <span>Reports</span>
                </a>@endcan
            </nav>
            <div id="dashboardSidebarFooter">
                <a href="{{ route('login') }}" class="dashboardNavLink">
                    <span class="material-symbols-outlined" aria-hidden="true">logout</span>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        <!-- Main Content -->
        <main id="dashboardMain">
            <!-- Header -->
            <header id="dashboardHeader">
                <div id="dashboardHeaderTitle">
                    <h1>Sales History</h1>
                    <p>View and manage all recorded sales transactions</p>
                </div>
                <div id="dashboardHeaderProfile">
                    <span class="material-symbols-outlined" id="dashboardProfileIcon" aria-hidden="true">account_circle</span>
                    <div id="dashboardProfileDetails">
                        <h2>Ivan Mark Lizardo</h2>
                        <p>Admin</p>
                    </div>
                </div>
            </header>
            <!-- Sales Content -->
            <div id="salesContent">
                <!-- Date Range, Search and Export -->
                <section id="salesToolbar">
                    <div id="salesDateFilter">
                        <label for="salesDateRange">Date Range</label>
                        <div id="salesDateRangeContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                            <select id="salesDateRange">
                                <option value="jan2025">Jan 01, 2025 – Jan 31, 2025</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                            </select>
                        </div>
                    </div>
                    <div id="salesSearchContainer">
                        <span class="material-symbols-outlined" aria-hidden="true">search</span>
                        <input type="search" id="salesSearchInput" placeholder="Search by transaction no. or customer name..." aria-label="Search sales transactions">
                    </div>
                    <button type="button" id="salesExportButton">
                        <span class="material-symbols-outlined" aria-hidden="true">download</span>
                        Export
                    </button>
                </section>
                <!-- Sales History Table -->
                <section id="salesTableSection" aria-label="Sales transaction history">
                    <div id="salesTableContainer">
                        <table id="salesTable">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <span class="salesTableHeading">
                                            Transaction No.
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">
                                        <span class="salesTableHeading">
                                            Date &amp; Time
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">
                                        <span class="salesTableHeading">
                                            Customer
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">
                                        <span class="salesTableHeading">
                                            Total Amount
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">
                                        <span class="salesTableHeading">
                                            Payment Method
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">
                                        <span class="salesTableHeading">
                                            Cashier
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($sales))
                                    @foreach($sales as $sale)
                                        <tr><td>{{ $sale->transaction_code }}</td><td>{{ optional($sale->completed_at)->format('M d, Y h:i A') }}</td><td>{{ $sale->customer?->full_name ?: '—' }}</td><td>₱{{ number_format((float) $sale->total_amount, 2) }}</td><td>{{ strtoupper($sale->payment_method) }}</td><td>{{ $sale->soldBy?->full_name }}</td><td><a href="{{ route('sales.show', $sale) }}">View</a> <a href="{{ route('sales.receipt', $sale) }}">Receipt</a></td></tr>
                                    @endforeach
                                @else
                                <!-- Static Sample Transaction 1 -->
                                <tr>
                                    <td>TRX-0001</td>
                                    <td>Jan 31, 2025 02:15 PM</td>
                                    <td>Maria Santos</td>
                                    <td>₱450.00</td>
                                    <td>Cash</td>
                                    <td>Juan Dela Cruz</td>
                                    <td>
                                        <div class="salesActionButtons">
                                            <button type="button" class="salesViewButton" aria-label="View transaction TRX-0001">
                                                <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
                                            </button>
                                            <button type="button" class="salesPrintButton" aria-label="Print transaction TRX-0001">
                                                <span class="material-symbols-outlined" aria-hidden="true">print</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Transaction 2 -->
                                <tr>
                                    <td>TRX-0002</td>
                                    <td>Jan 31, 2025 01:48 PM</td>
                                    <td>Carlo Reyes</td>
                                    <td>₱220.00</td>
                                    <td>GCash</td>
                                    <td>Juan Dela Cruz</td>
                                    <td>
                                        <div class="salesActionButtons">
                                            <button type="button" class="salesViewButton" aria-label="View transaction TRX-0002">
                                                <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
                                            </button>
                                            <button type="button" class="salesPrintButton" aria-label="Print transaction TRX-0002">
                                                <span class="material-symbols-outlined" aria-hidden="true">print</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Transaction 3 -->
                                <tr>
                                    <td>TRX-0003</td>
                                    <td>Jan 30, 2025 05:20 PM</td>
                                    <td>—</td>
                                    <td>₱180.00</td>
                                    <td>Cash</td>
                                    <td>Maria Santos</td>
                                    <td>
                                        <div class="salesActionButtons">
                                            <button type="button" class="salesViewButton" aria-label="View transaction TRX-0003">
                                                <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
                                            </button>
                                            <button type="button" class="salesPrintButton" aria-label="Print transaction TRX-0003">
                                                <span class="material-symbols-outlined" aria-hidden="true">print</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Transaction 4 -->
                                <tr>
                                    <td>TRX-0004</td>
                                    <td>Jan 30, 2025 03:05 PM</td>
                                    <td>Angelica Cruz</td>
                                    <td>₱600.00</td>
                                    <td>Juan Dela Cruz</td>
                                    <td>
                                        <div class="salesActionButtons">
                                            <button type="button" class="salesViewButton" aria-label="View transaction TRX-0004">
                                                <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
                                            </button>
                                            <button type="button" class="salesPrintButton" aria-label="Print transaction TRX-0004">
                                                <span class="material-symbols-outlined" aria-hidden="true">print</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Transaction 5 -->
                                <tr>
                                    <td>TRX-0005</td>
                                    <td>Jan 29, 2025 07:10 PM</td>
                                    <td>Mark Villanueva</td>
                                    <td>₱330.00</td>
                                    <td>Cash</td>
                                    <td>Maria Santos</td>
                                    <td>
                                        <div class="salesActionButtons">
                                            <button type="button" class="salesViewButton" aria-label="View transaction TRX-0005">
                                                <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
                                            </button>
                                            <button type="button" class="salesPrintButton" aria-label="Print transaction TRX-0005">
                                                <span class="material-symbols-outlined" aria-hidden="true">print</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div id="salesPagination">
                        <p id="salesPaginationInfo">Showing 1 to 5 of 24 transactions</p>
                        <nav id="salesPaginationControls" aria-label="Sales history pagination">
                            <button type="button" class="salesPaginationButton" aria-label="Previous page" disabled>
                                <span class="material-symbols-outlined" aria-hidden="true">chevron_left</span>
                            </button>
                            <button type="button" class="salesPaginationButton active" aria-current="page">1</button>
                            <button type="button" class="salesPaginationButton">2</button>
                            <button type="button" class="salesPaginationButton">3</button>
                            <button type="button" class="salesPaginationButton">4</button>
                            <button type="button" class="salesPaginationButton">5</button>
                            <button type="button" class="salesPaginationButton" aria-label="Next page">
                                <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                            </button>
                        </nav>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
