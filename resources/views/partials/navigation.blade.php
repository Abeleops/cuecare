<nav id="dashboardSidebarNavigation" aria-label="Main navigation">
    {{-- Show each module only to users with permission --}}
    @can('DashboardView')
        <a href="{{ route('dashboard') }}" class="dashboardNavLink {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="material-symbols-outlined" aria-hidden="true">dashboard</span><span>Dashboard</span>
        </a>
    @endcan
    @can('PosAccess')
        <a href="{{ route('pos.index') }}" class="dashboardNavLink {{ request()->routeIs('pos.*') ? 'active' : '' }}">
            <span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span><span>Record Sale</span>
        </a>
    @endcan
    @can('ProductsView')
        <a href="{{ route('products.index') }}" class="dashboardNavLink {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <span class="material-symbols-outlined" aria-hidden="true">deployed_code</span><span>Products</span>
        </a>
    @endcan
    @can('CustomersView')
        <a href="{{ route('customers.index') }}" class="dashboardNavLink {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <span class="material-symbols-outlined" aria-hidden="true">groups</span><span>Customers</span>
        </a>
    @endcan
    @can('StaffView')
        <a href="{{ route('staff.index') }}" class="dashboardNavLink {{ request()->routeIs('staff.*') ? 'active' : '' }}">
            <span class="material-symbols-outlined" aria-hidden="true">account_box</span><span>Staff</span>
        </a>
    @endcan
    @canany(['SalesViewAll', 'SalesViewOwn'])
        <a href="{{ route('sales.index') }}" class="dashboardNavLink {{ request()->routeIs('sales.*') ? 'active' : '' }}">
            <span class="material-symbols-outlined" aria-hidden="true">receipt_long</span><span>Sales History</span>
        </a>
    @endcanany
    @can('ReportsView')
        <a href="{{ route('reports.index') }}" class="dashboardNavLink {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <span class="material-symbols-outlined" aria-hidden="true">monitoring</span><span>Reports</span>
        </a>
    @endcan
</nav>
