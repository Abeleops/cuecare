
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
    <div id="posPage">
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
                <a href="{{ route('pos.index') }}" class="dashboardNavLink active" aria-current="page">
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
                <a href="{{ route('reports.index') }}" class="dashboardNavLink">
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
                    <h1>Record Sale</h1>
                    <p>Create a new transaction for food &amp; beverages</p>
                </div>
                <div id="dashboardHeaderProfile">
                    <span class="material-symbols-outlined" id="dashboardProfileIcon" aria-hidden="true">account_circle</span>
                    <div id="dashboardProfileDetails">
                        <h2>Ivan Mark Lizardo</h2>
                        <p>Admin</p>
                    </div>
                </div>
            </header>
            <!-- POS Content -->
            <div id="posContent">
                <!-- Product Selection Panel -->
                <section id="posProductsPanel" aria-label="Product selection">
                    <!-- Category Navigation -->
                    <div id="posCategoryNavigation">
                        <button type="button" class="posCategoryButton active">
                            <span class="material-symbols-outlined" aria-hidden="true">apps</span>
                            All Items
                        </button>
                        <button type="button" class="posCategoryButton">
                            <span class="material-symbols-outlined" aria-hidden="true">restaurant</span>
                            Silog Meals
                        </button>
                        <button type="button" class="posCategoryButton">
                            <span class="material-symbols-outlined" aria-hidden="true">rice_bowl</span>
                            Rice Meals
                        </button>
                        <button type="button" class="posCategoryButton">
                            <span class="material-symbols-outlined" aria-hidden="true">set_meal</span>
                            Chicken Poppers
                        </button>
                        <button type="button" class="posCategoryButton">
                            <span class="material-symbols-outlined" aria-hidden="true">sports_bar</span>
                            Chicken Wings
                        </button>
                        <button type="button" id="posCategoryNextButton" aria-label="More categories">
                            <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                        </button>
                    </div>
                    <!-- Search and View Options -->
                    <div id="posProductsToolbar">
                        <div id="posSearchContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">search</span>
                            <input type="search" id="posSearchInput" placeholder="Search product (e.g., Mixilog, Fries, Coke...)" aria-label="Search products">
                        </div>
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
                        <h2>Products <span>(124 items)</span></h2>
                    </div>
                    <!-- Product Cards -->
                    <div id="posProductsList">
                        <!-- Static Sample Product 1 -->
                        <article class="posProductCard">
                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Mixilog placeholder" class="posProductImage">
                            <div class="posProductDetails">
                                <h3>Mixilog</h3>
                                <div class="posProductFooter">
                                    <span>₱130.00</span>
                                    <button type="button" class="posAddProductButton" aria-label="Add Mixilog to order">
                                        <span class="material-symbols-outlined" aria-hidden="true">add</span>
                                    </button>
                                </div>
                            </div>
                        </article>
                        <!-- Static Sample Product 2 -->
                        <article class="posProductCard">
                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Pork Sisig placeholder" class="posProductImage">
                            <div class="posProductDetails">
                                <h3>Pork Sisig</h3>
                                <div class="posProductFooter">
                                    <span>₱195.00</span>
                                    <button type="button" class="posAddProductButton" aria-label="Add Pork Sisig to order">
                                        <span class="material-symbols-outlined" aria-hidden="true">add</span>
                                    </button>
                                </div>
                            </div>
                        </article>
                        <!-- Static Sample Product 3 -->
                        <article class="posProductCard">
                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Buffalo Wings placeholder" class="posProductImage">
                            <div class="posProductDetails">
                                <h3>Buffalo Wings</h3>
                                <div class="posProductFooter">
                                    <span>₱170.00</span>
                                    <button type="button" class="posAddProductButton" aria-label="Add Buffalo Wings to order">
                                        <span class="material-symbols-outlined" aria-hidden="true">add</span>
                                    </button>
                                </div>
                            </div>
                        </article>
                        <!-- Static Sample Product 4 -->
                        <article class="posProductCard">
                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Fries placeholder" class="posProductImage">
                            <div class="posProductDetails">
                                <h3>Fries</h3>
                                <div class="posProductFooter">
                                    <span>₱100.00</span>
                                    <button type="button" class="posAddProductButton" aria-label="Add Fries to order">
                                        <span class="material-symbols-outlined" aria-hidden="true">add</span>
                                    </button>
                                </div>
                            </div>
                        </article>
                        <!-- Static Sample Product 5 -->
                        <article class="posProductCard">
                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Coke Mismo placeholder" class="posProductImage">
                            <div class="posProductDetails">
                                <h3>Coke Mismo</h3>
                                <div class="posProductFooter">
                                    <span>₱30.00</span>
                                    <button type="button" class="posAddProductButton" aria-label="Add Coke Mismo to order">
                                        <span class="material-symbols-outlined" aria-hidden="true">add</span>
                                    </button>
                                </div>
                            </div>
                        </article>
                        <!-- Static Sample Product 6 -->
                        <article class="posProductCard">
                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Nutella Frappe placeholder" class="posProductImage">
                            <div class="posProductDetails">
                                <h3>Nutella Frappe</h3>
                                <div class="posProductFooter">
                                    <span>₱129.00</span>
                                    <button type="button" class="posAddProductButton" aria-label="Add Nutella Frappe to order">
                                        <span class="material-symbols-outlined" aria-hidden="true">add</span>
                                    </button>
                                </div>
                            </div>
                        </article>
                        <!-- Static Sample Product 7 -->
                        <article class="posProductCard">
                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Strawberry Soda placeholder" class="posProductImage">
                            <div class="posProductDetails">
                                <h3>Strawberry Soda</h3>
                                <div class="posProductFooter">
                                    <span>₱80.00</span>
                                    <button type="button" class="posAddProductButton" aria-label="Add Strawberry Soda to order">
                                        <span class="material-symbols-outlined" aria-hidden="true">add</span>
                                    </button>
                                </div>
                            </div>
                        </article>
                        <!-- Static Sample Product 8 -->
                        <article class="posProductCard">
                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="San Mig Light placeholder" class="posProductImage">
                            <div class="posProductDetails">
                                <h3>San Mig Light</h3>
                                <div class="posProductFooter">
                                    <span>₱95.00</span>
                                    <button type="button" class="posAddProductButton" aria-label="Add San Mig Light to order">
                                        <span class="material-symbols-outlined" aria-hidden="true">add</span>
                                    </button>
                                </div>
                            </div>
                        </article>
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
                                <!-- Static Sample Order Item 1 -->
                                <tr>
                                    <td>
                                        <div class="posOrderProduct">
                                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Pork Sisig placeholder">
                                            <span>Pork Sisig</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="posQuantityControl">
                                            <button type="button" aria-label="Decrease Pork Sisig quantity">−</button>
                                            <span>1</span>
                                            <button type="button" aria-label="Increase Pork Sisig quantity">+</button>
                                        </div>
                                    </td>
                                    <td>₱195.00</td>
                                    <td>₱195.00</td>
                                    <td>
                                        <button type="button" class="posRemoveItemButton" aria-label="Remove Pork Sisig">
                                            <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                        </button>
                                    </td>
                                </tr>
                                <!-- Static Sample Order Item 2 -->
                                <tr>
                                    <td>
                                        <div class="posOrderProduct">
                                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Fries placeholder">
                                            <span>Fries</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="posQuantityControl">
                                            <button type="button" aria-label="Decrease Fries quantity">−</button>
                                            <span>2</span>
                                            <button type="button" aria-label="Increase Fries quantity">+</button>
                                        </div>
                                    </td>
                                    <td>₱100.00</td>
                                    <td>₱200.00</td>
                                    <td>
                                        <button type="button" class="posRemoveItemButton" aria-label="Remove Fries">
                                            <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                        </button>
                                    </td>
                                </tr>
                                <!-- Static Sample Order Item 3 -->
                                <tr>
                                    <td>
                                        <div class="posOrderProduct">
                                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Coke Mismo placeholder">
                                            <span>Coke Mismo</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="posQuantityControl">
                                            <button type="button" aria-label="Decrease Coke Mismo quantity">−</button>
                                            <span>1</span>
                                            <button type="button" aria-label="Increase Coke Mismo quantity">+</button>
                                        </div>
                                    </td>
                                    <td>₱30.00</td>
                                    <td>₱30.00</td>
                                    <td>
                                        <button type="button" class="posRemoveItemButton" aria-label="Remove Coke Mismo">
                                            <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Order Totals -->
                    <div id="posOrderSummary">
                        <div class="posOrderSummaryRow">
                            <span>Subtotal</span>
                            <span>₱425.00</span>
                        </div>
                        <div class="posOrderSummaryRow">
                            <label for="posDiscountInput">Discount</label>
                            <div id="posDiscountControl">
                                <span>−</span>
                                <input type="text" id="posDiscountInput" value="₱0.00" aria-label="Discount amount">
                            </div>
                        </div>
                        <div id="posOrderTotal">
                            <strong>Total</strong>
                            <strong>₱425.00</strong>
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
</body>
</html>
