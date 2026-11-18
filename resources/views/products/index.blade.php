
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List | GG Billiards & Autocare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/products.js'])
</head>
<body>
    <div id="productsPage">
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
                    <h1>Product List</h1>
                    <p>Manage menu items, prices, and inventory</p>
                </div>
                @include('partials.profile')
            </header>
            <!-- Products Content -->
            <div id="productsContent">
                @if (session('status'))
                    <div class="productsStatusMessage" role="status">{{ session('status') }}</div>
                @endif
                <!-- Category Tabs and Add Product -->
                <section id="productsToolbar">
                    <div id="productsCategoryTabs">
                        <a class="productsCategoryTab {{ !request('group') ? 'active' : '' }}" href="{{ route('products.index') }}">All Products ({{ $allProductCount }})</a>
                        @foreach (['food' => 'Food', 'beverage' => 'Beverages', 'alcoholic' => 'Alcoholic Drinks'] as $group => $label)
                            <a class="productsCategoryTab {{ request('group') === $group ? 'active' : '' }}"
                                href="{{ route('products.index', ['group' => $group]) }}">
                                {{ $label }} ({{ $groupCounts[$group] }})
                            </a>
                        @endforeach
                    </div>
                    @can('ProductsManage')<button type="button" id="productsAddButton">
                        <span class="material-symbols-outlined" aria-hidden="true">add</span>
                        Add Product
                    </button>@endcan
                </section>
                <!-- Search and Filters -->
                <form id="productsFilterSection" aria-label="Product filters" method="GET" action="{{ route('products.index') }}">
                    @if (request('group')) <input type="hidden" name="group" value="{{ request('group') }}"> @endif
                    <div id="productsSearchContainer">
                        <span class="material-symbols-outlined" aria-hidden="true">search</span>
                        <input type="search" id="productsSearchInput" name="search" value="{{ request('search') }}"
                            maxlength="100" placeholder="Search products" aria-label="Search products">
                    </div>
                    <div id="productsCategoryFilter">
                        <label for="productsCategorySelect">Category:</label>
                        <select id="productsCategorySelect" name="category">
                            <option value="">All Categories</option>
                            @foreach ($availableCategories as $category)
                                <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="productsStockFilter">
                        <label for="productsStockSelect">Stock:</label>
                        <select id="productsStockSelect" name="stock">
                            <option value="">All Stock</option>
                            <option value="inStock" @selected(request('stock') === 'inStock')>In Stock</option>
                            <option value="lowStock" @selected(request('stock') === 'lowStock')>Low Stock</option>
                            <option value="outOfStock" @selected(request('stock') === 'outOfStock')>Out of Stock</option>
                        </select>
                    </div>
                    <button type="submit">Apply filters</button>
                </form>
                <!-- Products Table -->
                <section id="productsTableSection" aria-label="Product list">
                    <div id="productsTableContainer">
                        <table id="productsTable">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <input type="checkbox" id="productsSelectAll" aria-label="Select all products">
                                    </th>
                                    <th scope="col">
                                        <span class="productsTableHeading">
                                            Product
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">
                                        <span class="productsTableHeading">
                                            Category
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">
                                        <span class="productsTableHeading">
                                            Price
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">
                                        <span class="productsTableHeading">
                                            Stock
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($products->isNotEmpty())
                                    @foreach($products as $product)
                                        <tr>
                                            <td><input type="checkbox" aria-label="Select {{ $product->name }}"></td>
                                            <td>
                                                <div class="productsProductInfo">
                                                    <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('images/noImagePlaceholderWhite.png') }}"
                                                        alt="{{ $product->name }}" class="productsProductImage">
                                                    <span>{{ $product->name }}</span>
                                                </div>
                                            </td>
                                            <td><span class="productsCategoryBadge">{{ $product->category?->name }}</span></td>
                                            <td>₱{{ number_format((float) $product->price, 2) }}</td>
                                            <td>{{ $product->stock_quantity }}</td>
                                            <td>
                                                <span class="productsStatusBadge {{ $product->stock_quantity <= $product->low_stock_threshold
                                                    ? 'productsLowStock' : 'productsInStock' }}">
                                                    {{ $product->stock_quantity === 0
                                                        ? 'Out of Stock'
                                                        : ($product->stock_quantity <= $product->low_stock_threshold ? 'Low Stock' : 'In Stock') }}
                                                </span>
                                            </td>
                                            <td>
                                                @can('ProductsManage')
                                                    <div class="productsActionButtons">
                                                        <button type="button" class="productsEditButton"
                                                            data-product-id="{{ $product->id }}"
                                                            data-update-url="{{ route('products.update', $product) }}"
                                                            data-category-id="{{ $product->category_id }}"
                                                            data-stock-quantity="{{ $product->stock_quantity }}"
                                                            data-low-stock-threshold="{{ $product->low_stock_threshold }}"
                                                            aria-label="Edit {{ $product->name }}">
                                                            <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                                        </button>
                                                        <form method="POST" action="{{ route('products.destroy', $product) }}"
                                                            onsubmit="return confirm('Delete this product?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="productsDeleteButton" aria-label="Delete {{ $product->name }}">
                                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endcan
                                                @can('InventoryAdjust')
                                                    <details>
                                                        <summary>Adjust stock</summary>
                                                        <form method="POST" action="{{ route('products.stock', $product) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <label>Quantity change <input type="number" name="quantity_change" required></label>
                                                            <label>Reason <input type="text" name="note" maxlength="255" required></label>
                                                            <button type="submit">Save adjustment</button>
                                                        </form>
                                                    </details>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="7">No products found.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div id="productsPagination">
                        <p id="productsPaginationInfo">Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</p>
                        <nav id="productsPaginationControls" aria-label="Product pagination">
                            @if ($products->previousPageUrl()) <a href="{{ $products->previousPageUrl() }}">Previous</a> @endif
                            @if ($products->nextPageUrl()) <a href="{{ $products->nextPageUrl() }}">Next</a> @endif
                        </nav>
                    </div>
                </section>
            </div>
        </main>
    </div>
    @include('products.modals.add')
    @include('products.modals.edit')
</body>
</html>
