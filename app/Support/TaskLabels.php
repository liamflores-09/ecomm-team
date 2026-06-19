<?php

namespace App\Support;

class TaskLabels
{
    protected static array $labels = [
        'content' => [
            'col1' => 'New SKU',
            'col2' => 'Variation SKU',
            'col3' => 'Advance Data Gathering',
            'col4' => 'Update Listings',
            'col5' => 'Other Tasks',
            'desc1' => 'Parent / Single product',
            'desc2' => 'Child / Variant',
            'desc3' => 'Research completed',
            'desc4' => 'Old SKUs updated',
            'desc5' => 'Canva, etc.',
        ],
        'lead' => [
            'col1' => 'New PR SKU',
            'col2' => 'Advance PR',
            'col3' => 'PR Project',
            'col4' => 'JG Used & Trade-in',
            'col5' => 'Others',
            'desc1' => 'New product research',
            'desc2' => 'Advance product research',
            'desc3' => 'PR project tasks',
            'desc4' => 'JG trade-in tasks',
            'desc5' => 'Other tasks',
        ],
        'researcher' => [
            'col1' => 'New PR SKU',
            'col2' => 'Advance PR',
            'col3' => 'PR Project',
            'col4' => 'JG Used & Trade-in',
            'col5' => 'Others',
            'desc1' => 'New product research',
            'desc2' => 'Advance product research',
            'desc3' => 'PR project tasks',
            'desc4' => 'JG trade-in tasks',
            'desc5' => 'Other tasks',
        ],
        'graphics' => [
            'col1' => 'New CVP',
            'col2' => 'Banners',
            'col3' => 'Draft',
            'col4' => 'Update CVP',
            'col5' => 'Others',
            'desc1' => 'New content visuals',
            'desc2' => 'Banner designs',
            'desc3' => 'Draft designs',
            'desc4' => 'Updated visuals',
            'desc5' => 'Other tasks',
        ],
        'backend' => [
            'col1' => 'Bulk CP',
            'col2' => 'Bulk CVP',
            'col3' => 'Q&A Inquiries',
            'col4' => 'QC',
            'col5' => 'Others',
            'desc1' => 'Cross listing uploads',
            'desc2' => 'Content uploads',
            'desc3' => 'Q&A responses',
            'desc4' => 'Quality checks',
            'desc5' => 'Other tasks',
        ],
    ];

    public static function get(string $role): array
    {
        return self::$labels[$role] ?? self::$labels['content'];
    }
}
