
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | GG Billiards & Autocare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body>
    <div id="reportsPage">
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
                    <h1>Reports</h1>
                    <p>Generate sales and inventory reports</p>
                </div>
                @include('partials.profile')
            </header>
            <!-- Reports Content -->
            <div id="reportsContent">
                <!-- Report Tabs -->    
                <nav id="reportsTabs" aria-label="Report types">
                    <a href="{{ route('reports.index') }}" class="reportsTabButton active" aria-current="page">
                        <span class="material-symbols-outlined" aria-hidden="true">bar_chart</span>
                        Sales Report
                    </a>
                    <a href="{{ route('reports.inventory') }}" class="reportsTabButton">
                        <span class="material-symbols-outlined" aria-hidden="true">inventory_2</span>
                        Inventory Report
                    </a>
                </nav>
                <!-- Sales Report -->
                <section id="reportsSalesSection" aria-label="Sales report">
                    <!-- Filters and Actions -->
                    <form id="reportsFilters" method="GET" action="{{ route('reports.index') }}">
                        <div id="reportsDateFilter">
                            <label for="reportsStartDate">Date Range</label>
                            <div id="reportsDateRangeContainer">
                                <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                                <input type="date" id="reportsStartDate" name="start_date" value="{{ $startDate }}" aria-label="Start date">
                                <input type="date" name="end_date" value="{{ $endDate }}" aria-label="End date">
                            </div>
                        </div>
                        <div id="reportsPaymentFilter">
                            <label for="reportsPaymentMethod">Payment Method</label>
                            <select id="reportsPaymentMethod" name="payment">
                                <option value="">All Methods</option>
                                <option value="cash" @selected($payment === 'cash')>Cash</option>
                                <option value="gcash" @selected($payment === 'gcash')>GCash</option>
                            </select>
                        </div>
                        <button type="submit" id="reportsGenerateButton">
                            <span class="material-symbols-outlined" aria-hidden="true">refresh</span>
                            Generate Report
                        </button>
                        @can('ReportsExport')
                            <button type="submit" id="reportsExportButton" formaction="{{ route('reports.export') }}" name="type" value="sales">Export Excel</button>
                        @endcan
                    </form>
                    @if ($errors->any()) <p role="alert">{{ $errors->first() }}</p> @endif
                    <!-- Sales Statistics -->
                    <section id="reportsSalesStatistics" aria-label="Sales statistics">
                        <article class="reportsStatCard">
                            <span class="material-symbols-outlined reportsStatIcon" aria-hidden="true">shopping_cart</span>
                            <div class="reportsStatInformation">
                                <p>Total Sales</p>
                                <h2>₱{{ number_format($salesTotal, 2) }}</h2>
                            </div>
                        </article>
                        <article class="reportsStatCard">
                            <span class="material-symbols-outlined reportsStatIcon" aria-hidden="true">receipt_long</span>
                            <div class="reportsStatInformation">
                                <p>Total Transactions</p>
                                <h2>{{ $transactionCount }}</h2>
                            </div>
                        </article>
                        <article class="reportsStatCard">
                            <span class="material-symbols-outlined reportsStatIcon" aria-hidden="true">shopping_basket</span>
                            <div class="reportsStatInformation">
                                <p>Average Sale</p>
                                <h2>₱{{ number_format($averageSale, 2) }}</h2>
                            </div>
                        </article>
                        <article class="reportsStatCard reportsGrowthCard">
                            <span class="material-symbols-outlined" aria-hidden="true">trending_up</span>
                            <div class="reportsStatInformation">
                                <h2>{{ sprintf('%+.1f%%', $growth) }}</h2>
                                <p>vs. previous period</p>
                            </div>
                        </article>
                    </section>
                    <!-- Sales Trend and Top Selling Products -->
                    <section id="reportsSalesOverview" aria-label="Sales overview">
                        <article id="reportsSalesTrend" class="reportsPanel">
                            <div class="reportsPanelHeader">
                                <h2>Sales Trend</h2>
                            </div>
                            <div id="reportsSalesTrendChart">
                                <table>
                                    <thead><tr><th>Date</th><th>Sales</th></tr></thead>
                                    <tbody>
                                        @forelse ($dailyTotals as $day)
                                            <tr>
                                                <td>
                                                    {{ $day->sale_date }}
                                                    <progress aria-label="Sales on {{ $day->sale_date }}"
                                                        value="{{ $day->total }}" max="{{ max(1, $dailyTotals->max('total')) }}"></progress>
                                                </td>
                                                <td>₱{{ number_format((float) $day->total, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="2">No sales for this period.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </article>
                        <article id="reportsTopSellingProducts" class="reportsPanel">
                            <div class="reportsPanelHeader">
                                <h2>Top Selling Products</h2>
                                <a href="{{ route('products.index') }}" class="reportsViewAllLink">
                                    View All
                                    <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                                </a>
                            </div>
                            <div id="reportsTopProductsList">
                                @forelse ($topProducts as $product)
                                    <div class="reportsTopProductItem">
                                        <span>{{ $product->product_name }}</span>
                                        <span>{{ $product->units }} sold</span>
                                        <span>₱{{ number_format((float) $product->revenue, 2) }}</span>
                                    </div>
                                @empty
                                    <p>No products sold for this period.</p>
                                @endforelse
                            </div>
                        </article>
                    </section>
                    <!-- Payment Breakdown and Sales Summary -->
                    <section id="reportsSalesDetails" aria-label="Sales details">  
                        <article id="reportsPaymentBreakdown" class="reportsPanel">
                            <div class="reportsPanelHeader">
                                <h2>Sales by Payment Method</h2>
                            </div>
                            <div id="reportsPaymentContent">
                                <p>Cash: ₱{{ number_format((float) ($paymentTotals['cash'] ?? 0), 2) }}</p>
                                <p>GCash: ₱{{ number_format((float) ($paymentTotals['gcash'] ?? 0), 2) }}</p>
                                <p>Cash + GCash: ₱{{ number_format((float) ($paymentTotals['split'] ?? 0), 2) }}</p>
                            </div>
                        </article>
                        <article id="reportsSalesSummary" class="reportsPanel">
                            <div class="reportsPanelHeader">
                                <h2>Sales Summary</h2>
                            </div>
                            <div id="reportsSummaryList">
                                <div class="reportsSummaryItem">
                                    <div>
                                        <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                                        <span>Total Sales</span>
                                    </div>
                                    <span>₱{{ number_format($salesTotal, 2) }}</span>
                                </div>
                                <div class="reportsSummaryItem">
                                    <div>
                                        <span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span>
                                        <span>Total Transactions</span>
                                    </div>
                                    <span>{{ $transactionCount }}</span>
                                </div>
                                <div class="reportsSummaryItem">
                                    <div>
                                        <span class="material-symbols-outlined" aria-hidden="true">receipt_long</span>
                                        <span>Average Sale per Transaction</span>
                                    </div>
                                    <span>₱{{ number_format($averageSale, 2) }}</span>
                                </div>
                            </div>
                        </article>
                    </section>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
