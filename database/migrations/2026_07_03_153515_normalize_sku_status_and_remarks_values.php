<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PR_STATUS_MAP = [
        'DONE' => 'Done',
        'IN PROGRESS' => 'In Progress',
    ];

    private const REMARKS_MAP = [
        'NO RESOURCES' => 'No Resources',
        'OOS' => 'Out-of-Stock',
        'SKU ISSUE' => 'SKU Issue',
        'POSTED' => 'Posted',
        'OLD POSTED' => 'Old Posted',
    ];

    public function up(): void
    {
        foreach (self::PR_STATUS_MAP as $old => $new) {
            DB::table('skus')->where('pr_status', $old)->update(['pr_status' => $new]);
        }
        foreach (self::REMARKS_MAP as $old => $new) {
            DB::table('skus')->where('remarks', $old)->update(['remarks' => $new]);
        }
    }

    public function down(): void
    {
        foreach (self::PR_STATUS_MAP as $old => $new) {
            DB::table('skus')->where('pr_status', $new)->update(['pr_status' => $old]);
        }
        foreach (self::REMARKS_MAP as $old => $new) {
            DB::table('skus')->where('remarks', $new)->update(['remarks' => $old]);
        }
    }
};
