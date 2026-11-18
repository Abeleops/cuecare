
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
            @include('partials.navigation')
            <div id="dashboardSidebarFooter">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dashboardNavLink">
                        <span class="material-symbols-outlined" aria-hidden="true">logout</span>
                        <span>Logout</span>
                    </button>
                </form>
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
                @include('partials.profile')
            </header>
            <!-- Sales Content -->
            <div id="salesContent">
                <!-- Date Range, Search and Export -->
                <form id="salesToolbar" method="GET" action="{{ route('sales.index') }}">
                    <div id="salesDateFilter">
                        <label for="salesStartDate">Date Range</label>
                        <div id="salesDateRangeContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                            <input type="date" id="salesStartDate" name="start_date" value="{{ $startDate }}" aria-label="Start date">
                            <input type="date" id="salesEndDate" name="end_date" value="{{ $endDate }}" aria-label="End date">
                        </div>
                    </div>
                    <div id="salesSearchContainer">
                        <span class="material-symbols-outlined" aria-hidden="true">search</span>
                        <input type="search" id="salesSearchInput" name="search" maxlength="100"
                            value="{{ request('search') }}" placeholder="Search by transaction no. or customer name..."
                            aria-label="Search sales transactions">
                    </div>
                    <button type="submit">Apply filters</button>
                </form>
                @if ($errors->any()) <p role="alert">{{ $errors->first() }}</p> @endif
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
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($sales->isNotEmpty())
                                    @foreach($sales as $sale)
                                        <tr>
                                            <td>{{ $sale->transaction_code }}</td>
                                            <td>{{ optional($sale->completed_at)->format('M d, Y h:i A') }}</td>
                                            <td>{{ $sale->customer?->full_name ?: '—' }}</td>
                                            <td>₱{{ number_format((float) $sale->total_amount, 2) }}</td>
                                            <td>{{ strtoupper($sale->payment_method) }}</td>
                                            <td>{{ $sale->soldBy?->full_name }}</td>
                                            <td>{{ ucfirst($sale->status) }}</td>
                                            <td>
                                                <a href="{{ route('sales.show', $sale) }}">View</a>
                                                <a href="{{ route('sales.receipt', $sale) }}">Receipt</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="8">No transactions found.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div id="salesPagination">
                        <p id="salesPaginationInfo">Showing {{ $sales->firstItem() ?? 0 }} to {{ $sales->lastItem() ?? 0 }} of {{ $sales->total() }} transactions</p>
                        <nav id="salesPaginationControls" aria-label="Sales history pagination">
                            @if ($sales->previousPageUrl()) <a href="{{ $sales->previousPageUrl() }}">Previous</a> @endif
                            @if ($sales->nextPageUrl()) <a href="{{ $sales->nextPageUrl() }}">Next</a> @endif
                        </nav>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
