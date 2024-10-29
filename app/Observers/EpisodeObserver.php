<?php

namespace App\Observers;

use App\Models\CustomerNotification;
use App\Models\Episode;
use App\Models\Wishlist;

class EpisodeObserver
{
    /**
     * Handle the "created" event for the Episode model.
     */
    public function created(Episode $episode)
    {
        $this->notifyCustomers($episode, 'created');
    }

    /**
     * Handle the "updated" event for the Episode model.
     */
    public function updated(Episode $episode)
    {
        $this->notifyCustomers($episode, 'updated');
    }

    /**
     * Handle the "deleted" event for the Episode model.
     */
    public function deleted(Episode $episode)
    {
        $this->notifyCustomers($episode, 'deleted');
    }

    /**
     * Notify customers based on Wishlist entries.
     */
    protected function notifyCustomers(Episode $episode, $type)
    {
        $product = $episode->product;  // Ensure Episode has a 'product' relationship.
        $link = null;
        $message = '';

        // Customize the message based on the event type.
        switch ($type) {
            case 'created':
                $message = "{$product->name} has a new chapter: {$episode->name}";
                $link = "{$product->slug}/{$episode->slug}";
                break;

            case 'updated':
                $message = "{$product->name} updated chapter: {$episode->name}";
                $link = "{$product->slug}/{$episode->slug}";
                break;

            case 'deleted':
                $message = "{$product->name} deleted: {$episode->name}";
                break;

            default:
                $message = 'Product Update';
                break;
        }

        // Find all wishlist entries with the matching product_id.
        $wishlists = Wishlist::where('product_id', $product->id)->get();

        // Create notifications for each customer.
        foreach ($wishlists as $wishlist) {
            CustomerNotification::create([
                'customer_id' => $wishlist->customer_id,
                'message' => $message,
                'link' => $link,
            ]);
        }
    }
}
