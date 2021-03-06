<?php

namespace RackbeatSDK\Resources;

use RackbeatSDK\Models\Item;
use RackbeatSDK\Resources\Filters\ItemFilters;
use RackbeatSDK\Resources\Traits\CanFind;
use RackbeatSDK\Resources\Traits\CanIndex;

class ItemResource extends BaseResource
{
	use CanIndex, CanFind, ItemFilters;

	protected const MODEL         = Item::class;
	protected const RESOURCE_KEY  = 'item';
	protected const ENDPOINT_BASE = 'items';

	protected function formatKeyForRequest( $key ): string
	{
		return rawurlencode( $key );
	}
}