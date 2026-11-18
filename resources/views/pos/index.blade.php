
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Sale | GG Billiards & Autocare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/pos.js'])
</head>
<body>
    <div id="posPage"
        data-cart-url="{{ route('pos.cart') }}"
        data-cart-clear-url="{{ route('pos.cart.clear') }}"
        data-discount-url="{{ route('pos.cart.discount') }}"
        data-cart-update-url="{{ route('pos.cart.update', ['product' => '__ID__']) }}"
        data-cart-remove-url="{{ route('pos.cart.remove', ['product' => '__ID__']) }}"
        data-customer-search-url="{{ route('pos.customers.search') }}">
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
                    <h1>Record Sale</h1>
                    <p>Create a new transaction for food &amp; beverages</p>
                </div>
                @include('partials.profile')
            </header>
            <!-- POS Content -->
            <div id="posContent">
                @if ($errors->any())
                    <div role="alert">{{ $errors->first() }}</div>
                @endif
                <!-- Product Selection Panel -->
                <section id="posProductsPanel" aria-label="Product selection">
                    <!-- Category Navigation -->
                    <div id="posCategoryNavigation">
                        <a class="posCategoryButton {{ !request('group') ? 'active' : '' }}" href="{{ route('pos.index') }}">All Items</a>
                        @foreach (['food' => 'Food', 'beverage' => 'Beverages', 'alcoholic' => 'Alcoholic Drinks'] as $key => $label)
                            <a class="posCategoryButton {{ request('group') === $key ? 'active' : '' }}" href="{{ route('pos.index', ['group' => $key]) }}">{{ $label }}</a>
                        @endforeach
                    </div>
                    <!-- Search and View Options -->
                    <div id="posProductsToolbar">
                        <form id="posSearchContainer" method="GET" action="{{ route('pos.index') }}">
                            <span class="material-symbols-outlined" aria-hidden="true">search</span>
                            <input type="search" id="posSearchInput" name="search" value="{{ request('search') }}"
                                maxlength="100" placeholder="Search products" aria-label="Search products">
                            <select name="category" aria-label="Category">
                                <option value="">All categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if (request('group')) <input type="hidden" name="group" value="{{ request('group') }}"> @endif
                            <button type="submit">Search</button>
                        </form>
                        <div id="posViewOptions">
                            <button type="button" class="posViewButton active" aria-label="Grid view" aria-pressed="true">
                                <span class="material-symbols-outlined" aria-hidden="true">grid_view</span>
                            </button>
                            <button type="button" class="posViewButton" aria-label="List view" aria-pressed="false">
                                <span class="material-symbols-outlined" aria-hidden="true">view_list</span>
                            </button>
                        </div>
                    </div>
                    <!-- Products Heading -->
                    <div id="posProductsHeading">
                        <h2>Products <span>({{ $products->count() }} items)</span></h2>
                    </div>
                    <!-- Product Cards -->
                    <div id="posProductsList">
                        @if(isset($products))
                            @foreach($products as $product)
                                <article class="posProductCard">
                                    <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('images/noImagePlaceholderWhite.png') }}"
                                        alt="{{ $product->name }}" class="posProductImage">
                                    <div class="posProductDetails">
                                        <h3>{{ $product->name }}</h3>
                                        <div class="posProductFooter">
                                            <span>₱{{ number_format((float) $product->price, 2) }}</span>
                                            <button type="button" class="posAddProductButton"
                                                data-product-id="{{ $product->id }}"
                                                data-product-name="{{ $product->name }}"
                                                data-product-price="{{ $product->price }}"
                                                aria-label="Add {{ $product->name }} to order">
                                                <span class="material-symbols-outlined" aria-hidden="true">add</span>
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                            @if ($products->isEmpty()) <p>No products found.</p> @endif
                        @endif
                    </div>
                </section>
                <!-- Current Order Panel -->
                <section id="posOrderPanel" aria-label="Current order">
                    <div id="posOrderHeader">
                        <div id="posOrderTitle">
                            <span class="material-symbols-outlined" aria-hidden="true">receipt_long</span>
                            <h2>Current Order</h2>
                        </div>
                        <button type="button" id="posClearOrderButton">
                            <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                            Clear All
                        </button>
                    </div>
                    <!-- Order Table -->
                    <div id="posOrderItemsContainer">
                        <table id="posOrderTable">
                            <thead>
                                <tr>
                                    <th scope="col">Item</th>
                                    <th scope="col">Qty</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Subtotal</th>
                                    <th scope="col"><span class="posVisuallyHidden">Remove</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="5">No items in the order.</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Order Totals -->
                    <div id="posOrderSummary">
                        <div class="posOrderSummaryRow">
                            <span>Subtotal</span>
                            <span>₱0.00</span>
                        </div>
                        @can('DiscountApply')
                        <div class="posOrderSummaryRow">
                            <label for="posDiscountInput">Discount</label>
                            <div id="posDiscountControl">
                                <select id="posDiscountType" aria-label="Discount type">
                                    <option value="fixed">Fixed (₱)</option>
                                    <option value="percentage">Percentage (%)</option>
                                </select>
                                <input type="number" id="posDiscountInput" value="0" min="0" step="0.01" aria-label="Discount amount">
                                <button type="button" id="posApplyDiscount">Apply</button>
                            </div>
                        </div>
                        @endcan
                        <div class="posOrderSummaryRow"><span>Discount</span><span id="posDiscountAmount">₱0.00</span></div>
                        <div id="posOrderTotal">
                            <strong>Total</strong>
                            <strong>₱0.00</strong>
                        </div>
                    </div>
                    <!-- Order Actions -->
                    <div id="posOrderActions">
                        <button type="button" id="posCancelButton">Cancel</button>
                        <button type="button" id="posCompleteSaleButton">
                            <span class="material-symbols-outlined" aria-hidden="true">credit_card</span>
                            Complete Sale
                        </button>
                    </div>
                </section>
            </div>
        </main>
    </div>
    @include('pos.modals.checkout')
    @include('pos.modals.addCustomer')
    @include('pos.modals.success')
</body>
</html>
