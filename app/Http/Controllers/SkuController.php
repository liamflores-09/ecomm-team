<?php

namespace App\Http\Controllers;

use App\Models\Sku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkuController extends Controller
{
    private const VARIANTS = ['Single', 'Variant/Parent', 'Variant/Child', 'Add Variant'];
    private const PR_STATUSES = ['DONE', 'IN PROGRESS', 'On Hold'];

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
        if ($request->filled('month')) {
            [$year, $mon] = explode('-', $request->query('month'));
            $query->where(function ($q) use ($year, $mon) {
                $q->where(fn ($q2) => $q2->whereYear('pr_date_started', $year)->whereMonth('pr_date_started', $mon))
                  ->orWhere(fn ($q2) => $q2->whereYear('content_date_started', $year)->whereMonth('content_date_started', $mon));
            });
        }

        $skus = $query->orderByDesc('id')->paginate(25)->withQueryString();

        $allSkus = Sku::select('content_date_posted', 'pr_date_started', 'pr_date_completed', 'content_date_started')->get();
        $stats = [
            'total' => Sku::count(),
            'posted' => Sku::whereNotNull('content_date_posted')->count(),
            'avg_pr_sla' => round($allSkus->map->pr_sla->filter()->avg() ?? 0, 1),
            'avg_content_sla' => round($allSkus->map->content_sla->filter()->avg() ?? 0, 1),
        ];

        $availableMonths = Sku::whereNotNull('pr_date_started')
            ->get(['pr_date_started'])
            ->map(fn ($sku) => $sku->pr_date_started->format('Y-m'))
            ->unique()
            ->sortDesc()
            ->values();

        return view('sku.tracker', [
            'skus' => $skus,
            'stats' => $stats,
            'perms' => $perms,
            'variants' => self::VARIANTS,
            'prStatuses' => self::PR_STATUSES,
            'filters' => $request->only(['brand', 'pr_status', 'posted', 'month']),
            'availableMonths' => $availableMonths,
            'existingSkuCodes' => Sku::pluck('sku')->map(fn ($s) => strtolower($s))->values(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($this->permissions(Auth::user()->role)['can_create'], 403);

        $data = $request->validate([
            'brand' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
            'variant' => 'nullable|in:' . implode(',', self::VARIANTS),
            'pr_file_location' => 'nullable|string',
            'pr_assignee' => 'nullable|string|max:255',
            'pr_status' => 'nullable|in:' . implode(',', self::PR_STATUSES),
            'ready_for_cvp' => 'nullable|boolean',
            'remarks' => 'nullable|string',
            'pr_date_started' => 'nullable|date',
            'pr_date_completed' => 'nullable|date|after_or_equal:pr_date_started',
        ]);
        $data['created_by'] = Auth::id();

        Sku::create($data);

        return back()->with('success', 'SKU added.');
    }

    public function update(Request $request, Sku $sku)
    {
        $perms = $this->permissions(Auth::user()->role);
        abort_unless($perms['can_edit_pr'] || $perms['can_edit_content'], 403);

        $rules = [];
        if ($perms['can_edit_pr']) {
            $rules += [
                'brand' => 'required|string|max:255',
                'sku' => 'required|string|max:255',
                'variant' => 'nullable|in:' . implode(',', self::VARIANTS),
                'pr_file_location' => 'nullable|string',
                'pr_assignee' => 'nullable|string|max:255',
                'pr_status' => 'nullable|in:' . implode(',', self::PR_STATUSES),
                'ready_for_cvp' => 'nullable|boolean',
                'remarks' => 'nullable|string',
                'pr_date_started' => 'nullable|date',
                'pr_date_completed' => 'nullable|date|after_or_equal:pr_date_started',
            ];
        }
        if ($perms['can_edit_content']) {
            $rules += [
                'content_assignee' => 'nullable|string|max:255',
                'content_date_started' => 'nullable|date',
                'content_date_posted' => 'nullable|date|after_or_equal:content_date_started',
                'cvp_uploaded' => 'nullable|boolean',
                'shopee_link' => 'nullable|string|max:2000',
                'lazada_link' => 'nullable|string|max:2000',
                'tiktok_link' => 'nullable|string|max:2000',
                'jg_pro_shopee_link' => 'nullable|string|max:2000',
                'jg_pro_lazada_link' => 'nullable|string|max:2000',
                'shopify_link' => 'nullable|string|max:2000',
                'cinepro_link' => 'nullable|string|max:2000',
                'lzd_brand_mall_link' => 'nullable|string|max:2000',
                'shp_brand_mall_link' => 'nullable|string|max:2000',
                'tt_brand_mall_link' => 'nullable|string|max:2000',
            ];
        }

        $data = $request->validate($rules);
        if (array_key_exists('ready_for_cvp', $rules)) {
            $data['ready_for_cvp'] = $request->boolean('ready_for_cvp');
        }
        if (array_key_exists('cvp_uploaded', $rules)) {
            $data['cvp_uploaded'] = $request->boolean('cvp_uploaded');
        }

        $sku->update($data);

        return back()->with('success', 'SKU updated.');
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
