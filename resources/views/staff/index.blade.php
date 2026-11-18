
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
                    <h1>Staff List</h1>
                    <p>Manage System staff accounts and their information</p>
                </div>
                @include('partials.profile')
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
                        <input type="search" id="staffSearchInput" name="search" value="{{ request('search') }}"
                            maxlength="100" placeholder="Search staff (e.g., name, username...)" aria-label="Search staff">
                        <button type="submit">Search</button>
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
                                @if($staff->isNotEmpty())
                                    @foreach($staff as $member)
                                        <tr>
                                            <td>
                                                <span class="staffAvatar">
                                                    @if($member->avatar)
                                                        <img src="{{ asset('storage/'.$member->avatar) }}" alt="{{ $member->full_name }}">
                                                    @else
                                                        <span class="material-symbols-outlined" aria-hidden="true">person</span>
                                                    @endif
                                                </span>
                                            </td>
                                            <td>{{ $member->staff_code }}</td>
                                            <td>{{ $member->full_name }}</td>
                                            <td>{{ $member->username }}</td>
                                            <td>{{ $member->role?->name }}</td>
                                            <td>{{ optional($member->date_joined)->format('M d, Y') }}</td>
                                            <td>
                                                <button type="button" class="staffEditButton"
                                                    data-user-id="{{ $member->id }}"
                                                    data-update-url="{{ route('staff.update', $member) }}"
                                                    data-role-key="{{ $member->role?->key }}"
                                                    aria-label="Edit {{ $member->full_name }}">Edit</button>
                                                <form method="POST" action="{{ route('staff.destroy', $member) }}"
                                                    style="display:inline" onsubmit="return confirm('Deactivate this account?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="staffDeleteButton">Deactivate</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="7">No staff found.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div id="staffPagination">
                        <p id="staffPaginationInfo">Showing {{ $staff->firstItem() ?? 0 }} to {{ $staff->lastItem() ?? 0 }} of {{ $staff->total() }} staff</p>
                        <nav id="staffPaginationControls" aria-label="Staff pagination">
                            @if ($staff->previousPageUrl()) <a href="{{ $staff->previousPageUrl() }}">Previous</a> @endif
                            @if ($staff->nextPageUrl()) <a href="{{ $staff->nextPageUrl() }}">Next</a> @endif
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
