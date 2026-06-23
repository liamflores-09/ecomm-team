<?php

namespace App\Notifications;

use App\Models\BrandCatalog;
use Illuminate\Notifications\Notification;

class NewBrandCatalog extends Notification
{
    public function __construct(private BrandCatalog $catalog) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'   => 'New Brand Catalog',
            'message' => 'A new catalog was added for ' . $this->catalog->brand->name,
            'icon'    => 'fa-book-open',
            'color'   => 'success',
            'url'     => '/brand-catalogs?brand_id=' . $this->catalog->brand_id,
        ];
    }
}
