<?php

namespace Nsml\Tests;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * nsml_get_property_meta_array() decodes a property's JSON postmeta
 * (stats / gallery) into a plain PHP array, returning [] for anything that
 * isn't valid JSON-encoded array data -- since template code relies on
 * always getting back an iterable, never null/false/a warning.
 */
class TemplateTagsTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_returns_empty_array_for_malformed_json() {
		Functions\expect( 'get_post_meta' )
			->once()
			->with( 7, 'nsml_stats_json', true )
			->andReturn( 'not valid json' );

		$this->assertSame( array(), nsml_get_property_meta_array( 7, 'nsml_stats_json' ) );
	}

	public function test_returns_empty_array_for_empty_meta() {
		Functions\expect( 'get_post_meta' )
			->once()
			->with( 7, 'nsml_gallery_json', true )
			->andReturn( '' );

		$this->assertSame( array(), nsml_get_property_meta_array( 7, 'nsml_gallery_json' ) );
	}

	public function test_returns_empty_array_when_json_decodes_to_non_array_scalar() {
		Functions\expect( 'get_post_meta' )
			->once()
			->with( 7, 'nsml_stats_json', true )
			->andReturn( '"just a string"' );

		$this->assertSame( array(), nsml_get_property_meta_array( 7, 'nsml_stats_json' ) );
	}

	public function test_returns_decoded_array_for_valid_json() {
		$raw = json_encode(
			array(
				array( 'value' => '440K+', 'label' => 'Cumulative Participants' ),
				array( 'value' => '54', 'label' => 'African Nations Represented' ),
			)
		);

		Functions\expect( 'get_post_meta' )
			->once()
			->with( 99, 'nsml_stats_json', true )
			->andReturn( $raw );

		$result = nsml_get_property_meta_array( 99, 'nsml_stats_json' );

		$this->assertCount( 2, $result );
		$this->assertSame( '440K+', $result[0]['value'] );
		$this->assertSame( 'Cumulative Participants', $result[0]['label'] );
	}

	public function test_get_property_stats_delegates_to_meta_array_helper() {
		Functions\expect( 'get_post_meta' )
			->once()
			->with( 5, 'nsml_stats_json', true )
			->andReturn( json_encode( array( array( 'value' => '1', 'label' => 'x' ) ) ) );

		$result = nsml_get_property_stats( 5 );

		$this->assertCount( 1, $result );
	}

	public function test_get_property_gallery_delegates_to_meta_array_helper() {
		Functions\expect( 'get_post_meta' )
			->once()
			->with( 5, 'nsml_gallery_json', true )
			->andReturn( json_encode( array( array( 'id' => 1, 'wide' => false ) ) ) );

		$result = nsml_get_property_gallery( 5 );

		$this->assertCount( 1, $result );
	}
}
