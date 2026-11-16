
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report | GG Billiards & Autocare</title>
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
            <!-- Inventory Report Content -->
            <div id="reportsContent">
                <!-- Report Navigation -->
                <nav id="reportsTabs" aria-label="Report types">
                    <a href="{{ route('reports.index') }}" class="reportsTabButton">
                        <span class="material-symbols-outlined" aria-hidden="true">bar_chart</span>
                        Sales Report
                    </a>
                    <a href="{{ route('reports.inventory') }}" class="reportsTabButton active" aria-current="page">
                        <span class="material-symbols-outlined" aria-hidden="true">inventory_2</span>
                        Inventory Report
                    </a>
                </nav>
                <!-- Inventory Report -->
                <section id="reportsInventorySection" aria-label="Inventory report">
                    <!-- Filters and Actions -->
                    <div id="reportsInventoryFilters">
                        <div id="reportsInventoryDateFilter">
                            <label for="reportsInventoryDateRange">Date Range</label>
                            <div id="reportsInventoryDateContainer">
                                <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                                <select id="reportsInventoryDateRange">
                                    <option value="jan2025">Jan 01, 2025 – Jan 31, 2025</option>
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                </select>
                            </div>
                        </div>
                        <div id="reportsInventoryCategoryFilter">
                            <label for="reportsInventoryCategory">Category</label>
                            <select id="reportsInventoryCategory">
                                <option value="all">All Categories</option>
                                <option value="silog">Silog Meals</option>
                                <option value="poppersRice">Chicken Poppers with Rice</option>
                                <option value="riceMeals">Rice Meals</option>
                                <option value="wings">Chicken Wings</option>
                                <option value="poppers">Chicken Poppers</option>
                                <option value="pulutan">Pulutan</option>
                                <option value="snacks">Snacks &amp; Appetizers</option>
                                <option value="extras">Extras &amp; Add-ons</option>
                                <option value="softdrinks">Softdrinks &amp; Refreshments</option>
                                <option value="fruitySoda">Fruity Soda</option>
                                <option value="frappe">Frappe</option>
                                <option value="ggTower">GG Tower</option>
                                <option value="beer">Beer</option>
                                <option value="hardLiquors">Hard Liquors</option>
                                <option value="brandy">Brandy</option>
                            </select>
                        </div>
                        <button type="button" id="reportsInventoryGenerateButton">
                            <span class="material-symbols-outlined" aria-hidden="true">refresh</span>
                            Generate Report
                        </button>
                        <button type="button" id="reportsInventoryExportButton">
                            <span class="material-symbols-outlined" aria-hidden="true">download</span>
                            Export
                            <span class="material-symbols-outlined" aria-hidden="true">expand_more</span>
                        </button>
                    </div>
                    <!-- Inventory Statistics -->
                    <section id="reportsInventoryStatistics" aria-label="Inventory statistics">
                        <article class="reportsInventoryStatCard">
                            <span class="material-symbols-outlined reportsInventoryStatIcon" aria-hidden="true">deployed_code</span>
                            <div class="reportsInventoryStatInformation">
                                <p>Total Products</p>
                                <h2>85</h2>
                            </div>
                        </article>
                        <article class="reportsInventoryStatCard">
                            <span class="material-symbols-outlined reportsInventoryStatIcon" aria-hidden="true">inventory_2</span>
                            <div class="reportsInventoryStatInformation">
                                <p>Total Stock Units</p>
                                <h2>1,248</h2>
                            </div>
                        </article>
                        <article class="reportsInventoryStatCard">
                            <span class="material-symbols-outlined reportsInventoryStatIcon" aria-hidden="true">warning</span>
                            <div class="reportsInventoryStatInformation">
                                <p>Low Stock Items</p>
                                <h2>8</h2>
                            </div>
                        </article>
                        <article class="reportsInventoryStatCard">
                            <span class="material-symbols-outlined reportsInventoryStatIcon reportsInventoryDangerIcon" aria-hidden="true">block</span>
                            <div class="reportsInventoryStatInformation">
                                <p>Out of Stock</p>
                                <h2>3</h2>
                            </div>
                        </article>
                    </section>
                    <!-- Empty Chart Containers -->
                    <section id="reportsInventoryCharts" aria-label="Inventory charts">
                        <article id="reportsInventoryByCategory" class="reportsPanel">
                            <div class="reportsPanelHeader">
                                <h2>Inventory by Category</h2>
                            </div>
                            <div id="reportsInventoryCategoryChart"></div>
                        </article>
                        <article id="reportsStockStatusOverview" class="reportsPanel">
                            <div class="reportsPanelHeader">
                                <h2>Stock Status Overview</h2>
                            </div>
                            <div id="reportsStockStatusChart"></div>
                        </article>
                    </section>
                    <!-- Inventory Stock Details -->
                    <section id="reportsInventoryDetails" class="reportsPanel" aria-label="Inventory stock details">
                        <div class="reportsPanelHeader">
                            <h2>Inventory Stock Details</h2>
                        </div>
                        <div id="reportsInventoryTableContainer">
                            <table id="reportsInventoryTable">
                                <thead>
                                    <tr>
                                        <th scope="col">Product</th>
                                        <th scope="col">Category</th>
                                        <th scope="col">Current Stock</th>
                                        <th scope="col">Stock Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="reportsInventoryProduct">
                                                <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Bottled Water placeholder">
                                                <span>Bottled Water</span>
                                            </div>
                                        </td>
                                        <td>Softdrinks &amp; Refreshments</td>
                                        <td>32</td>
                                        <td><span class="reportsInventoryStatus reportsInventoryInStock">In Stock</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="reportsInventoryProduct">
                                                <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Coke Mismo placeholder">
                                                <span>Coke Mismo</span>
                                            </div>
                                        </td>
                                        <td>Softdrinks &amp; Refreshments</td>
                                        <td>7</td>
                                        <td><span class="reportsInventoryStatus reportsInventoryLowStock">Low Stock</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="reportsInventoryProduct">
                                                <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Fries placeholder">
                                                <span>Fries</span>
                                            </div>
                                        </td>
                                        <td>Snacks &amp; Appetizers</td>
                                        <td>0</td>
                                        <td><span class="reportsInventoryStatus reportsInventoryOutOfStock">Out of Stock</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="reportsInventoryProduct">
                                                <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Tapsilog placeholder">
                                                <span>Tapsilog</span>
                                            </div>
                                        </td>
                                        <td>Silog Meals</td>
                                        <td>18</td>
                                        <td><span class="reportsInventoryStatus reportsInventoryInStock">In Stock</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="reportsInventoryProduct">
                                                <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Nutella Frappe placeholder">
                                                <span>Nutella Frappe</span>
                                            </div>
                                        </td>
                                        <td>Frappe</td>
                                        <td>4</td>
                                        <td><span class="reportsInventoryStatus reportsInventoryLowStock">Low Stock</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
