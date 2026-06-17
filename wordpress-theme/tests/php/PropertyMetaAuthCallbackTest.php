<?php

namespace Nsml\Tests;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * nsml_property_meta_auth_callback() is the auth_callback registered for
 * every nsml_property meta field. It must defer entirely to
 * current_user_can( 'edit_post', $post_id ) -- this test asserts that the
 * underlying WP capability check is invoked with the right arguments and
 * that its return value passes straight through (both true and false).
 */
class PropertyMetaAuthCallbackTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_returns_true_when_current_user_can_edit_post() {
		Functions\expect( 'current_user_can' )
			->once()
			->with( 'edit_post', 123 )
			->andReturn( true );

		$result = nsml_property_meta_auth_callback( false, 'nsml_location', 123 );

		$this->assertTrue( $result );
	}

	public function test_returns_false_when_current_user_cannot_edit_post() {
		Functions\expect( 'current_user_can' )
			->once()
			->with( 'edit_post', 456 )
			->andReturn( false );

		$result = nsml_property_meta_auth_callback( true, 'nsml_gallery_json', 456 );

		$this->assertFalse( $result );
	}
}
