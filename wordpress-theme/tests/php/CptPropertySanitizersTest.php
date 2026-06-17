<?php

namespace Nsml\Tests;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the pure sanitizer functions in inc/cpt-property.php.
 *
 * These functions are deliberately small, dependency-light wrappers around
 * a couple of WordPress sanitization helpers (sanitize_key, absint,
 * wp_json_encode, sanitize_text_field). Brain Monkey lets us stub those
 * WP functions with realistic plain-PHP equivalents so we can exercise the
 * NSML-specific validation logic (dropping malformed rows, capping array
 * length, defaulting invalid organizer types) without booting WordPress.
 */
class CptPropertySanitizersTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();

		Functions\stubs(
			array(
				'sanitize_key'       => static function ( $value ) {
					$value = strtolower( (string) $value );
					return preg_replace( '/[^a-z0-9_\-]/', '', $value );
				},
				'sanitize_text_field' => static function ( $value ) {
					return trim( (string) $value );
				},
				'absint'              => static function ( $value ) {
					return abs( (int) $value );
				},
				'wp_json_encode'      => static function ( $value ) {
					return json_encode( $value );
				},
			)
		);
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	// -- nsml_sanitize_organizer_type ---------------------------------------

	public function test_organizer_type_accepts_owned() {
		$this->assertSame( 'owned', nsml_sanitize_organizer_type( 'owned' ) );
	}

	public function test_organizer_type_accepts_consultant() {
		$this->assertSame( 'consultant', nsml_sanitize_organizer_type( 'consultant' ) );
	}

	public function test_organizer_type_rejects_invalid_value_and_defaults_to_owned() {
		$this->assertSame( 'owned', nsml_sanitize_organizer_type( 'something-else' ) );
		$this->assertSame( 'owned', nsml_sanitize_organizer_type( '' ) );
		$this->assertSame( 'owned', nsml_sanitize_organizer_type( '<script>alert(1)</script>' ) );
	}

	// -- nsml_sanitize_stats_json --------------------------------------------

	public function test_stats_json_returns_empty_array_for_non_array_input() {
		$this->assertSame( '[]', nsml_sanitize_stats_json( 'not json at all' ) );
		$this->assertSame( '[]', nsml_sanitize_stats_json( '' ) );
		$this->assertSame( '[]', nsml_sanitize_stats_json( '"just a string"' ) );
	}

	public function test_stats_json_drops_malformed_rows() {
		$input  = json_encode( array( 'not-an-array-row', array( 'value' => '5K+', 'label' => 'Participants' ) ) );
		$result = json_decode( nsml_sanitize_stats_json( $input ), true );

		$this->assertCount( 1, $result );
		$this->assertSame( '5K+', $result[0]['value'] );
		$this->assertSame( 'Participants', $result[0]['label'] );
	}

	public function test_stats_json_caps_at_twelve_rows() {
		$rows = array();
		for ( $i = 0; $i < 20; $i++ ) {
			$rows[] = array( 'value' => (string) $i, 'label' => "Label $i" );
		}
		$result = json_decode( nsml_sanitize_stats_json( json_encode( $rows ) ), true );

		$this->assertCount( 12, $result );
		$this->assertSame( '0', $result[0]['value'] );
		$this->assertSame( '11', $result[11]['value'] );
	}

	public function test_stats_json_fills_missing_keys_with_empty_strings() {
		$input  = json_encode( array( array( 'value' => 'Only Value' ) ) );
		$result = json_decode( nsml_sanitize_stats_json( $input ), true );

		$this->assertSame( 'Only Value', $result[0]['value'] );
		$this->assertSame( '', $result[0]['label'] );
	}

	// -- nsml_sanitize_gallery_json ------------------------------------------

	public function test_gallery_json_returns_empty_array_for_non_array_input() {
		$this->assertSame( '[]', nsml_sanitize_gallery_json( 'nonsense' ) );
		$this->assertSame( '[]', nsml_sanitize_gallery_json( '' ) );
	}

	public function test_gallery_json_drops_rows_missing_id() {
		$input  = json_encode(
			array(
				array( 'wide' => true ),               // missing id -> dropped
				array( 'id' => 42, 'wide' => false ),
			)
		);
		$result = json_decode( nsml_sanitize_gallery_json( $input ), true );

		$this->assertCount( 1, $result );
		$this->assertSame( 42, $result[0]['id'] );
	}

	public function test_gallery_json_caps_at_24_rows() {
		$rows = array();
		for ( $i = 1; $i <= 40; $i++ ) {
			$rows[] = array( 'id' => $i, 'wide' => false );
		}
		$result = json_decode( nsml_sanitize_gallery_json( json_encode( $rows ) ), true );

		$this->assertCount( 24, $result );
		$this->assertSame( 1, $result[0]['id'] );
		$this->assertSame( 24, $result[23]['id'] );
	}

	public function test_gallery_json_coerces_wide_to_bool_and_id_to_int() {
		$input  = json_encode(
			array(
				array( 'id' => '17', 'wide' => 'yes' ),
				array( 'id' => '5',  'wide' => '' ),
			)
		);
		$result = json_decode( nsml_sanitize_gallery_json( $input ), true );

		$this->assertSame( 17, $result[0]['id'] );
		$this->assertIsInt( $result[0]['id'] );
		$this->assertTrue( $result[0]['wide'] );

		$this->assertSame( 5, $result[1]['id'] );
		$this->assertFalse( $result[1]['wide'] );
	}
}
