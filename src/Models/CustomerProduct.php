<?php

namespace RackbeatSDK\Models;

use RackbeatSDK\Models\Objects\PhysicalObject;
use RackbeatSDK\Resources\CustomerProductResource;
use RackbeatSDK\Resources\ItemLocationAvailableStockResource;

/**
 * @property string            $name
 * @property string            $type
 * @property string            $number
 * @property string            $urlfriendly_number
 * @property float|double      $sales_price
 * @property float|double      $regular_sales_price
 * @property boolean           $is_custom_sales_price
 * @property float|double      $discount_percentage
 * @property string            $price_source
 *
 * @property null|ProductGroup $group (optionally returned)
 */
class CustomerProduct extends Model
{
	protected static string $RESOURCE = CustomerProductResource::class;

	protected array $casts = [
		'name'                  => 'string',
		'type'                  => 'string',
		'number'                => 'string',
		'urlfriendly_number'    => 'string',
		'sales_price'           => 'double',
		'regular_sales_price'   => 'double',
		'is_custom_sales_price' => 'boolean',
		'discount_percentage'   => 'double',
		'price_source'          => 'string',

		// Extra attributes optionally returned
		'group'                 => ProductGroup::class,
		'physical'              => PhysicalObject::class,
	];

	public function getMetadataAttribute( array $value )
	{
		return array_map( function ( $metaObject ) {
			return (object) $metaObject;
		}, $value );
	}

	public function availableLocationStockReport()
	{
		return new ItemLocationAvailableStockResource( $this->urlfriendly_number );
	}
}