<?php

namespace Nsml\Tests;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the pure validation logic in inc/newsletter.php.
 *
 * nsml_newsletter_email_is_valid() is the last line of defense before a
 * row is inserted into the subscribers table, so it's covered directly
 * (independent of $_POST, nonces, and $wpdb, which all live in
 * nsml_handle_newsletter_signup()).
 */
class NewsletterTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();

		Functions\stubs(
			array(
				'is_email' => static function ( $value ) {
					return (bool) filter_var( $value, FILTER_VALIDATE_EMAIL );
				},
			)
		);
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_accepts_well_formed_email() {
		$this->assertTrue( nsml_newsletter_email_is_valid( 'jane@example.com' ) );
	}

	public function test_rejects_malformed_email() {
		$this->assertFalse( nsml_newsletter_email_is_valid( 'not-an-email' ) );
	}

	public function test_rejects_empty_email() {
		$this->assertFalse( nsml_newsletter_email_is_valid( '' ) );
	}
}
