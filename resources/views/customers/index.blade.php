
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer List | GG Billiards & Autocare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/customers.js'])
</head>
<body>
    <div id="customersPage">
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
                <a href="{{ route('customers.index') }}" class="dashboardNavLink active" aria-current="page">
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
                    <h1>Customer List</h1>
                    <p>Manage registered customers and their information</p>
                </div>
                <div id="dashboardHeaderProfile">
                    <span class="material-symbols-outlined" id="dashboardProfileIcon" aria-hidden="true">account_circle</span>
                    <div id="dashboardProfileDetails">
                        <h2>Ivan Mark Lizardo</h2>
                        <p>Admin</p>
                    </div>
                </div>
            </header>
            <!-- Customers Content -->
            <div id="customersContent">
                <!-- Search and Add Customer -->
                <section id="customersToolbar">
                    <div id="customersSearchContainer">
                        <span class="material-symbols-outlined" aria-hidden="true">search</span>
                        <input type="search" id="customersSearchInput" placeholder="Search customer (e.g., name, phone...)" aria-label="Search customers">
                    </div>
                    <button type="button" id="customersAddButton">
                        <span class="material-symbols-outlined" aria-hidden="true">add</span>
                        Add Customer
                    </button>
                </section>
                <!-- Customers Table -->
                <section id="customersTableSection" aria-label="Customer list">
                    <div id="customersTableContainer">
                        <table id="customersTable">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <span class="customersTableHeading">
                                            Customer ID
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">
                                        <span class="customersTableHeading">
                                            Customer Name
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">
                                        <span class="customersTableHeading">
                                            Phone Number
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">
                                        <span class="customersTableHeading">
                                            Email
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($customers))
                                    @foreach($customers as $customer)
                                        <tr><td>{{ $customer->customer_code }}</td><td>{{ $customer->full_name }}</td><td>{{ $customer->phone ?: '—' }}</td><td>{{ $customer->email ?: '—' }}</td><td><button type="button" class="customersEditButton" data-customer-id="{{ $customer->id }}" aria-label="Edit {{ $customer->full_name }}">Edit</button><form method="POST" action="{{ route('customers.destroy', $customer) }}" style="display:inline" onsubmit="return confirm('Delete this customer?')">@csrf @method('DELETE')<button type="submit" class="customersDeleteButton">Delete</button></form></td></tr>
                                    @endforeach
                                @else
                                <!-- Static Sample Customer 1 -->
                                <tr>
                                    <td>CUST-001</td>
                                    <td>Maria Santos</td>
                                    <td>0917 123 4567</td>
                                    <td>maria.santos@gmail.com</td>
                                    <td>
                                        <div class="customersActionButtons">
                                            <button type="button" class="customersEditButton" aria-label="Edit Maria Santos">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="customersDeleteButton" aria-label="Delete Maria Santos">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Customer 2 -->
                                <tr>
                                    <td>CUST-002</td>
                                    <td>Carlo Reyes</td>
                                    <td>0920 765 4321</td>
                                    <td>carlo.reyes@gmail.com</td>
                                    <td>
                                        <div class="customersActionButtons">
                                            <button type="button" class="customersEditButton" aria-label="Edit Carlo Reyes">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="customersDeleteButton" aria-label="Delete Carlo Reyes">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Customer 3 -->
                                <tr>
                                    <td>CUST-003</td>
                                    <td>Angelica Cruz</td>
                                    <td>0918 555 7890</td>
                                    <td>angelica.cruz@gmail.com</td>
                                    <td>
                                        <div class="customersActionButtons">
                                            <button type="button" class="customersEditButton" aria-label="Edit Angelica Cruz">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="customersDeleteButton" aria-label="Delete Angelica Cruz">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Customer 4 -->
                                <tr>
                                    <td>CUST-004</td>
                                    <td>Mark Villanueva</td>
                                    <td>0935 222 1111</td>
                                    <td>mark.villanueva@gmail.com</td>
                                    <td>
                                        <div class="customersActionButtons">
                                            <button type="button" class="customersEditButton" aria-label="Edit Mark Villanueva">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="customersDeleteButton" aria-label="Delete Mark Villanueva">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Customer 5 -->
                                <tr>
                                    <td>CUST-005</td>
                                    <td>Denise Flores</td>
                                    <td>0919 888 3344</td>
                                    <td>denise.flores@gmail.com</td>
                                    <td>
                                        <div class="customersActionButtons">
                                            <button type="button" class="customersEditButton" aria-label="Edit Denise Flores">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="customersDeleteButton" aria-label="Delete Denise Flores">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div id="customersPagination">
                        <p id="customersPaginationInfo">Showing 1 to 5 of 20 customers</p>
                        <nav id="customersPaginationControls" aria-label="Customer pagination">
                            <button type="button" class="customersPaginationButton" aria-label="Previous page" disabled>
                                <span class="material-symbols-outlined" aria-hidden="true">chevron_left</span>
                            </button>
                            <button type="button" class="customersPaginationButton active" aria-current="page">1</button>
                            <button type="button" class="customersPaginationButton">2</button>
                            <button type="button" class="customersPaginationButton">3</button>
                            <button type="button" class="customersPaginationButton">4</button>
                            <button type="button" class="customersPaginationButton" aria-label="Next page">
                                <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                            </button>
                        </nav>
                    </div>
                </section>
            </div>
        </main>
    </div>
    @include('customers.modals.add')
    @include('customers.modals.edit')
</body>
</html>
