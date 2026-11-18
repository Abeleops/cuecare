
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | GG Billiards & Autocare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body>
    <div id="dashboardPage">
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
        <main id="dashboardMain">
            <header id="dashboardHeader">
                <div id="dashboardHeaderTitle">
                    <h1>Dashboard</h1>
                    <p>Overview of sales, inventory, and operations</p>
                </div>
                @include('partials.profile')
            </header>
            <div id="dashboardContent">
                <form method="GET" action="{{ route('dashboard') }}">
                    <label for="dashboardStartDate">From</label>
                    <input id="dashboardStartDate" type="date" name="start_date" value="{{ $startDate }}">
                    <label for="dashboardEndDate">To</label>
                    <input id="dashboardEndDate" type="date" name="end_date" value="{{ $endDate }}">
                    <button type="submit">Apply</button>
                </form>
                @if ($errors->any()) <p role="alert">{{ $errors->first() }}</p> @endif
                <!-- Dashboard Statistics -->
                <section id="dashboardStatistics" aria-label="Sales statistics">
                    <article id="dashboardTodaySalesCard" class="dashboardStatCard">
                        <div class="dashboardStatInformation">
                            <div class="dashboardStatValue">
                                <h2>₱{{ number_format($periodTotal, 2) }}</h2>
                                <span class="dashboardGrowthBadge">
                                    <span class="material-symbols-outlined" aria-hidden="true">arrow_upward</span>
                                    {{ sprintf('%+.1f%%', $salesGrowth) }}
                                </span>
                            </div>
                            <p>Sales in selected period</p>
                        </div>
                        <div class="dashboardStatIcon dashboardVioletIcon">
                            <span class="material-symbols-outlined" aria-hidden="true">point_of_sale</span>
                        </div>
                    </article>
                    <article id="dashboardTransactionsCard" class="dashboardStatCard">
                        <div class="dashboardStatInformation">
                            <div class="dashboardStatValue">
                                <h2>{{ $periodTransactions }}</h2>
                                <span class="dashboardGrowthBadge">
                                    <span class="material-symbols-outlined" aria-hidden="true">arrow_upward</span>
                                    {{ sprintf('%+.1f%%', $transactionGrowth) }}
                                </span>
                            </div>
                            <p>Transactions</p>
                        </div>
                        <div class="dashboardStatIcon dashboardBlueIcon">
                            <span class="material-symbols-outlined" aria-hidden="true">receipt_long</span>
                        </div>
                    </article>
                    <article id="dashboardCustomersCard" class="dashboardStatCard">
                        <div class="dashboardStatInformation">
                            <div class="dashboardStatValue">
                                <h2>{{ $customerCount ?? 0 }}</h2>
                            </div>
                            <p>Customers</p>
                        </div>
                        <div class="dashboardStatIcon dashboardOrangeIcon">
                            <span class="material-symbols-outlined" aria-hidden="true">groups</span>
                        </div>
                    </article>
                    <article id="dashboardAverageOrderCard" class="dashboardStatCard">
                        <div class="dashboardStatInformation">
                            <div class="dashboardStatValue">
                                <h2>₱{{ number_format($averageOrderValue ?? 0, 2) }}</h2>
                            </div>
                            <p>Average Order Value</p>
                        </div>
                        <div class="dashboardStatIcon dashboardGreenIcon">
                            <span class="material-symbols-outlined" aria-hidden="true">paid</span>
                        </div>
                    </article>
                </section>
                <!-- Sales charts -->
                <section id="dashboardCharts" aria-label="Sales charts">
                    <div id="dashboardTopSellingProducts" class="dashboardPanel">
                        <h2>Top selling products</h2>
                        <table><thead><tr><th>Product</th><th>Units</th><th>Revenue</th></tr></thead><tbody>
                            @forelse ($topProducts as $product)
                                <tr>
                                    <td>
                                        {{ $product->product_name }}
                                        <progress aria-label="Units sold for {{ $product->product_name }}"
                                            value="{{ $product->units_sold }}"
                                            max="{{ max(1, $topProducts->max('units_sold')) }}"></progress>
                                    </td>
                                    <td>{{ $product->units_sold }}</td>
                                    <td>₱{{ number_format((float) $product->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3">No sales today.</td></tr>
                            @endforelse
                        </tbody></table>
                    </div>
                    <div id="dashboardSalesByCategory" class="dashboardPanel">
                        <h2>Sales by category</h2>
                        <table><thead><tr><th>Category</th><th>Sales</th><th>Share</th></tr></thead><tbody>
                            @forelse ($categorySales as $category)
                                <tr>
                                    <td>
                                        {{ [
                                            'food' => 'Food',
                                            'beverage' => 'Beverages',
                                            'alcoholic' => 'Alcoholic Drinks',
                                        ][$category->category_group] ?? $category->category_group }}
                                        <progress aria-label="Category sales share" value="{{ $category->revenue }}"
                                            max="{{ max(1, $categorySales->sum('revenue')) }}"></progress>
                                    </td>
                                    <td>₱{{ number_format((float) $category->revenue, 2) }}</td>
                                    <td>{{ number_format($categorySales->sum('revenue') > 0 ? $category->revenue / $categorySales->sum('revenue') * 100 : 0, 1) }}%</td>
                                </tr>
                            @empty
                                <tr><td colspan="3">No sales today.</td></tr>
                            @endforelse
                        </tbody></table>
                    </div>
                </section>
                <!-- Dashboard Bottom Panels -->
                <section id="dashboardBottomPanels" aria-label="Dashboard activity">
                    <article id="dashboardRecentTransactions" class="dashboardPanel">
                        <div class="dashboardPanelHeader">
                            <div class="dashboardPanelTitle">
                                <span class="material-symbols-outlined dashboardPanelIcon" aria-hidden="true">format_list_bulleted</span>
                                <h2>Recent Transaction</h2>
                            </div>
                            <a href="{{ route('sales.index') }}" class="dashboardPanelLink">
                                View Transactions
                                <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                            </a>
                        </div>
                        <div id="dashboardTransactionTableContainer">
                            <table id="dashboardTransactionTable">
                                <thead>
                                    <tr>
                                        <th scope="col">Transaction No.</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentSales as $sale)
                                        <tr>
                                            <td>
                                                @if (
                                                    auth()->user()->hasPermission('SalesViewAll') ||
                                                    (auth()->user()->hasPermission('SalesViewOwn') && $sale->sold_by === auth()->id())
                                                )
                                                    <a href="{{ route('sales.show', $sale) }}">{{ $sale->transaction_code }}</a>
                                                @else
                                                    {{ $sale->transaction_code }}
                                                @endif
                                            </td>
                                            <td>{{ $sale->order_type === 'takeout' ? 'Takeout' : 'Dine-In' }}</td>
                                            <td>₱{{ number_format((float) $sale->total_amount, 2) }}</td>
                                            <td>{{ $sale->completed_at?->format('h:i A') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4">No transactions found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </article>
                    <article id="dashboardLowStockAlerts" class="dashboardPanel">
                        <div class="dashboardPanelHeader">
                            <div class="dashboardPanelTitle">
                                <span class="material-symbols-outlined dashboardPanelIcon" aria-hidden="true">warning</span>
                                <h2>Low Stock Alerts</h2>
                            </div>
                            <a href="{{ route('products.index', ['stock' => 'lowStock']) }}" class="dashboardPanelLink">
                                View All
                                <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                            </a>
                        </div>
                        <div id="dashboardLowStockList">
                            @forelse ($lowStockProducts as $product)
                                <div class="dashboardLowStockItem">
                                    <div class="dashboardLowStockProduct"><span>{{ $product->name }}</span></div>
                                    <span class="dashboardStockBadge">{{ $product->stock_quantity }} left</span>
                                </div>
                            @empty
                                <p>No low-stock products.</p>
                            @endforelse
                        </div>
                    </article>
                    <article id="dashboardTodaySummary" class="dashboardPanel">
                        <div class="dashboardPanelHeader">
                            <div class="dashboardPanelTitle">
                                <span class="material-symbols-outlined dashboardPanelIcon" aria-hidden="true">event</span>
                                <h2>Today's Summary</h2>
                            </div>
                        </div>
                        <div id="dashboardSummaryList">
                            <div class="dashboardSummaryItem">
                                <span>Total Sales</span>
                                <span>₱{{ number_format($todayTotal, 2) }}</span>
                            </div>
                            <div class="dashboardSummaryItem">
                                <span>Total Transaction</span>
                                <span>{{ $todayTransactions }}</span>
                            </div>
                            <div class="dashboardSummaryItem">
                                <span>Items Sold</span>
                                <span>{{ $itemsSold }}</span>
                            </div>
                        </div>
                    </article>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
