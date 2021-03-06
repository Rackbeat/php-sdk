<?php

namespace RackbeatSDK\Http;

class MockHttpEngine extends HttpEngine
{
	public static array $callsMade   = [];
	public static array $mockedCalls = [];

	public function call( $method, $uri, $options = [] )
	{
		$method = \strtolower( $method );

		$response = self::$mockedCalls[ $method . $uri ] ?? '';

		self::$callsMade[] = [
			'method'   => $method,
			'uri'      => $uri,
			'options'  => $options,
			'response' => $response,
		];

		return $response;
	}

	public function __destruct()
	{
		self::$callsMade   = [];
		self::$mockedCalls = [];
	}

	public static function getCallsMade(): array
	{
		return self::$callsMade;
	}

	public static function mockResponse( $method, $uri, $statusCode, $response )
	{
		self::$mockedCalls[ \strtolower( $method ) . $uri ] = [
			'status_code' => $statusCode,
			'content'     => $response
		];
	}

	public static function calledCount( $method, $uri ): int
	{
		return \count( self::getCalls( $method, $uri ) );
	}

	public static function latestResponse( $method, $uri )
	{
		$calls = array_reverse( self::getCalls( $method, $uri ) );

		if ( \count( $calls ) === 0 ) {
			return null;
		}

		return $calls[0]['response'];
	}

	public static function getCalls( $method, $uri )
	{
		return array_filter( self::getCallsMade(), function ( $call ) use ( $method, $uri ) {
			return $call['method'] === \strtolower( $method ) &&
			       $call['uri'] === $uri;
		} );
	}
}