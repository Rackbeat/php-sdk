<?php

namespace RackbeatSDK\Resources;

use Illuminate\Support\Str;
use RackbeatSDK\API;
use RackbeatSDK\Http\Responses\IndexResponse;
use RackbeatSDK\Http\Responses\PaginatedIndexResponse;

class BaseResource
{
	/**
	 * Array of filters
	 *
	 * @var array
	 */
	protected array $wheres = [];

	/** @var string */
	protected const ENDPOINT_BASE = '/';

	/** @var string */
	protected const RESOURCE_KEY = 'item';

	/** @var null|string */
	protected const MODEL = null;

	/** @var null|string */
	protected const RESOURCE_KEY_PLURAL = null;

	public function __construct()
	{
	}

	public function __call( $name, $arguments )
	{
		if ( method_exists( $this, $name ) ) {
			return $this->$name( ...$arguments );
		}

		throw new \BadMethodCallException( sprintf( 'Method "%s" does not exist in class %s', $name, static::class ) );
	}

	public static function getIndexUrl(): string
	{
		return trim( static::ENDPOINT_BASE, '/' );
	}

	protected function get( $page = 1, $perPage = 20, $query = [] )
	{
		$responseData = API::http()->get( static::getIndexUrl(), array_merge( [ 'page' => $page, 'limit' => $perPage ], $query, $this->wheres ) );

		if ( method_exists( $this, 'formatIndexResponse' ) ) {
			return $this->formatIndexResponse( $responseData );
		}

		$items = $responseData[ static::getPluralisedKey() ];

		if ( $model = static::MODEL ) {
			$items = array_map( function ( $item ) use ( $model ) { return new $model( $item ); }, $items );
		}

		if ( isset( $responseData['pages'] ) ) {
			return new PaginatedIndexResponse(
				$items,
				$responseData['pages'],
				$responseData['page'],
				$responseData['limit'],
				$responseData['total'],
			);
		}

		return new IndexResponse( $items );
	}

	protected static function delete( $key ) { }

	protected static function find( $key ) { }

	protected static function update( $model ) { }

	protected static function create( $data = [] ) { }

	public function where( $key, $value )
	{
		$this->wheres[ $key ] = $value;

		return $this;
	}

	public function when( $booleanCondition, callable $callback )
	{
		if ( ! empty( $booleanCondition ) ) {
			$callback( $this );
		}

		return $this;
	}

	/**
	 * Get the resource key, singular.
	 *
	 * @return string
	 */
	protected function getSingularKey(): string
	{
		return static::RESOURCE_KEY;
	}

	/**
	 * Get the resource key, pluralised.
	 *
	 * Uses Illuminate Str::plural (as does Rackbeat API) but can be overridden as necessary.
	 *
	 * To override the default, set your RESOURCE_KEY_PLURAL to the plural version.
	 *
	 * Example:
	 *
	 * RESOURCE_KEY         = person
	 * RESOURCE_KEY_PLURAL  = people
	 *
	 * @return string
	 */
	protected static function getPluralisedKey(): string
	{
		return static::RESOURCE_KEY_PLURAL ?? Str::plural( static::RESOURCE_KEY );
	}
}