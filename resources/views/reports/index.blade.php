
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
            <nav id="dashboardSidebarNavigation" aria-label="Main navigation">
                <a href="{{ route('dashboard') }}" class="dashboardNavLink">
                    <span class="material-symbols-outlined" aria-hidden="true">dashboard</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('pos.index') }}" class="dashboardNavLink">
                    <span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span>
                    <span>Record Sale</span>
                </a>
                <a href="{{ route('products.index') }}" class="dashboardNavLink">
                    <span class="material-symbols-outlined" aria-hidden="true">deployed_code</span>
                    <span>Products</span>
                </a>
                <a href="{{ route('customers.index') }}" class="dashboardNavLink">
                    <span class="material-symbols-outlined" aria-hidden="true">groups</span>
                    <span>Customers</span>
                </a>
                <a href="{{ route('staff.index') }}" class="dashboardNavLink">
                    <span class="material-symbols-outlined" aria-hidden="true">account_box</span>
                    <span>Staff</span>
                </a>
                <a href="{{ route('sales.index') }}" class="dashboardNavLink">
                    <span class="material-symbols-outlined" aria-hidden="true">receipt_long</span>
                    <span>Sales History</span>
                </a>
                <a href="{{ route('reports.index') }}" class="dashboardNavLink active" aria-current="page">
                    <span class="material-symbols-outlined" aria-hidden="true">monitoring</span>
                    <span>Reports</span>
                </a>
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
                    <h1>Reports</h1>
                    <p>Generate sales and inventory reports</p>
                </div>
                <div id="dashboardHeaderProfile">
                    <span class="material-symbols-outlined" id="dashboardProfileIcon" aria-hidden="true">account_circle</span>
                    <div id="dashboardProfileDetails">
                        <h2>Ivan Mark Lizardo</h2>
                        <p>Admin</p>
                    </div>
                </div>
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
                    <div id="reportsFilters">
                        <div id="reportsDateFilter">
                            <label for="reportsDateRange">Date Range</label>
                            <div id="reportsDateRangeContainer">
                                <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                                <select id="reportsDateRange">
                                    <option value="jan2025">Jan 01, 2025 – Jan 31, 2025</option>
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                </select>
                            </div>
                        </div>
                        <div id="reportsPaymentFilter">
                            <label for="reportsPaymentMethod">Payment Method</label>
                            <select id="reportsPaymentMethod">
                                <option value="all">All Methods</option>
                                <option value="cash">Cash</option>
                                <option value="gcash">GCash</option>
                            </select>
                        </div>
                        <button type="button" id="reportsGenerateButton">
                            <span class="material-symbols-outlined" aria-hidden="true">refresh</span>
                            Generate Report
                        </button>
                        <button type="button" id="reportsExportButton">
                            <span class="material-symbols-outlined" aria-hidden="true">download</span>
                            Export
                            <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
                        </button>
                    </div>
                    <!-- Sales Statistics -->
                    <section id="reportsSalesStatistics" aria-label="Sales statistics">
                        <article class="reportsStatCard">
                            <span class="material-symbols-outlined reportsStatIcon" aria-hidden="true">shopping_cart</span>
                            <div class="reportsStatInformation">
                                <p>Total Sales</p>
                                <h2>₱12,450.00</h2>
                            </div>
                        </article>
                        <article class="reportsStatCard">
                            <span class="material-symbols-outlined reportsStatIcon" aria-hidden="true">receipt_long</span>
                            <div class="reportsStatInformation">
                                <p>Total Transactions</p>
                                <h2>54</h2>
                            </div>
                        </article>
                        <article class="reportsStatCard">
                            <span class="material-symbols-outlined reportsStatIcon" aria-hidden="true">shopping_basket</span>
                            <div class="reportsStatInformation">
                                <p>Average Sale</p>
                                <h2>₱230.56</h2>
                            </div>
                        </article>
                        <article class="reportsStatCard reportsGrowthCard">
                            <span class="material-symbols-outlined" aria-hidden="true">trending_up</span>
                            <div class="reportsStatInformation">
                                <h2>+12.8%</h2>
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
                            <div id="reportsSalesTrendChart"></div>
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
                                <div class="reportsTopProductItem">
                                    <div class="reportsTopProductInformation">
                                        <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Bottled Water placeholder">
                                        <span>Bottled Water</span>
                                    </div>
                                    <span>32 sold</span>
                                    <span>₱480.00</span>
                                </div>
                                <div class="reportsTopProductItem">
                                    <div class="reportsTopProductInformation">
                                        <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Softdrink placeholder">
                                        <span>Softdrink</span>
                                    </div>
                                    <span>28 sold</span>
                                    <span>₱560.00</span>
                                </div>
                                <div class="reportsTopProductItem">
                                    <div class="reportsTopProductInformation">
                                        <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Chips placeholder">
                                        <span>Chips</span>
                                    </div>
                                    <span>18 sold</span>
                                    <span>₱360.00</span>
                                </div>
                                <div class="reportsTopProductItem">
                                    <div class="reportsTopProductInformation">
                                        <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Instant Noodles placeholder">
                                        <span>Instant Noodles</span>
                                    </div>
                                    <span>15 sold</span>
                                    <span>₱225.00</span>
                                </div>
                                <div class="reportsTopProductItem">
                                    <div class="reportsTopProductInformation">
                                        <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Cup Noodles placeholder">
                                        <span>Cup Noodles</span>
                                    </div>
                                    <span>12 sold</span>
                                    <span>₱120.00</span>
                                </div>
                            </div>
                        </article>
                    </section>
                    <!-- Payment Breakdown and Sales Summary -->
                    <section id="reportsSalesDetails" aria-label="Sales details">  
                        <article id="reportsPaymentBreakdown" class="reportsPanel">
                            <div class="reportsPanelHeader">
                                <h2>Sales by Payment Method</h2>
                            </div>
                            <div id="reportsPaymentContent"></div>
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
                                    <span>₱12,450.00</span>
                                </div>
                                <div class="reportsSummaryItem">
                                    <div>
                                        <span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span>
                                        <span>Total Transactions</span>
                                    </div>
                                    <span>54</span>
                                </div>
                                <div class="reportsSummaryItem">
                                    <div>
                                        <span class="material-symbols-outlined" aria-hidden="true">receipt_long</span>
                                        <span>Average Sale per Transaction</span>
                                    </div>
                                    <span>₱230.56</span>
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
