
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
                    <form id="reportsInventoryFilters" method="GET" action="{{ route('reports.inventory') }}">
                        <div id="reportsInventoryCategoryFilter">
                            <label for="reportsInventoryCategory">Category</label>
                            <select id="reportsInventoryCategory" name="category">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="reportsInventoryStock">Stock status</label>
                            <select id="reportsInventoryStock" name="stock">
                                <option value="">All Stock</option>
                                <option value="in_stock" @selected(request('stock') === 'in_stock')>In Stock</option>
                                <option value="low_stock" @selected(request('stock') === 'low_stock')>Low Stock</option>
                                <option value="out_of_stock" @selected(request('stock') === 'out_of_stock')>Out of Stock</option>
                            </select>
                        </div>
                        <button type="submit" id="reportsInventoryGenerateButton">Apply filters</button>
                        @can('ReportsExport')
                            <button type="submit" id="reportsInventoryExportButton" formaction="{{ route('reports.export') }}" name="type" value="inventory">Export CSV</button>
                        @endcan
                    </form>
                    <!-- Inventory Statistics -->
                    <section id="reportsInventoryStatistics" aria-label="Inventory statistics">
                        <article class="reportsInventoryStatCard">
                            <span class="material-symbols-outlined reportsInventoryStatIcon" aria-hidden="true">deployed_code</span>
                            <div class="reportsInventoryStatInformation">
                                <p>Total Products</p>
                                <h2>{{ $totalProducts }}</h2>
                            </div>
                        </article>
                        <article class="reportsInventoryStatCard">
                            <span class="material-symbols-outlined reportsInventoryStatIcon" aria-hidden="true">inventory_2</span>
                            <div class="reportsInventoryStatInformation">
                                <p>Total Stock Units</p>
                                <h2>{{ number_format($totalStock) }}</h2>
                            </div>
                        </article>
                        <article class="reportsInventoryStatCard">
                            <span class="material-symbols-outlined reportsInventoryStatIcon" aria-hidden="true">warning</span>
                            <div class="reportsInventoryStatInformation">
                                <p>Low Stock Items</p>
                                <h2>{{ $lowStock }}</h2>
                            </div>
                        </article>
                        <article class="reportsInventoryStatCard">
                            <span class="material-symbols-outlined reportsInventoryStatIcon reportsInventoryDangerIcon" aria-hidden="true">block</span>
                            <div class="reportsInventoryStatInformation">
                                <p>Out of Stock</p>
                                <h2>{{ $outOfStock }}</h2>
                            </div>
                        </article>
                    </section>
                    <!-- Inventory charts -->
                    <section id="reportsInventoryCharts" aria-label="Inventory charts">
                        <article id="reportsInventoryByCategory" class="reportsPanel">
                            <div class="reportsPanelHeader">
                                <h2>Inventory by Category</h2>
                            </div>
                            <div id="reportsInventoryCategoryChart">
                                <table>
                                    <thead><tr><th>Category</th><th>Stock units</th></tr></thead>
                                    <tbody>
                                        @forelse ($categorySummary as $category)
                                            <tr>
                                                <td>
                                                    {{ $category->name }}
                                                    <progress aria-label="Stock for {{ $category->name }}"
                                                        value="{{ $category->stock_units }}"
                                                        max="{{ max(1, $categorySummary->max('stock_units')) }}"></progress>
                                                </td>
                                                <td>{{ $category->stock_units }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="2">No products found.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </article>
                        <article id="reportsStockStatusOverview" class="reportsPanel">
                            <div class="reportsPanelHeader">
                                <h2>Stock Status Overview</h2>
                            </div>
                            <div id="reportsStockStatusChart">
                                <p>In stock: {{ $totalProducts - $lowStock - $outOfStock }}</p>
                                <p>Low stock: {{ $lowStock }}</p>
                                <p>Out of stock: {{ $outOfStock }}</p>
                            </div>
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
                                        <th scope="col">Threshold</th>
                                        <th scope="col">Stock Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->category?->name }}</td>
                                            <td>{{ $product->stock_quantity }}</td>
                                            <td>{{ $product->low_stock_threshold }}</td>
                                            <td>
                                                {{ $product->stock_quantity === 0
                                                    ? 'Out of Stock'
                                                    : ($product->stock_quantity <= $product->low_stock_threshold ? 'Low Stock' : 'In Stock') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5">No products found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <p>Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</p>
                        <nav aria-label="Inventory report pagination">
                            @if ($products->previousPageUrl()) <a href="{{ $products->previousPageUrl() }}">Previous</a> @endif
                            @if ($products->nextPageUrl()) <a href="{{ $products->nextPageUrl() }}">Next</a> @endif
                        </nav>
                    </section>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
