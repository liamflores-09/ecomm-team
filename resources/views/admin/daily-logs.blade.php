@extends('layouts.app')

@section('title', 'Daily Logs — Admin Panel')
@section('has-sidebar', true)

@section('favicon')
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23DC2626' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'/></svg>">
@endsection

@section('styles')
<style>
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: var(--white);
        border-radius: 8px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.875rem;
        transition: all 0.2s;
    }

    .stat-card:hover { transform: scale(1.02); }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .stat-count { font-size: 1.5rem; font-weight: 800; line-height: 1; }
    .stat-label { font-size: 0.75rem; font-weight: 500; color: var(--gray-500); }

    .admin-divider {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 1.75rem 0 1rem;
    }

    .admin-divider .ad-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.75rem;
        flex-shrink: 0;
    }

    .admin-divider h4 { font-weight: 800; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em; margin: 0; }
    .admin-divider .ad-line { flex: 1; height: 2px; background: var(--muted); }

    .logs-card {
        background: var(--white);
        border-radius: 8px;
        overflow: hidden;
    }

    .logs-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        border-bottom: 2px solid var(--muted);
    }

    .logs-header h4 { font-weight: 800; font-size: 0.85rem; margin: 0; }

    .filter-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-select {
        height: 36px;
        padding: 0 0.75rem;
        background: var(--muted);
        border: 2px solid transparent;
        border-radius: 6px;
        font-family: 'Outfit', sans-serif;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--fg);
        outline: none;
        cursor: pointer;
        appearance: auto;
    }

    .filter-select:focus {
        border-color: var(--primary);
        background: var(--white);
    }

    .logs-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    .logs-table thead th {
        background: var(--muted);
        padding: 0.75rem 1rem;
        font-weight: 700;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--gray-500);
        text-align: left;
        white-space: nowrap;
    }

    .logs-table tbody td {
        padding: 0.75rem 1rem;
        border-top: 2px solid var(--muted);
        font-weight: 500;
    }

    .logs-table tbody tr:hover td {
        background: #F8FAFC;
    }

    .logs-table .num {
        font-weight: 700;
        text-align: center;
    }

    .empty-logs {
        text-align: center;
        padding: 3rem;
        color: var(--gray-400);
        font-weight: 500;
    }

    .empty-logs i { font-size: 1.5rem; display: block; margin-bottom: 0.5rem; color: var(--gray-300); }

    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: repeat(2, 1fr); }
        .logs-table-wrap { overflow-x: auto; }
        .logs-table { min-width: 800px; }
    }

    @media (max-width: 480px) {
        .stat-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon" style="background: #DC2626;">ED</div>
        <div>
            <h5>Ecomm Dept</h5>
            <span>Admin Panel</span>
        </div>
    </div>

    <ul class="sidebar-nav">
        <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-grip"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.users') }}"><i class="fas fa-users"></i> Manage Users</a></li>
        <li class="nav-dropdown" id="dailyLogsDropdown">
            <a href="javascript:void(0)" onclick="toggleDropdown()" class="has-submenu">
                <i class="fas fa-clipboard-list"></i> Daily Logs <i class="fas fa-chevron-down dropdown-arrow" id="dropdownArrow"></i>
            </a>
            <ul class="submenu" id="dailyLogsSubmenu" style="display: {{ request()->query('role') ? 'block' : 'none' }};">
                <li><a href="{{ route('admin.daily-logs') }}" class="{{ !request()->query('role') ? 'active' : '' }}">All Roles</a></li>
                <li><a href="{{ route('admin.daily-logs', ['role' => 'content']) }}" class="{{ request()->query('role') === 'content' ? 'active' : '' }}">Content</a></li>
                <li><a href="{{ route('admin.daily-logs', ['role' => 'lead']) }}" class="{{ request()->query('role') === 'lead' ? 'active' : '' }}">Lead</a></li>
                <li><a href="{{ route('admin.daily-logs', ['role' => 'researcher']) }}" class="{{ request()->query('role') === 'researcher' ? 'active' : '' }}">Researcher</a></li>
                <li><a href="{{ route('admin.daily-logs', ['role' => 'graphics']) }}" class="{{ request()->query('role') === 'graphics' ? 'active' : '' }}">Graphics</a></li>
                <li><a href="{{ route('admin.daily-logs', ['role' => 'backend']) }}" class="{{ request()->query('role') === 'backend' ? 'active' : '' }}">Backend</a></li>
            </ul>
        </li>
    </ul>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout"><i class="fas fa-arrow-right-from-bracket"></i> Logout</button>
        </form>
    </div>
</div>

<div class="main-content">
    <div class="top-bar anim-up">
        <div>
            <h2>Daily <span class="highlight">Logs</span></h2>
            <p>View and track team daily activity</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="stat-grid anim-up d1">
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--primary);"><i class="fas fa-clipboard-list"></i></div>
            <div>
                <div class="stat-count">{{ $totalLogs }}</div>
                <div class="stat-label">Total Logs</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #059669;"><i class="fas fa-calendar-check"></i></div>
            <div>
                <div class="stat-count">{{ $thisMonthLogs }}</div>
                <div class="stat-label">This Month</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #F59E0B;"><i class="fas fa-users"></i></div>
            <div>
                <div class="stat-count">{{ $todayLogCount }}</div>
                <div class="stat-label">Logged Today</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #8B5CF6;"><i class="fas fa-chart-line"></i></div>
            <div>
                <div class="stat-count">{{ $avgDailyTasks }}</div>
                <div class="stat-label">Avg. Tasks/Day</div>
            </div>
        </div>
    </div>

    <!-- Divider: Charts -->
    <div class="admin-divider anim-up d2">
        <div class="ad-icon" style="background: var(--primary);"><i class="fas fa-chart-bar"></i></div>
        <h4>Weekly Overview</h4>
        <div class="ad-line"></div>
    </div>

    <!-- Charts -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 0.75rem; margin-bottom: 1.5rem;" class="anim-up d2">
        <div style="background: var(--white); border-radius: 8px; padding: 1.25rem;">
            <h4 style="font-weight: 800; font-size: 0.85rem; margin-bottom: 1rem;">Daily Tasks (Last 7 Days)</h4>
            <div style="position: relative; height: 250px;">
                <canvas id="dailyTasksChart"></canvas>
            </div>
        </div>
        <div style="background: var(--white); border-radius: 8px; padding: 1.25rem;">
            <h4 style="font-weight: 800; font-size: 0.85rem; margin-bottom: 1rem;">Top Performers</h4>
            <div style="position: relative; height: 250px;">
                <canvas id="productivityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Divider: Member Status -->
    <div class="admin-divider anim-up d2b">
        <div class="ad-icon" style="background: #8B5CF6;"><i class="fas fa-users"></i></div>
        <h4>{{ $roleFilter ? ucfirst($roleFilter) . ' Team' : 'All Members' }} — {{ $memberLogStatus->count() }} total</h4>
        <div class="ad-line"></div>
    </div>

    <!-- Member Log Status Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 0.75rem; margin-bottom: 1.5rem;" class="anim-up d2b">
        @foreach($memberLogStatus as $m)
        @php
            $roleBadgeColors = [
                'content' => ['bg' => '#D1FAE5', 'text' => '#059669'],
                'lead' => ['bg' => '#FCE7F3', 'text' => '#DB2777'],
                'researcher' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
                'graphics' => ['bg' => '#DBEAFE', 'text' => '#2563EB'],
                'backend' => ['bg' => '#EDE9FE', 'text' => '#7C3AED'],
            ];
            $rbc = $roleBadgeColors[$m['user']->role] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
        @endphp
        <div style="background: var(--white); border-radius: 10px; padding: 1.25rem; border: 2px solid {{ $m['todayLog'] ? '#D1FAE5' : '#FEE2E2' }};">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ $m['user']->username }}&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" style="width: 44px; height: 44px; border-radius: 50%;" alt="">
                <div style="flex: 1;">
                    <div style="font-weight: 700; font-size: 0.95rem;">{{ $m['user']->first_name }} {{ $m['user']->last_name }}</div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem;">
                        <span style="display: inline-block; padding: 0.1rem 0.4rem; border-radius: 3px; font-size: 0.6rem; font-weight: 700; text-transform: uppercase; background: {{ $rbc['bg'] }}; color: {{ $rbc['text'] }};">{{ $m['user']->role }}</span>
                        @if($m['todayLog'])
                        <span style="display: inline-block; padding: 0.1rem 0.4rem; border-radius: 3px; font-size: 0.6rem; font-weight: 700; background: #D1FAE5; color: #059669;"><i class="fas fa-check" style="margin-right: 0.15rem;"></i>Logged</span>
                        @else
                        <span style="display: inline-block; padding: 0.1rem 0.4rem; border-radius: 3px; font-size: 0.6rem; font-weight: 700; background: #FEE2E2; color: #DC2626;"><i class="fas fa-clock" style="margin-right: 0.15rem;"></i>Not Logged</span>
                        @endif
                    </div>
                </div>
            </div>
            @if($m['todayLog'])
            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.375rem; text-align: center;">
                <div style="padding: 0.375rem; background: var(--muted); border-radius: 4px;">
                    <div style="font-size: 1rem; font-weight: 800; color: #3B82F6;">{{ $m['todayLog']->new_sku }}</div>
                    <div style="font-size: 0.55rem; font-weight: 600; color: var(--gray-400); text-transform: uppercase;">Col 1</div>
                </div>
                <div style="padding: 0.375rem; background: var(--muted); border-radius: 4px;">
                    <div style="font-size: 1rem; font-weight: 800; color: #8B5CF6;">{{ $m['todayLog']->variation_sku }}</div>
                    <div style="font-size: 0.55rem; font-weight: 600; color: var(--gray-400); text-transform: uppercase;">Col 2</div>
                </div>
                <div style="padding: 0.375rem; background: var(--muted); border-radius: 4px;">
                    <div style="font-size: 1rem; font-weight: 800; color: #059669;">{{ $m['todayLog']->advance_data_gathering }}</div>
                    <div style="font-size: 0.55rem; font-weight: 600; color: var(--gray-400); text-transform: uppercase;">Col 3</div>
                </div>
                <div style="padding: 0.375rem; background: var(--muted); border-radius: 4px;">
                    <div style="font-size: 1rem; font-weight: 800; color: #F59E0B;">{{ $m['todayLog']->update_listings }}</div>
                    <div style="font-size: 0.55rem; font-weight: 600; color: var(--gray-400); text-transform: uppercase;">Col 4</div>
                </div>
                <div style="padding: 0.375rem; background: var(--muted); border-radius: 4px;">
                    <div style="font-size: 1rem; font-weight: 800; color: #EC4899;">{{ $m['todayLog']->other_tasks }}</div>
                    <div style="font-size: 0.55rem; font-weight: 600; color: var(--gray-400); text-transform: uppercase;">Other</div>
                </div>
            </div>
            @else
            <div style="text-align: center; padding: 0.75rem; background: #FEF2F2; border-radius: 6px; color: #991B1B; font-size: 0.8rem; font-weight: 600;">
                <i class="fas fa-exclamation-circle" style="margin-right: 0.25rem;"></i>
                No log submitted today
                @if($m['lastLog'])
                <div style="font-size: 0.7rem; color: var(--gray-400); margin-top: 0.25rem; font-weight: 500;">Last logged: {{ $m['lastLog']->date->diffForHumans() }}</div>
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Divider: Today's Logs -->
    <div class="admin-divider anim-up d3">
        <div class="ad-icon" style="background: #059669;"><i class="fas fa-list-check"></i></div>
        <h4>Today's Logs — {{ now()->format('F j, Y') }}</h4>
        <div class="ad-line"></div>
    </div>

    <!-- Today's Logs Table -->
    <div class="logs-card anim-up d3">
        @if($todayLogs->count())
        <div class="logs-table-wrap">
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th style="text-align: center;">New SKU</th>
                        <th style="text-align: center;">Var. SKU</th>
                        <th style="text-align: center;">Data Gather</th>
                        <th style="text-align: center;">Update</th>
                        <th style="text-align: center;">Other</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($todayLogs as $log)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ $log->username }}&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" style="width: 28px; height: 28px; border-radius: 50%;" alt="">
                                <span style="font-weight: 600;">{{ $log->username }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $roleColors = [
                                    'manager' => ['bg' => '#FEF3C7', 'text' => '#D97706'],
                                    'lead' => ['bg' => '#FCE7F3', 'text' => '#DB2777'],
                                    'content' => ['bg' => '#D1FAE5', 'text' => '#059669'],
                                    'graphics' => ['bg' => '#DBEAFE', 'text' => '#2563EB'],
                                    'backend' => ['bg' => '#EDE9FE', 'text' => '#7C3AED'],
                                    'researcher' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
                                ];
                                $rc = $roleColors[$log->role] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
                            @endphp
                            <span style="display: inline-block; padding: 0.15rem 0.4rem; border-radius: 3px; font-size: 0.6rem; font-weight: 700; text-transform: uppercase; background: {{ $rc['bg'] }}; color: {{ $rc['text'] }};">{{ $log->role }}</span>
                        </td>
                        <td class="num">{{ $log->new_sku }}</td>
                        <td class="num">{{ $log->variation_sku }}</td>
                        <td class="num">{{ $log->advance_data_gathering }}</td>
                        <td class="num">{{ $log->update_listings }}</td>
                        <td class="num">{{ $log->other_tasks }}</td>
                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--gray-500); font-size: 0.8rem;">{{ $log->remarks ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-logs">
            <i class="fas fa-clipboard-list"></i>
            No logs submitted today yet.
        </div>
        @endif
    </div>

    <!-- Divider: Missing Logs -->
    @if($missingLogs->count())
    <div class="admin-divider anim-up d3b">
        <div class="ad-icon" style="background: #DC2626;"><i class="fas fa-exclamation-triangle"></i></div>
        <h4>Missing Logs — {{ $missingLogs->count() }} member{{ $missingLogs->count() > 1 ? 's' : '' }} pending</h4>
        <div class="ad-line"></div>
    </div>

    <div class="logs-card anim-up d3b" style="border: 2px solid #FEE2E2;">
        <div class="logs-table-wrap">
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Logged</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($missingLogs as $m)
                    @php
                        $lastLog = \App\Models\DailyLog::where('user_id', $m->id)->latest('date')->first();
                    @endphp
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ $m->username }}&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" style="width: 28px; height: 28px; border-radius: 50%;" alt="">
                                <span style="font-weight: 600;">{{ $m->first_name }} {{ $m->last_name }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $roleColors = [
                                    'manager' => ['bg' => '#FEF3C7', 'text' => '#D97706'],
                                    'lead' => ['bg' => '#FCE7F3', 'text' => '#DB2777'],
                                    'content' => ['bg' => '#D1FAE5', 'text' => '#059669'],
                                    'graphics' => ['bg' => '#DBEAFE', 'text' => '#2563EB'],
                                    'backend' => ['bg' => '#EDE9FE', 'text' => '#7C3AED'],
                                    'researcher' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
                                ];
                                $rc = $roleColors[$m->role] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
                            @endphp
                            <span style="display: inline-block; padding: 0.15rem 0.4rem; border-radius: 3px; font-size: 0.6rem; font-weight: 700; text-transform: uppercase; background: {{ $rc['bg'] }}; color: {{ $rc['text'] }};">{{ $m->role }}</span>
                        </td>
                        <td>
                            <span style="display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.65rem; font-weight: 700; background: #FEE2E2; color: #DC2626;">
                                <i class="fas fa-clock" style="margin-right: 0.25rem;"></i>Not Logged
                            </span>
                        </td>
                        <td style="color: var(--gray-500); font-size: 0.8rem;">
                            {{ $lastLog ? $lastLog->date->diffForHumans() : 'Never' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Divider: Calendar -->
    <div class="admin-divider anim-up d5">
        <div class="ad-icon" style="background: var(--primary);"><i class="fas fa-calendar"></i></div>
        <h4>Activity Calendar — {{ $calendarMonth->format('F Y') }}</h4>
        <div class="ad-line"></div>
    </div>

    <!-- Calendar -->
    <div style="background: var(--white); border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem;" class="anim-up d5">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <a href="{{ route('admin.daily-logs', array_merge(request()->query(), ['month' => $calendarMonth->copy()->subMonth()->format('Y-m')])) }}" style="text-decoration: none; color: var(--gray-500); font-weight: 600;"><i class="fas fa-chevron-left"></i></a>
            <h4 style="font-weight: 800; font-size: 1rem; margin: 0;">{{ $calendarMonth->format('F Y') }}</h4>
            <a href="{{ route('admin.daily-logs', array_merge(request()->query(), ['month' => $calendarMonth->copy()->addMonth()->format('Y-m')])) }}" style="text-decoration: none; color: var(--gray-500); font-weight: 600;"><i class="fas fa-chevron-right"></i></a>
        </div>
        <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 0.375rem;">
            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
            <div style="text-align: center; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: var(--gray-400); padding: 0.5rem 0;">{{ $dayName }}</div>
            @endforeach
            @php
                $firstDay = $calendarMonth->copy()->startOfMonth();
                $startOffset = $firstDay->dayOfWeek;
                $daysInMonth = $calendarMonth->daysInMonth;
            @endphp
            @for($i = 0; $i < $startOffset; $i++)
            <div></div>
            @endfor
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $dateStr = $calendarMonth->copy()->day($day)->format('Y-m-d');
                    $hasLogs = in_array($dateStr, $calendarDays);
                    $isToday = $dateStr === now()->format('Y-m-d');
                    $isSelected = $dateStr === $selectedDay;
                @endphp
                <a href="{{ route('admin.daily-logs', array_merge(request()->query(), ['day' => $dateStr])) }}"
                   style="display: flex; align-items: center; justify-content: center; height: 40px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; transition: all 0.15s;
                   @if($isSelected) background: var(--primary); color: white;
                   @elseif($isToday) background: #DBEAFE; color: #2563EB;
                   @elseif($hasLogs) background: #D1FAE5; color: #059669;
                   @else color: var(--gray-600);
                   @endif">
                    {{ $day }}
                </a>
            @endfor
        </div>
        <div style="display: flex; gap: 1rem; margin-top: 0.75rem; font-size: 0.7rem; color: var(--gray-400);">
            <span><span style="display: inline-block; width: 10px; height: 10px; border-radius: 3px; background: #D1FAE5; vertical-align: middle; margin-right: 0.25rem;"></span> Has logs</span>
            <span><span style="display: inline-block; width: 10px; height: 10px; border-radius: 3px; background: #DBEAFE; vertical-align: middle; margin-right: 0.25rem;"></span> Today</span>
            <span><span style="display: inline-block; width: 10px; height: 10px; border-radius: 3px; background: var(--primary); vertical-align: middle; margin-right: 0.25rem;"></span> Selected</span>
        </div>
    </div>

    <!-- Selected Day Logs -->
    @if($selectedDay)
    <div style="background: var(--white); border-radius: 8px; overflow: hidden; margin-bottom: 1.5rem; border: 2px solid var(--primary);" class="anim-up d5b">
        <div style="padding: 1rem 1.5rem; border-bottom: 2px solid var(--muted); display: flex; align-items: center; justify-content: space-between;">
            <h4 style="font-weight: 800; font-size: 0.85rem; margin: 0;"><i class="fas fa-calendar-day" style="color: var(--primary); margin-right: 0.5rem;"></i>{{ \Carbon\Carbon::parse($selectedDay)->format('l, F j, Y') }}</h4>
            <span style="font-size: 0.75rem; font-weight: 600; color: var(--gray-400);">{{ $selectedDayLogs->count() }} log{{ $selectedDayLogs->count() !== 1 ? 's' : '' }}</span>
        </div>
        @if($selectedDayLogs->count())
        <table class="logs-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th style="text-align: center;">Col 1</th>
                    <th style="text-align: center;">Col 2</th>
                    <th style="text-align: center;">Col 3</th>
                    <th style="text-align: center;">Col 4</th>
                    <th style="text-align: center;">Other</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($selectedDayLogs as $log)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ $log->username }}&backgroundColor=b6e3f4,c0aede,d1d4f9,ffd5dc,ffdfbf" style="width: 24px; height: 24px; border-radius: 50%;" alt="">
                            <span style="font-weight: 600;">{{ $log->username }}</span>
                        </div>
                    </td>
                    <td>
                        @php
                            $rc2 = $roleColors[$log->role] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
                        @endphp
                        <span style="display: inline-block; padding: 0.15rem 0.4rem; border-radius: 3px; font-size: 0.6rem; font-weight: 700; text-transform: uppercase; background: {{ $rc2['bg'] }}; color: {{ $rc2['text'] }};">{{ $log->role }}</span>
                    </td>
                    <td class="num">{{ $log->new_sku }}</td>
                    <td class="num">{{ $log->variation_sku }}</td>
                    <td class="num">{{ $log->advance_data_gathering }}</td>
                    <td class="num">{{ $log->update_listings }}</td>
                    <td class="num">{{ $log->other_tasks }}</td>
                    <td style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--gray-500); font-size: 0.8rem;">{{ $log->remarks ?: '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="text-align: center; padding: 2rem; color: var(--gray-400); font-weight: 500;">
            <i class="fas fa-clipboard-list" style="font-size: 1.25rem; display: block; margin-bottom: 0.5rem; color: var(--gray-200);"></i>
            No logs submitted on this day.
        </div>
        @endif
    </div>
    @endif

    <!-- Divider: History -->
    <div class="admin-divider anim-up d6">
        <div class="ad-icon" style="background: var(--secondary);"><i class="fas fa-history"></i></div>
        <h4>History — Last 14 Days</h4>
        <div class="ad-line"></div>
    </div>

    <!-- Day-by-Day History -->
    <div style="background: var(--white); border-radius: 8px; overflow: hidden; margin-bottom: 1.5rem;" class="anim-up d6">
        @if($historyDays->count())
        <table class="logs-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th style="text-align: center;">Members</th>
                    <th style="text-align: center;">Col 1</th>
                    <th style="text-align: center;">Col 2</th>
                    <th style="text-align: center;">Col 3</th>
                    <th style="text-align: center;">Col 4</th>
                    <th style="text-align: center;">Other</th>
                    <th style="text-align: center;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historyDays as $hd)
                <tr style="cursor: pointer;" onclick="window.location='{{ route('admin.daily-logs', array_merge(request()->query(), ['day' => $hd->date->format('Y-m-d')])) }}'">
                    <td style="font-weight: 700; white-space: nowrap;">{{ $hd->date->format('M d, Y') }}</td>
                    <td class="num">{{ $hd->user_count }}</td>
                    <td class="num">{{ $hd->total_new_sku }}</td>
                    <td class="num">{{ $hd->total_variation_sku }}</td>
                    <td class="num">{{ $hd->total_data_gathering }}</td>
                    <td class="num">{{ $hd->total_update_listings }}</td>
                    <td class="num">{{ $hd->total_other_tasks }}</td>
                    <td class="num" style="font-weight: 800;">{{ $hd->total_new_sku + $hd->total_variation_sku + $hd->total_data_gathering + $hd->total_update_listings + $hd->total_other_tasks }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="text-align: center; padding: 2rem; color: var(--gray-400); font-weight: 500;">
            <i class="fas fa-history" style="font-size: 1.25rem; display: block; margin-bottom: 0.5rem; color: var(--gray-200);"></i>
            No history yet.
        </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function filterTable() {
    var role = document.getElementById('roleFilter').value;
    var rows = document.querySelectorAll('#allLogsTable tbody tr[data-role]');
    rows.forEach(function(row) {
        if (role === 'all' || row.getAttribute('data-role') === role) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var labels = @json($chartLabels);
    var newSku = @json($chartNewSku);
    var variationSku = @json($chartVariationSku);
    var dataGathering = @json($chartDataGathering);
    var updateListings = @json($chartUpdateListings);
    var otherTasks = @json($chartOtherTasks);

    var prodLabels = @json($prodLabels);
    var prodData = @json($prodData);

    var dailyCtx = document.getElementById('dailyTasksChart');
    if (dailyCtx) {
        new Chart(dailyCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels.length ? labels : ['No Data'],
                datasets: [
                    { label: 'New SKU', data: newSku.length ? newSku : [0], backgroundColor: '#3B82F6', borderRadius: 4 },
                    { label: 'Var. SKU', data: variationSku.length ? variationSku : [0], backgroundColor: '#8B5CF6', borderRadius: 4 },
                    { label: 'Data Gather', data: dataGathering.length ? dataGathering : [0], backgroundColor: '#059669', borderRadius: 4 },
                    { label: 'Update', data: updateListings.length ? updateListings : [0], backgroundColor: '#F59E0B', borderRadius: 4 },
                    { label: 'Other', data: otherTasks.length ? otherTasks : [0], backgroundColor: '#EC4899', borderRadius: 4 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12, font: { size: 11, family: 'Outfit' } } }
                },
                scales: {
                    x: { stacked: true, grid: { display: false }, ticks: { font: { size: 11, family: 'Outfit' } } },
                    y: { stacked: true, beginAtZero: true, grid: { color: '#F1F5F9' }, ticks: { font: { size: 11, family: 'Outfit' } } }
                }
            }
        });
    }

    var prodCtx = document.getElementById('productivityChart');
    if (prodCtx) {
        var barColors = ['#3B82F6', '#8B5CF6', '#059669', '#F59E0B', '#EC4899', '#06B6D4', '#F97316', '#6366F1'];
        new Chart(prodCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: prodLabels.length ? prodLabels : ['No Data'],
                datasets: [{
                    label: 'Total Tasks',
                    data: prodData.length ? prodData : [0],
                    backgroundColor: barColors.slice(0, prodLabels.length || 1),
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#F1F5F9' }, ticks: { font: { size: 11, family: 'Outfit' } } },
                    y: { grid: { display: false }, ticks: { font: { size: 11, family: 'Outfit', weight: 600 } } }
                }
            }
        });
    }
});
</script>
<script>
function toggleDropdown() {
    var submenu = document.getElementById('dailyLogsSubmenu');
    var arrow = document.getElementById('dropdownArrow');
    if (submenu.style.display === 'none' || submenu.style.display === '') {
        submenu.style.display = 'block';
        arrow.style.transform = 'rotate(180deg)';
    } else {
        submenu.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
    }
}
</script>
<style>
.nav-dropdown .has-submenu {
    display: flex !important;
    align-items: center;
    justify-content: space-between;
}
.dropdown-arrow {
    font-size: 0.65rem;
    transition: transform 0.2s;
    margin-left: auto;
}
.submenu {
    list-style: none;
    padding: 0;
    margin: 0.25rem 0 0.5rem 1.75rem;
}
.submenu li { margin: 0.125rem 0; }
.submenu a {
    display: block;
    padding: 0.5rem 0.875rem;
    color: var(--gray-300);
    text-decoration: none;
    border-radius: 4px;
    font-weight: 500;
    font-size: 0.85rem;
    transition: all 0.15s;
}
.submenu a:hover { background: var(--gray-700); color: white; }
.submenu a.active { background: var(--primary); color: white; }
</style>
@endsection
