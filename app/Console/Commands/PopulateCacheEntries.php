<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use App\Events\AttributeVerified;

class PopulateCacheEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate-cache-entries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates the cache entries for the past month';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $orders = Order::where('created_at', '>=', now()->subMonth())
            ->where(function ($query) {
                $query->orWhereNotNull('itgcontainer_dictid')
                    ->orWhereNotNull('carrier_dictid')
                    ->orWhereNotNull('tms_template_dictid')
                    ->orWhereNotNull('vessel_dictid');
            })
            ->whereNotNull('tms_shipment_id')
            ->get(['id', 'itgcontainer_dictid', 'carrier_dictid', 'vessel_dictid', 'tms_template_dictid']);

        foreach ($orders as $order) {
            $this->info("Queueing AttributeVerified events for order {$order->id}");
            AttributeVerified::dispatchIf($order->itgcontainer_dictid, $order, 'itgcontainer_dictid_verified');
            AttributeVerified::dispatchIf($order->carrier_dictid, $order, 'carrier_dictid_verified');
            AttributeVerified::dispatchIf($order->vessel_dictid, $order, 'vessel_dictid_verified');
            AttributeVerified::dispatchIf($order->tms_template_dictid, $order, 'tms_template_dictid_verified');
        }
    }
}
