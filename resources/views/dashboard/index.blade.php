
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
            <nav id="dashboardSidebarNavigation" aria-label="Main navigation">
                @can('DashboardView')<a href="{{ route('dashboard') }}" class="dashboardNavLink active" aria-current="page">
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
                @canany(['SalesViewAll', 'SalesViewOwn'])<a href="{{ route('sales.index') }}" class="dashboardNavLink">
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
        <main id="dashboardMain">
            <header id="dashboardHeader">
                <div id="dashboardHeaderTitle">
                    <h1>Dashboard</h1>
                    <p>Overview of sales, inventory, and operations</p>
                </div>
                <div id="dashboardHeaderProfile">
                    <span class="material-symbols-outlined" id="dashboardProfileIcon" aria-hidden="true">account_circle</span>
                    <div id="dashboardProfileDetails">
                        <h2>Ivan Mark Lizardo</h2>
                        <p>Admin</p>
                    </div>
                </div>
            </header>
            <div id="dashboardContent">
                <!-- Dashboard Statistics -->
                <section id="dashboardStatistics" aria-label="Sales statistics">
                    <article id="dashboardTodaySalesCard" class="dashboardStatCard">
                        <div class="dashboardStatInformation">
                            <div class="dashboardStatValue">
                                <h2>₱12,450.00</h2>
                                <span class="dashboardGrowthBadge">
                                    <span class="material-symbols-outlined" aria-hidden="true">arrow_upward</span>
                                    +10.8%
                                </span>
                            </div>
                            <p>Today's Sales</p>
                        </div>
                        <div class="dashboardStatIcon dashboardVioletIcon">
                            <span class="material-symbols-outlined" aria-hidden="true">point_of_sale</span>
                        </div>
                    </article>
                    <article id="dashboardTransactionsCard" class="dashboardStatCard">
                        <div class="dashboardStatInformation">
                            <div class="dashboardStatValue">
                                <h2>38</h2>
                                <span class="dashboardGrowthBadge">
                                    <span class="material-symbols-outlined" aria-hidden="true">arrow_upward</span>
                                    +12.5%
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
                                <h2>20</h2>
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
                                <h2>₱327.63</h2>
                            </div>
                            <p>Average Order Value</p>
                        </div>
                        <div class="dashboardStatIcon dashboardGreenIcon">
                            <span class="material-symbols-outlined" aria-hidden="true">paid</span>
                        </div>
                    </article>
                </section>
                <!-- Chart Containers - Reserved for Backend Team -->
                <section id="dashboardCharts" aria-label="Sales charts">
                    <div id="dashboardTopSellingProducts"></div>
                    <div id="dashboardSalesByCategory"></div>
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
                                    <tr>
                                        <td>
                                            <span class="material-symbols-outlined" aria-hidden="true">receipt_long</span>
                                            SI-047681
                                        </td>
                                        <td>Dine-In</td>
                                        <td>₱450.00</td>
                                        <td>10:45 AM</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="material-symbols-outlined" aria-hidden="true">receipt_long</span>
                                            SI-047680
                                        </td>
                                        <td>Dine-In</td>
                                        <td>₱320.00</td>
                                        <td>9:32 AM</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="material-symbols-outlined" aria-hidden="true">receipt_long</span>
                                            SI-047679
                                        </td>
                                        <td>Dine-In</td>
                                        <td>₱680.00</td>
                                        <td>7:31 PM</td>
                                    </tr>
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
                            <a href="{{ route('products.index') }}" class="dashboardPanelLink">
                                View All
                                <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                            </a>
                        </div>
                        <div id="dashboardLowStockList">
                            <div class="dashboardLowStockItem">
                                <div class="dashboardLowStockProduct">
                                    <span class="material-symbols-outlined" aria-hidden="true">image</span>
                                    <span>Coke Mismo</span>
                                </div>
                                <span class="dashboardStockBadge">5 left</span>
                            </div>
                            <div class="dashboardLowStockItem">
                                <div class="dashboardLowStockProduct">
                                    <span class="material-symbols-outlined" aria-hidden="true">image</span>
                                    <span>Fries</span>
                                </div>
                                <span class="dashboardStockBadge">8 left</span>
                            </div>
                            <div class="dashboardLowStockItem">
                                <div class="dashboardLowStockProduct">
                                    <span class="material-symbols-outlined" aria-hidden="true">image</span>
                                    <span>Chicken Wings</span>
                                </div>
                                <span class="dashboardStockBadge">6 left</span>
                            </div>
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
                                <span>₱12,450.00</span>
                            </div>
                            <div class="dashboardSummaryItem">
                                <span>Total Transaction</span>
                                <span>38</span>
                            </div>
                            <div class="dashboardSummaryItem">
                                <span>Items Sold</span>
                                <span>112</span>
                            </div>
                        </div>
                    </article>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
