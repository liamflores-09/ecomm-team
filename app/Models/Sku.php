<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
    protected $fillable = [
        'brand', 'sku', 'variant',
        'pr_file_location', 'pr_assignee', 'pr_status', 'ready_for_cvp', 'remarks',
        'pr_date_started', 'pr_date_completed',
        'content_assignee', 'content_date_started', 'content_date_posted', 'cvp_uploaded',
        'shopee_link', 'lazada_link', 'tiktok_link',
        'jg_pro_shopee_link', 'jg_pro_lazada_link', 'shopify_link', 'cinepro_link',
        'lzd_brand_mall_link', 'shp_brand_mall_link', 'tt_brand_mall_link',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'ready_for_cvp' => 'boolean',
            'cvp_uploaded' => 'boolean',
            'pr_date_started' => 'date',
            'pr_date_completed' => 'date',
            'content_date_started' => 'date',
            'content_date_posted' => 'date',
        ];
    }

    public function getPrSlaAttribute(): ?int
    {
        if (!$this->pr_date_started || !$this->pr_date_completed) {
            return null;
        }
        $diff = (int) $this->pr_date_started->diffInDays($this->pr_date_completed);
        return max(1, $diff);
    }

    public function getContentSlaAttribute(): ?int
    {
        if (!$this->pr_date_completed || !$this->content_date_posted) {
            return null;
        }
        $diff = (int) $this->pr_date_completed->diffInDays($this->content_date_posted);
        return max(1, $diff);
    }

    public function getContentStatusAttribute(): string
    {
        if ($this->content_date_posted) {
            return 'DONE';
        }
        if ($this->content_date_started) {
            return 'PENDING';
        }
        return '—';
    }

    public function getPostedAttribute(): bool
    {
        return $this->content_date_posted !== null;
    }
}
