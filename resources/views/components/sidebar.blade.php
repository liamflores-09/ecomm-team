@props(['active' => '', 'isAdmin' => false])

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">ED</div>
        <div>
            <h5>Ecomm Dept</h5>
            <span>{{ $isAdmin ? 'Admin' : 'PR x Content' }}</span>
        </div>
    </div>

    <ul class="sidebar-nav">
        @if($isAdmin)
            <li><a href="{{ route('admin.dashboard') }}" class="{{ $active === 'admin.dashboard' ? 'active' : '' }}"><i class="fas fa-grip"></i> Dashboard</a></li>
            <li><a href="{{ route('admin.users') }}" class="{{ $active === 'admin.users' ? 'active' : '' }}"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="{{ route('admin.daily-logs') }}" class="{{ $active === 'admin.daily-logs' ? 'active' : '' }}"><i class="fas fa-clipboard-list"></i> Daily Logs</a></li>
            <li><a href="{{ route('admin.reports') }}" class="{{ $active === 'admin.reports' ? 'active' : '' }}"><i class="fas fa-chart-pie"></i> Reports</a></li>
            <li><a href="{{ route('admin.brands') }}" class="{{ $active === 'admin.brands' ? 'active' : '' }}"><i class="fas fa-tag"></i> Brands</a></li>
            <li><a href="{{ route('brand-catalogs') }}" class="{{ $active === 'brand-catalogs' ? 'active' : '' }}" style="padding-left:2.25rem;"><i class="fas fa-book-open"></i> Brand Catalogs</a></li>
            <li><a href="{{ route('calendar') }}" class="{{ $active === 'calendar' ? 'active' : '' }}"><i class="fas fa-calendar-days"></i> Calendar</a></li>
        @else
            <li><a href="{{ route('dashboard') }}" class="{{ $active === 'dashboard' ? 'active' : '' }}"><i class="fas fa-grip"></i> Dashboard</a></li>
            @if(Auth::user()->role === 'content')
            <li><a href="{{ route('posting-procedure') }}" class="{{ $active === 'posting-procedure' ? 'active' : '' }}"><i class="fas fa-list-check"></i> Posting Procedure</a></li>
            <li><a href="{{ route('ecommerce-requirements') }}" class="{{ $active === 'ecommerce-requirements' ? 'active' : '' }}"><i class="fas fa-clipboard-list"></i> Requirements</a></li>
            @endif
            <li><a href="{{ route('end-of-day') }}" class="{{ $active === 'end-of-day' ? 'active' : '' }}"><i class="fas fa-calendar-check"></i> EOD Report</a></li>
            <li><a href="{{ route('important-links') }}" class="{{ $active === 'important-links' ? 'active' : '' }}"><i class="fas fa-link"></i> Important Links</a></li>
            <li><a href="{{ route('brand-catalogs') }}" class="{{ $active === 'brand-catalogs' ? 'active' : '' }}"><i class="fas fa-book-open"></i> Brand Catalogs</a></li>

            <li style="padding: 12px 12px 4px; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--muted-foreground); pointer-events: none;">Tools</li>
            @if(Auth::user()->role === 'content')
            <li><a href="{{ route('data-gathering') }}" class="{{ $active === 'data-gathering' ? 'active' : '' }}"><i class="fas fa-folder-open"></i> Data Gathering</a></li>
            @endif
            <li><a href="{{ route('price-calculator') }}" class="{{ $active === 'price-calculator' ? 'active' : '' }}"><i class="fas fa-calculator"></i> Price Calculator</a></li>

            <li><a href="{{ route('calendar') }}" class="{{ $active === 'calendar' ? 'active' : '' }}"><i class="fas fa-calendar-days"></i> Calendar</a></li>

            <li style="height:1px;background:var(--sidebar-border);margin:6px 0;pointer-events:none;"></li>
            <li><a href="{{ route('team') }}" class="{{ $active === 'team' ? 'active' : '' }}"><i class="fas fa-users"></i> The Team</a></li>
        @endif
    </ul>

    @if($isAdmin && $active === 'admin.reports')
    <div style="padding: 0 8px; margin-top: 8px; border-top: 1px solid var(--sidebar-border); padding-top: 12px;">
        <div style="font-size: 11px; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase; letter-spacing: 0.05em; padding: 0 12px; margin-bottom: 8px;">Filter by Role</div>
        <ul class="dropdown-nav">
            <li><a href="{{ route('admin.reports') }}" class="{{ !request()->query('role') ? 'active' : '' }}">All Roles</a></li>
            <li><a href="{{ route('admin.reports', ['role' => 'content']) }}" class="{{ request()->query('role') === 'content' ? 'active' : '' }}">Content</a></li>
            <li><a href="{{ route('admin.reports', ['role' => 'researcher']) }}" class="{{ request()->query('role') === 'researcher' ? 'active' : '' }}">Researcher</a></li>
            <li><a href="{{ route('admin.reports', ['role' => 'graphics']) }}" class="{{ request()->query('role') === 'graphics' ? 'active' : '' }}">Graphics</a></li>
            <li><a href="{{ route('admin.reports', ['role' => 'backend']) }}" class="{{ request()->query('role') === 'backend' ? 'active' : '' }}">Backend</a></li>
        </ul>
    </div>
    @endif

    @if($isAdmin && $active === 'admin.daily-logs')
    <div style="padding: 0 8px; margin-top: 8px; border-top: 1px solid var(--sidebar-border); padding-top: 12px;">
        <div style="font-size: 11px; font-weight: 600; color: var(--muted-foreground); text-transform: uppercase; letter-spacing: 0.05em; padding: 0 12px; margin-bottom: 8px;">Filter by Role</div>
        <ul class="dropdown-nav">
            <li><a href="{{ route('admin.daily-logs') }}" class="{{ !request()->query('role') ? 'active' : '' }}">All Roles</a></li>
            <li><a href="{{ route('admin.daily-logs', ['role' => 'content']) }}" class="{{ request()->query('role') === 'content' ? 'active' : '' }}">Content</a></li>
            <li><a href="{{ route('admin.daily-logs', ['role' => 'researcher']) }}" class="{{ request()->query('role') === 'researcher' ? 'active' : '' }}">Researcher</a></li>
            <li><a href="{{ route('admin.daily-logs', ['role' => 'graphics']) }}" class="{{ request()->query('role') === 'graphics' ? 'active' : '' }}">Graphics</a></li>
            <li><a href="{{ route('admin.daily-logs', ['role' => 'backend']) }}" class="{{ request()->query('role') === 'backend' ? 'active' : '' }}">Backend</a></li>
        </ul>
    </div>
    @endif

</aside>
