
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff List | GG Billiards & Autocare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/staff.js'])
</head>
<body>
    <div id="staffPage">
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
                <a href="{{ route('staff.index') }}" class="dashboardNavLink active" aria-current="page">
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
                    <h1>Staff List</h1>
                    <p>Manage System staff accounts and their information</p>
                </div>
                <div id="dashboardHeaderProfile">
                    <span class="material-symbols-outlined" id="dashboardProfileIcon" aria-hidden="true">account_circle</span>
                    <div id="dashboardProfileDetails">
                        <h2>Ivan Mark Lizardo</h2>
                        <p>Admin</p>
                    </div>
                </div>
            </header>
            <!-- Staff Content -->
            <div id="staffContent">
                @if ($errors->any())
                    <div role="alert">{{ $errors->first() }}</div>
                @endif
                @if (session('status'))
                    <div role="status">{{ session('status') }}</div>
                @endif
                <!-- Search and Add Staff -->
                <section id="staffToolbar">
                    <form id="staffSearchForm" method="GET" action="{{ route('staff.index') }}"><div id="staffSearchContainer">
                        <span class="material-symbols-outlined" aria-hidden="true">search</span>
                        <input type="search" id="staffSearchInput" name="search" value="{{ request('search') }}" placeholder="Search staff (e.g., name, username...)" aria-label="Search staff">
                    </div></form>
                    <button type="button" id="staffAddButton">
                        <span class="material-symbols-outlined" aria-hidden="true">add</span>
                        Add Staff
                    </button>
                </section>
                <!-- Staff Table -->
                <section id="staffTableSection" aria-label="Staff list">
                    <div id="staffTableContainer">
                        <table id="staffTable">
                            <thead>
                                <tr>
                                    <th scope="col">Photo</th>
                                    <th scope="col">
                                        <span class="staffTableHeading">
                                            Staff ID
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">
                                        <span class="staffTableHeading">
                                            Full Name
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">
                                        <span class="staffTableHeading">
                                            Username
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">
                                        <span class="staffTableHeading">
                                            Role
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">
                                        <span class="staffTableHeading">
                                            Date Joined
                                            <span class="material-symbols-outlined" aria-hidden="true">swap_vert</span>
                                        </span>
                                    </th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($staff))
                                    @foreach($staff as $member)
                                        <tr><td><span class="staffAvatar">@if($member->avatar)<img src="{{ asset('storage/'.$member->avatar) }}" alt="{{ $member->full_name }}">@else<span class="material-symbols-outlined" aria-hidden="true">person</span>@endif</span></td><td>{{ $member->staff_code }}</td><td>{{ $member->full_name }}</td><td>{{ $member->username }}</td><td>{{ $member->role?->name }}</td><td>{{ optional($member->date_joined)->format('M d, Y') }}</td><td><button type="button" class="staffEditButton" data-user-id="{{ $member->id }}" data-role-key="{{ $member->role?->key }}" aria-label="Edit {{ $member->full_name }}">Edit</button><form method="POST" action="{{ route('staff.destroy', $member) }}" style="display:inline" onsubmit="return confirm('Deactivate this account?')">@csrf @method('DELETE')<button type="submit" class="staffDeleteButton">Deactivate</button></form></td></tr>
                                    @endforeach
                                @else
                                <!-- Static Sample Staff 1 -->
                                <tr>
                                    <td>
                                        <span class="staffAvatar">
                                            <span class="material-symbols-outlined" aria-hidden="true">person</span>
                                        </span>
                                    </td>
                                    <td>STF-001</td>
                                    <td>Juan Dela Cruz</td>
                                    <td>juan.delacruz</td>
                                    <td>Cashier</td>
                                    <td>Jan 15, 2025</td>
                                    <td>
                                        <div class="staffActionButtons">
                                            <button type="button" class="staffEditButton" aria-label="Edit Juan Dela Cruz">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="staffDeleteButton" aria-label="Delete Juan Dela Cruz">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Staff 2 -->
                                <tr>
                                    <td>
                                        <span class="staffAvatar">
                                            <span class="material-symbols-outlined" aria-hidden="true">person</span>
                                        </span>
                                    </td>
                                    <td>STF-002</td>
                                    <td>Maria Santos</td>
                                    <td>maria.santos</td>
                                    <td>Cashier</td>
                                    <td>Jan 16, 2025</td>
                                    <td>
                                        <div class="staffActionButtons">
                                            <button type="button" class="staffEditButton" aria-label="Edit Maria Santos">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="staffDeleteButton" aria-label="Delete Maria Santos">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Staff 3 -->
                                <tr>
                                    <td>
                                        <span class="staffAvatar">
                                            <span class="material-symbols-outlined" aria-hidden="true">person</span>
                                        </span>
                                    </td>
                                    <td>STF-003</td>
                                    <td>Carlo Reyes</td>
                                    <td>carlo.reyes</td>
                                    <td>Admin</td>
                                    <td>Jan 10, 2025</td>
                                    <td>
                                        <div class="staffActionButtons">
                                            <button type="button" class="staffEditButton" aria-label="Edit Carlo Reyes">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="staffDeleteButton" aria-label="Delete Carlo Reyes">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Staff 4 -->
                                <tr>
                                    <td>
                                        <span class="staffAvatar">
                                            <span class="material-symbols-outlined" aria-hidden="true">person</span>
                                        </span>
                                    </td>
                                    <td>STF-004</td>
                                    <td>Angelica Cruz</td>
                                    <td>angelica.cruz</td>
                                    <td>Cashier</td>
                                    <td>Jan 18, 2025</td>
                                    <td>
                                        <div class="staffActionButtons">
                                            <button type="button" class="staffEditButton" aria-label="Edit Angelica Cruz">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="staffDeleteButton" aria-label="Delete Angelica Cruz">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Static Sample Staff 5 -->
                                <tr>
                                    <td>
                                        <span class="staffAvatar">
                                            <span class="material-symbols-outlined" aria-hidden="true">person</span>
                                        </span>
                                    </td>
                                    <td>STF-005</td>
                                    <td>Mark Villanueva</td>
                                    <td>mark.villanueva</td>
                                    <td>Staff</td>
                                    <td>Jan 20, 2025</td>
                                    <td>
                                        <div class="staffActionButtons">
                                            <button type="button" class="staffEditButton" aria-label="Edit Mark Villanueva">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="staffDeleteButton" aria-label="Delete Mark Villanueva">
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
                    <div id="staffPagination">
                        <p id="staffPaginationInfo">Showing 1 to 5 of 12 staff</p>
                        <nav id="staffPaginationControls" aria-label="Staff pagination">
                            <button type="button" class="staffPaginationButton" aria-label="Previous page" disabled>
                                <span class="material-symbols-outlined" aria-hidden="true">chevron_left</span>
                            </button>
                            <button type="button" class="staffPaginationButton active" aria-current="page">1</button>
                            <button type="button" class="staffPaginationButton">2</button>
                            <button type="button" class="staffPaginationButton">3</button>
                            <button type="button" class="staffPaginationButton" aria-label="Next page">
                                <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                            </button>
                        </nav>
                    </div>
                </section>
            </div>
        </main>
    </div>
    @include('staff.modals.add')
    @include('staff.modals.edit')
</body>
</html>
