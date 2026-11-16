
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
    @vite(['resources/css/app.css'])
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
            <nav id="dashboardSidebarNavigation" aria-label="Main navigation">
                <a href="{{ route('dashboard') }}" class="dashboardNavLink">
                    <span class="material-symbols-outlined" aria-hidden="true">dashboard</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('pos.index') }}" class="dashboardNavLink">
                    <span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span>
                    <span>Record Sale</span>
                </a>
                <a href="{{ route('products.index') }}" class="dashboardNavLink active" aria-current="page">
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
                    <h1>Product List</h1>
                    <p>Manage menu items, prices, and inventory</p>
                </div>
                <div id="dashboardHeaderProfile">
                    <span class="material-symbols-outlined" id="dashboardProfileIcon" aria-hidden="true">account_circle</span>
                    <div id="dashboardProfileDetails">
                        <h2>Ivan Mark Lizardo</h2>
                        <p>Admin</p>
                    </div>
                </div>
            </header>
            <!-- Products Content -->
            <div id="productsContent">
                <!-- Category Tabs and Add Product -->
                <section id="productsToolbar">
                    <div id="productsCategoryTabs">
                        <button type="button" class="productsCategoryTab active">
                            All Products (124)
                        </button>
                        <button type="button" class="productsCategoryTab">
                            <span class="material-symbols-outlined" aria-hidden="true">restaurant</span>
                            Food (72)
                        </button>
                        <button type="button" class="productsCategoryTab">
                            <span class="material-symbols-outlined" aria-hidden="true">local_cafe</span>
                            Beverages (40)
                        </button>
                        <button type="button" class="productsCategoryTab">
                            <span class="material-symbols-outlined" aria-hidden="true">liquor</span>
                            Alcoholic Drinks (12)
                        </button>
                    </div>
                    <button type="button" id="productsAddButton">
                        <span class="material-symbols-outlined" aria-hidden="true">add</span>
                        Add Product
                    </button>
                </section>
                <!-- Search and Filters -->
                <section id="productsFilterSection" aria-label="Product filters">
                    <div id="productsSearchContainer">
                        <span class="material-symbols-outlined" aria-hidden="true">search</span>
                        <input type="search" id="productsSearchInput" placeholder="Search product (e.g., Pork Sisig, Coke...)" aria-label="Search products">
                    </div>
                    <div id="productsCategoryFilter">
                        <label for="productsCategorySelect">Category:</label>
                        <select id="productsCategorySelect">
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
                    <div id="productsStockFilter">
                        <label for="productsStockSelect">Stock:</label>
                        <select id="productsStockSelect">
                            <option value="all">All Stock</option>
                            <option value="inStock">In Stock</option>
                            <option value="lowStock">Low Stock</option>
                            <option value="outOfStock">Out of Stock</option>
                        </select>
                    </div>
                </section>
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
                                <!-- Static Sample Product 1 -->
                                <tr>
                                    <td><input type="checkbox" aria-label="Select Mixilog"></td>
                                    <td>
                                        <div class="productsProductInfo">
                                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Mixilog placeholder" class="productsProductImage">
                                            <span>Mixilog</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="productsCategoryBadge">
                                            <span class="material-symbols-outlined" aria-hidden="true">restaurant</span>
                                            Silog Meals
                                        </span>
                                    </td>
                                    <td>₱130.00</td>
                                    <td>25</td>
                                    <td><span class="productsStatusBadge productsInStock">In Stock</span></td>
                                    <td>
                                        <div class="productsActionButtons">
                                            <button type="button" class="productsEditButton" aria-label="Edit Mixilog">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="productsDeleteButton" aria-label="Delete Mixilog">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Product 2 -->
                                <tr>
                                    <td><input type="checkbox" aria-label="Select Pork Sisig"></td>
                                    <td>
                                        <div class="productsProductInfo">
                                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Pork Sisig placeholder" class="productsProductImage">
                                            <span>Pork Sisig</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="productsCategoryBadge">
                                            <span class="material-symbols-outlined" aria-hidden="true">skillet</span>
                                            Pulutan
                                        </span>
                                    </td>
                                    <td>₱195.00</td>
                                    <td>18</td>
                                    <td><span class="productsStatusBadge productsInStock">In Stock</span></td>
                                    <td>
                                        <div class="productsActionButtons">
                                            <button type="button" class="productsEditButton" aria-label="Edit Pork Sisig">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="productsDeleteButton" aria-label="Delete Pork Sisig">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Product 3 -->
                                <tr>
                                    <td><input type="checkbox" aria-label="Select Buffalo Wings"></td>
                                    <td>
                                        <div class="productsProductInfo">
                                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Buffalo Wings placeholder" class="productsProductImage">
                                            <span>Buffalo Wings</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="productsCategoryBadge">
                                            <span class="material-symbols-outlined" aria-hidden="true">set_meal</span>
                                            Chicken Wings
                                        </span>
                                    </td>
                                    <td>₱170.00</td>
                                    <td>12</td>
                                    <td><span class="productsStatusBadge productsInStock">In Stock</span></td>
                                    <td>
                                        <div class="productsActionButtons">
                                            <button type="button" class="productsEditButton" aria-label="Edit Buffalo Wings">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="productsDeleteButton" aria-label="Delete Buffalo Wings">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Product 4 -->
                                <tr>
                                    <td><input type="checkbox" aria-label="Select Fries"></td>
                                    <td>
                                        <div class="productsProductInfo">
                                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Fries placeholder" class="productsProductImage">
                                            <span>Fries</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="productsCategoryBadge">
                                            <span class="material-symbols-outlined" aria-hidden="true">fastfood</span>
                                            Snacks &amp; Appetizers
                                        </span>
                                    </td>
                                    <td>₱100.00</td>
                                    <td>30</td>
                                    <td><span class="productsStatusBadge productsInStock">In Stock</span></td>
                                    <td>
                                        <div class="productsActionButtons">
                                            <button type="button" class="productsEditButton" aria-label="Edit Fries">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="productsDeleteButton" aria-label="Delete Fries">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Product 5 -->
                                <tr>
                                    <td><input type="checkbox" aria-label="Select Coke Mismo"></td>
                                    <td>
                                        <div class="productsProductInfo">
                                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Coke Mismo placeholder" class="productsProductImage">
                                            <span>Coke Mismo</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="productsCategoryBadge">
                                            <span class="material-symbols-outlined" aria-hidden="true">local_drink</span>
                                            Softdrinks &amp; Refreshments
                                        </span>
                                    </td>
                                    <td>₱30.00</td>
                                    <td>48</td>
                                    <td><span class="productsStatusBadge productsInStock">In Stock</span></td>
                                    <td>
                                        <div class="productsActionButtons">
                                            <button type="button" class="productsEditButton" aria-label="Edit Coke Mismo">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="productsDeleteButton" aria-label="Delete Coke Mismo">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Product 6 -->
                                <tr>
                                    <td><input type="checkbox" aria-label="Select Nutella Frappe"></td>
                                    <td>
                                        <div class="productsProductInfo">
                                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Nutella Frappe placeholder" class="productsProductImage">
                                            <span>Nutella Frappe</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="productsCategoryBadge">
                                            <span class="material-symbols-outlined" aria-hidden="true">local_cafe</span>
                                            Frappe
                                        </span>
                                    </td>
                                    <td>₱129.00</td>
                                    <td>15</td>
                                    <td><span class="productsStatusBadge productsInStock">In Stock</span></td>
                                    <td>
                                        <div class="productsActionButtons">
                                            <button type="button" class="productsEditButton" aria-label="Edit Nutella Frappe">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="productsDeleteButton" aria-label="Delete Nutella Frappe">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Product 7 -->
                                <tr>
                                    <td><input type="checkbox" aria-label="Select San Mig Light"></td>
                                    <td>
                                        <div class="productsProductInfo">
                                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="San Mig Light placeholder" class="productsProductImage">
                                            <span>San Mig Light</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="productsCategoryBadge">
                                            <span class="material-symbols-outlined" aria-hidden="true">sports_bar</span>
                                            Beer
                                        </span>
                                    </td>
                                    <td>₱95.00</td>
                                    <td>8</td>
                                    <td><span class="productsStatusBadge productsLowStock">Low Stock</span></td>
                                    <td>
                                        <div class="productsActionButtons">
                                            <button type="button" class="productsEditButton" aria-label="Edit San Mig Light">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="productsDeleteButton" aria-label="Delete San Mig Light">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Product 8 -->
                                <tr>
                                    <td><input type="checkbox" aria-label="Select Alfonso"></td>
                                    <td>
                                        <div class="productsProductInfo">
                                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Alfonso placeholder" class="productsProductImage">
                                            <span>Alfonso</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="productsCategoryBadge">
                                            <span class="material-symbols-outlined" aria-hidden="true">liquor</span>
                                            Brandy
                                        </span>
                                    </td>
                                    <td>₱600.00</td>
                                    <td>4</td>
                                    <td><span class="productsStatusBadge productsLowStock">Low Stock</span></td>
                                    <td>
                                        <div class="productsActionButtons">
                                            <button type="button" class="productsEditButton" aria-label="Edit Alfonso">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="productsDeleteButton" aria-label="Delete Alfonso">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div id="productsPagination">
                        <p id="productsPaginationInfo">Showing 1 to 8 of 124 products</p>
                        <nav id="productsPaginationControls" aria-label="Product pagination">
                            <button type="button" class="productsPaginationButton" aria-label="Previous page" disabled>
                                <span class="material-symbols-outlined" aria-hidden="true">chevron_left</span>
                            </button>
                            <button type="button" class="productsPaginationButton active" aria-current="page">1</button>
                            <button type="button" class="productsPaginationButton">2</button>
                            <button type="button" class="productsPaginationButton">3</button>
                            <span class="productsPaginationEllipsis">...</span>
                            <button type="button" class="productsPaginationButton">16</button>
                            <button type="button" class="productsPaginationButton" aria-label="Next page">
                                <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                            </button>
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
