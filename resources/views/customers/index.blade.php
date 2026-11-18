
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
                    <h1>Customer List</h1>
                    <p>Manage registered customers and their information</p>
                </div>
                @include('partials.profile')
            </header>
            <!-- Customers Content -->
            <div id="customersContent">
                <!-- Search and Add Customer -->
                <section id="customersToolbar">
                    <form id="customersSearchContainer" method="GET" action="{{ route('customers.index') }}">
                        <span class="material-symbols-outlined" aria-hidden="true">search</span>
                        <input type="search" id="customersSearchInput" name="search" value="{{ request('search') }}"
                            maxlength="100" placeholder="Search customer (e.g., name, phone...)" aria-label="Search customers">
                        <button type="submit">Search</button>
                    </form>
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
                                @if($customers->isNotEmpty())
                                    @foreach($customers as $customer)
                                        <tr>
                                            <td>{{ $customer->customer_code }}</td>
                                            <td>{{ $customer->full_name }}</td>
                                            <td>{{ $customer->phone ?: '—' }}</td>
                                            <td>{{ $customer->email ?: '—' }}</td>
                                            <td>
                                                <button type="button" class="customersEditButton"
                                                    data-customer-id="{{ $customer->id }}"
                                                    data-update-url="{{ route('customers.update', $customer) }}"
                                                    data-name="{{ $customer->full_name }}"
                                                    data-phone="{{ $customer->phone }}"
                                                    data-email="{{ $customer->email }}"
                                                    aria-label="Edit {{ $customer->full_name }}">Edit</button>
                                                <form method="POST" action="{{ route('customers.destroy', $customer) }}"
                                                    style="display:inline" onsubmit="return confirm('Delete this customer?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="customersDeleteButton">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="5">No customers found.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div id="customersPagination">
                        <p id="customersPaginationInfo">Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers</p>
                        <nav id="customersPaginationControls" aria-label="Customer pagination">
                            @if ($customers->previousPageUrl()) <a href="{{ $customers->previousPageUrl() }}">Previous</a> @endif
                            @if ($customers->nextPageUrl()) <a href="{{ $customers->nextPageUrl() }}">Next</a> @endif
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
