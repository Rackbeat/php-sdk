<?php

namespace RackbeatSDK\Models;

use RackbeatSDK\Resources\ItemLocationAvailableStockResource;

/**
 * @property string         $number
 * @property string         $name
 * @property-read \DateTime $created_at
 * @property-read \DateTime $updated_at
 */
class Lot extends Model
{
	protected string $primaryKey = 'number';
	protected string $keyType    = 'string';

	protected array $casts = [
		'number'             => 'string',
		'urlfriendly_number' => 'string',
		'metadata'           => 'object',
	];

	public function availableLocationStockReport()
	{
		return new ItemLocationAvailableStockResource( $this->urlfriendly_number );
	}
}