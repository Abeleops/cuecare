<div id="dashboardHeaderProfile">
    <span class="material-symbols-outlined" id="dashboardProfileIcon" aria-hidden="true">account_circle</span>
    <div id="dashboardProfileDetails">
        <h2>{{ auth()->user()->full_name }}</h2>
        <p>{{ auth()->user()->role?->name }}</p>
    </div>
</div>
