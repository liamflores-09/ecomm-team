<?php

namespace App\Http\Controllers;

use App\Models\Sku;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SkuController extends Controller
{
    private const VARIANTS = ['Single', 'Variant/Parent', 'Variant/Child', 'Add Variant'];
    private const PR_STATUSES = ['In Progress', 'Done', 'On Hold'];
    private const REMARKS = ['No Resources', 'Out-of-Stock', 'SKU Issue', 'Posted', 'Advance PR', 'Old Posted'];

    private const PR_FIELDS = [
        'brand', 'sku', 'variant', 'pr_file_location', 'pr_assignee',
        'pr_status', 'ready_for_cvp', 'remarks', 'pr_date_started', 'pr_date_completed',
    ];
    private const CONTENT_FIELDS = ['content_assignee', 'content_date_started', 'content_date_posted'];

    private function permissions(string $role): array
    {
        $prEditors = ['researcher', 'backend', 'manager', 'head'];
        $contentEditors = ['content', 'backend', 'manager', 'head'];

        return [
            'can_create' => in_array($role, $prEditors),
            'can_edit_pr' => in_array($role, $prEditors),
            'can_edit_content' => in_array($role, $contentEditors),
        ];
    }

    private function fieldRule(string $field): string
    {
        return match ($field) {
            'brand', 'sku' => 'required|string|max:255',
            'variant' => 'nullable|in:' . implode(',', self::VARIANTS),
            'pr_file_location' => 'nullable|string',
            'pr_assignee', 'content_assignee' => 'nullable|string|max:255',
            'pr_status' => 'nullable|in:' . implode(',', self::PR_STATUSES),
            'ready_for_cvp' => 'nullable|boolean',
            'remarks' => 'nullable|in:' . implode(',', self::REMARKS),
            'pr_date_started', 'pr_date_completed', 'content_date_started', 'content_date_posted' => 'nullable|date',
        };
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $perms = $this->permissions($user->role);

        $query = Sku::query();

        if ($request->filled('brand')) {
            $query->where(function ($q) use ($request) {
                $q->where('brand', 'like', '%' . $request->query('brand') . '%')
                  ->orWhere('sku', 'like', '%' . $request->query('brand') . '%');
            });
        }
        if ($request->filled('pr_status')) {
            $query->where('pr_status', $request->query('pr_status'));
        }
        if ($request->query('posted') === '1') {
            $query->whereNotNull('content_date_posted');
        } elseif ($request->query('posted') === '0') {
            $query->whereNull('content_date_posted');
        }
        $month = $request->has('month') ? ($request->query('month') ?? '') : now()->format('Y-m');
        if ($month !== '') {
            [$year, $mon] = explode('-', $month);
            $query->where(function ($q) use ($year, $mon) {
                $q->where(fn ($q2) => $q2->whereYear('pr_date_started', $year)->whereMonth('pr_date_started', $mon))
                  ->orWhere(fn ($q2) => $q2->whereYear('content_date_started', $year)->whereMonth('content_date_started', $mon))
                  ->orWhere(fn ($q2) => $q2->whereNull('pr_date_started')->whereNull('content_date_started'));
            });
        }

        $skus = $query->orderByDesc('id')->get();

        $statsQuery = (clone $query);
        $filteredSkus = $statsQuery->get(['content_date_posted', 'pr_date_started', 'pr_date_completed', 'content_date_started']);
        $stats = [
            'total' => $filteredSkus->count(),
            'posted' => $filteredSkus->whereNotNull('content_date_posted')->count(),
            'avg_pr_sla' => round($filteredSkus->map->pr_sla->filter()->avg() ?? 0, 1),
            'avg_content_sla' => round($filteredSkus->map->content_sla->filter()->avg() ?? 0, 1),
        ];
        $hasActiveFilters = $request->filled('brand') || $request->filled('pr_status') || $request->filled('posted') || $month !== '';
        $globalTotal = $hasActiveFilters ? Sku::count() : null;
        $globalPosted = $hasActiveFilters ? Sku::whereNotNull('content_date_posted')->count() : null;

        $availableMonths = Sku::whereNotNull('pr_date_started')
            ->get(['pr_date_started'])
            ->map(fn ($sku) => $sku->pr_date_started->format('Y-m'))
            ->toBase()
            ->push(now()->format('Y-m'))
            ->unique()
            ->sortDesc()
            ->values();

        return view('sku.tracker', [
            'skus' => $skus,
            'stats' => $stats,
            'globalTotal' => $globalTotal,
            'globalPosted' => $globalPosted,
            'perms' => $perms,
            'variants' => self::VARIANTS,
            'prStatuses' => self::PR_STATUSES,
            'remarksOptions' => self::REMARKS,
            'filters' => $request->only(['brand', 'pr_status', 'posted']),
            'selectedMonth' => $month,
            'availableMonths' => $availableMonths,
            'existingSkuCodes' => Sku::pluck('sku')->map(fn ($s) => strtolower($s))->values(),
            'researchers' => User::where('role', 'researcher')->orderBy('first_name')->pluck('first_name'),
            'contentUsers' => User::where('role', 'content')->orderBy('first_name')->pluck('first_name'),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($this->permissions(Auth::user()->role)['can_create'], 403);

        $data = $request->validate([
            'brand' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
        ]);
        $data['created_by'] = Auth::id();

        Sku::create($data);

        return back()->with('success', 'Row added.');
    }

    public function bulkStore(Request $request)
    {
        abort_unless($this->permissions(Auth::user()->role)['can_create'], 403);

        $request->validate(['rows_json' => 'required|string']);
        $rows = json_decode($request->input('rows_json'), true);

        if (!is_array($rows)) {
            return back()->with('error', 'That doesn\'t look like valid JSON.');
        }

        $created = 0;
        $skipped = 0;
        foreach ($rows as $row) {
            if (!is_array($row) || empty($row['brand']) || empty($row['sku'])) {
                $skipped++;
                continue;
            }
            Sku::create([
                'brand' => (string) $row['brand'],
                'sku' => (string) $row['sku'],
                'created_by' => Auth::id(),
            ]);
            $created++;
        }

        return back()->with('success', "Added {$created} SKU(s)." . ($skipped > 0 ? " Skipped {$skipped} row(s) missing brand/sku." : ''));
    }

    public function updateField(Request $request, Sku $sku)
    {
        $perms = $this->permissions(Auth::user()->role);
        $field = $request->input('field');

        if (in_array($field, self::PR_FIELDS, true)) {
            abort_unless($perms['can_edit_pr'], 403);
        } elseif (in_array($field, self::CONTENT_FIELDS, true)) {
            abort_unless($perms['can_edit_content'], 403);
        } else {
            return response()->json(['message' => 'Unknown field.'], 422);
        }

        $validator = Validator::make(
            ['value' => $request->input('value')],
            ['value' => $this->fieldRule($field)]
        );
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $value = $validator->validated()['value'];
        if ($field === 'ready_for_cvp') {
            $value = $request->boolean('value');
        }

        $sku->update([$field => $value]);
        $sku->refresh();

        return response()->json([
            'success' => true,
            'computed' => [
                'pr_sla' => $sku->pr_sla,
                'content_sla' => $sku->content_sla,
                'content_status' => $sku->content_status,
                'posted' => $sku->posted,
            ],
        ]);
    }

    public function slaWeeklyOutput(Request $request)
    {
        $availableMonths = Sku::whereNotNull('pr_date_started')
            ->get(['pr_date_started'])
            ->map(fn ($sku) => $sku->pr_date_started->format('Y-m'))
            ->unique()
            ->sortDesc()
            ->values();

        $monthA = $request->query('month_a', $availableMonths->first());
        $monthB = $request->query('month_b', $availableMonths->get(1, $availableMonths->first()));

        $weeklyAverages = function (?string $month) {
            if (!$month) {
                return collect();
            }
            [$year, $mon] = explode('-', $month);
            return Sku::whereNotNull('pr_date_started')
                ->whereYear('pr_date_started', $year)
                ->whereMonth('pr_date_started', $mon)
                ->get()
                ->groupBy(fn ($sku) => (int) $sku->pr_date_started->format('W'))
                ->map(function ($rows, $week) {
                    return [
                        'week' => $week,
                        'avg_pr_sla' => round($rows->map->pr_sla->filter()->avg() ?? 0, 1),
                        'avg_content_sla' => round($rows->map->content_sla->filter()->avg() ?? 0, 1),
                    ];
                })
                ->sortKeys()
                ->values();
        };

        $weeksA = $weeklyAverages($monthA)->keyBy('week');
        $weeksB = $weeklyAverages($monthB)->keyBy('week');
        $allWeeks = $weeksA->keys()->merge($weeksB->keys())->unique()->sort()->values();

        $comparison = $allWeeks->map(function ($week) use ($weeksA, $weeksB) {
            $a = $weeksA->get($week, ['avg_pr_sla' => 0, 'avg_content_sla' => 0]);
            $b = $weeksB->get($week, ['avg_pr_sla' => 0, 'avg_content_sla' => 0]);
            $prChange = $a['avg_pr_sla'] > 0 ? round((($b['avg_pr_sla'] - $a['avg_pr_sla']) / $a['avg_pr_sla']) * 100, 1) : null;
            $contentChange = $a['avg_content_sla'] > 0 ? round((($b['avg_content_sla'] - $a['avg_content_sla']) / $a['avg_content_sla']) * 100, 1) : null;

            return [
                'week' => $week,
                'pr_a' => $a['avg_pr_sla'],
                'pr_b' => $b['avg_pr_sla'],
                'pr_change' => $prChange,
                'content_a' => $a['avg_content_sla'],
                'content_b' => $b['avg_content_sla'],
                'content_change' => $contentChange,
            ];
        });

        return view('sku.sla-weekly-output', [
            'availableMonths' => $availableMonths,
            'monthA' => $monthA,
            'monthB' => $monthB,
            'comparison' => $comparison,
        ]);
    }
}
