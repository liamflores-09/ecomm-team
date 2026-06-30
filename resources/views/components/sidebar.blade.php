@props(['active' => '', 'isAdmin' => false])

@php
    $role = Auth::user()->role ?? '';
    if ($isPreview) {
        $role = $previewRole;
        $isAdmin = false;
    }
@endphp

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">ED</div>
        <div>
            <h5>Ecomm Dept</h5>
            <span>{{ $isPreview ? 'Previewing: ' . ucfirst($previewRole) : ($isAdmin ? 'Admin Panel' : 'PR x Content') }}</span>
        </div>
    </div>

    <ul class="sidebar-nav">
        @if($isAdmin)
            {{-- ── Top ── --}}
            <li><a href="{{ route('admin.dashboard') }}"  class="{{ $active === 'admin.dashboard'  ? 'active' : '' }}"><i class="fas fa-table-cells-large"></i> Dashboard</a></li>
            <li><a href="{{ route('admin.users') }}"      class="{{ $active === 'admin.users'      ? 'active' : '' }}"><i class="fas fa-user-group"></i> Users</a></li>

            {{-- ── Analytics ── --}}
            <li style="height:1px;background:var(--sidebar-border);margin:6px 0;pointer-events:none;"></li>
            <li style="padding:12px 12px 4px;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--muted-foreground);pointer-events:none;">Analytics</li>
            <li><a href="{{ route('admin.daily-logs') }}" class="{{ $active === 'admin.daily-logs' ? 'active' : '' }}"><i class="fas fa-clock-rotate-left"></i> Daily Logs</a></li>
            <li><a href="{{ route('admin.reports') }}"    class="{{ $active === 'admin.reports'    ? 'active' : '' }}"><i class="fas fa-chart-column"></i> Reports</a></li>
            <li><a href="{{ route('admin.attendance') }}" class="{{ $active === 'admin.attendance' ? 'active' : '' }}"><i class="fas fa-calendar-check"></i> Attendance</a></li>

            {{-- ── Brand Management ── --}}
            <li style="height:1px;background:var(--sidebar-border);margin:6px 0;pointer-events:none;"></li>
            <li style="padding:12px 12px 4px;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--muted-foreground);pointer-events:none;">Brand Management</li>
            <li><a href="{{ route('admin.brands') }}"     class="{{ $active === 'admin.brands'    ? 'active' : '' }}"><i class="fas fa-layer-group"></i> Brands</a></li>
            <li><a href="{{ route('brand-catalogs') }}"   class="{{ $active === 'brand-catalogs'  ? 'active' : '' }}"><i class="fas fa-book-open"></i> Brand Catalogs</a></li>

            {{-- ── General ── --}}
            <li style="height:1px;background:var(--sidebar-border);margin:6px 0;pointer-events:none;"></li>
            <li style="padding:12px 12px 4px;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--muted-foreground);pointer-events:none;">General</li>
            <li><a href="{{ route('announcements') }}"    class="{{ $active === 'announcements'   ? 'active' : '' }}"><i class="fas fa-bullhorn"></i> Announcements</a></li>
            <li><a href="{{ route('calendar') }}"         class="{{ $active === 'calendar'        ? 'active' : '' }}"><i class="fas fa-calendar-days"></i> Calendar</a></li>
            <li><a href="{{ route('team') }}"             class="{{ $active === 'team'            ? 'active' : '' }}"><i class="fas fa-people-group"></i> The Team</a></li>

            {{-- ── Utility ── --}}
            <li style="height:1px;background:var(--sidebar-border);margin:6px 0;pointer-events:none;"></li>
            <li><a href="#" onclick="openModal('rolePickerModal');return false;" class="{{ $active === 'dashboard' ? 'active' : '' }}"><i class="fas fa-arrow-right-from-bracket"></i> Member View</a></li>

        @else
            {{-- ── Dashboard ── --}}
            <li><a href="{{ route('dashboard') }}" class="{{ $active === 'dashboard' ? 'active' : '' }}"><i class="fas fa-table-cells-large"></i> Dashboard</a></li>

            {{-- ── Work (non-analyst) ── --}}
            @if($role !== 'analyst')
            <li style="height:1px;background:var(--sidebar-border);margin:6px 0;pointer-events:none;"></li>
            <li style="padding:12px 12px 4px;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--muted-foreground);pointer-events:none;">Work</li>
            <li><a href="{{ route('end-of-day') }}"              class="{{ $active === 'end-of-day'             ? 'active' : '' }}"><i class="fas fa-calendar-check"></i> EOD Report</a></li>
            @if($role === 'content')
            <li><a href="{{ route('posting-procedure') }}"       class="{{ $active === 'posting-procedure'      ? 'active' : '' }}"><i class="fas fa-list-check"></i> Posting Procedure</a></li>
            <li><a href="{{ route('ecommerce-requirements') }}"  class="{{ $active === 'ecommerce-requirements' ? 'active' : '' }}"><i class="fas fa-clipboard-list"></i> Requirements</a></li>
            @endif
            @endif

            {{-- ── Tools (non-analyst) ── --}}
            @if($role !== 'analyst')
            <li style="height:1px;background:var(--sidebar-border);margin:6px 0;pointer-events:none;"></li>
            <li style="padding:12px 12px 4px;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--muted-foreground);pointer-events:none;">Tools</li>
            @if($role === 'content')
            <li><a href="{{ route('data-gathering') }}"   class="{{ $active === 'data-gathering'  ? 'active' : '' }}"><i class="fas fa-magnifying-glass-chart"></i> Data Gathering</a></li>
            @endif
            <li><a href="{{ route('price-calculator') }}" class="{{ $active === 'price-calculator' ? 'active' : '' }}"><i class="fas fa-calculator"></i> Price Calculator</a></li>
            @endif

            {{-- ── Resources (non-analyst) ── --}}
            @if($role !== 'analyst')
            <li style="height:1px;background:var(--sidebar-border);margin:6px 0;pointer-events:none;"></li>
            <li style="padding:12px 12px 4px;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--muted-foreground);pointer-events:none;">Resources</li>
            <li><a href="{{ route('important-links') }}"  class="{{ $active === 'important-links' ? 'active' : '' }}"><i class="fas fa-bookmark"></i> Important Links</a></li>
            @endif

            {{-- ── Team ── --}}
            <li style="height:1px;background:var(--sidebar-border);margin:6px 0;pointer-events:none;"></li>
            <li style="padding:12px 12px 4px;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--muted-foreground);pointer-events:none;">Team</li>
            <li><a href="{{ route('announcements') }}"    class="{{ $active === 'announcements'   ? 'active' : '' }}"><i class="fas fa-bullhorn"></i> Announcements</a></li>
            <li><a href="{{ route('brand-catalogs') }}"   class="{{ $active === 'brand-catalogs'  ? 'active' : '' }}"><i class="fas fa-book-open"></i> Brand Catalogs</a></li>
            @if($role !== 'analyst')
            <li><a href="{{ route('calendar') }}"         class="{{ $active === 'calendar'         ? 'active' : '' }}"><i class="fas fa-calendar-days"></i> Calendar</a></li>
            @endif
            <li><a href="{{ route('team') }}"             class="{{ $active === 'team'            ? 'active' : '' }}"><i class="fas fa-people-group"></i> The Team</a></li>
        @endif
    </ul>

    @if($isAdmin && $active === 'admin.reports')
    <div style="padding:0 8px;margin-top:8px;border-top:1px solid var(--sidebar-border);padding-top:12px;">
        <div style="font-size:11px;font-weight:600;color:var(--muted-foreground);text-transform:uppercase;letter-spacing:0.05em;padding:0 12px;margin-bottom:8px;">Filter by Role</div>
        <ul class="dropdown-nav">
            <li><a href="{{ route('admin.reports') }}"                              class="{{ !request()->query('role') ? 'active' : '' }}">All Roles</a></li>
            <li><a href="{{ route('admin.reports', ['role' => 'content']) }}"       class="{{ request()->query('role') === 'content'    ? 'active' : '' }}">Content</a></li>
            <li><a href="{{ route('admin.reports', ['role' => 'researcher']) }}"    class="{{ request()->query('role') === 'researcher' ? 'active' : '' }}">Researcher</a></li>
            <li><a href="{{ route('admin.reports', ['role' => 'graphics']) }}"      class="{{ request()->query('role') === 'graphics'   ? 'active' : '' }}">Graphics</a></li>
            <li><a href="{{ route('admin.reports', ['role' => 'backend']) }}"       class="{{ request()->query('role') === 'backend'    ? 'active' : '' }}">Backend</a></li>
        </ul>
    </div>
    @endif

    @if($isAdmin && $active === 'admin.daily-logs')
    <div style="padding:0 8px;margin-top:8px;border-top:1px solid var(--sidebar-border);padding-top:12px;">
        <div style="font-size:11px;font-weight:600;color:var(--muted-foreground);text-transform:uppercase;letter-spacing:0.05em;padding:0 12px;margin-bottom:8px;">Filter by Role</div>
        <ul class="dropdown-nav">
            <li><a href="{{ route('admin.daily-logs') }}"                              class="{{ !request()->query('role') ? 'active' : '' }}">All Roles</a></li>
            <li><a href="{{ route('admin.daily-logs', ['role' => 'content']) }}"       class="{{ request()->query('role') === 'content'    ? 'active' : '' }}">Content</a></li>
            <li><a href="{{ route('admin.daily-logs', ['role' => 'researcher']) }}"    class="{{ request()->query('role') === 'researcher' ? 'active' : '' }}">Researcher</a></li>
            <li><a href="{{ route('admin.daily-logs', ['role' => 'graphics']) }}"      class="{{ request()->query('role') === 'graphics'   ? 'active' : '' }}">Graphics</a></li>
            <li><a href="{{ route('admin.daily-logs', ['role' => 'backend']) }}"       class="{{ request()->query('role') === 'backend'    ? 'active' : '' }}">Backend</a></li>
        </ul>
    </div>
    @endif

</aside>
