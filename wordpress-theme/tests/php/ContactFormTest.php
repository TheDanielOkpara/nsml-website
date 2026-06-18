<?php

namespace Nsml\Tests;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the pure validation logic in inc/contact-form.php.
 *
 * nsml_contact_form_is_valid() mirrors the client-side validation rules in
 * page-contact.php and is the last line of defense before an email is
 * sent, so it's covered directly (independent of $_POST, nonces, and
 * wp_mail(), which all live in nsml_handle_contact_form_submission()).
 */
class ContactFormTest extends TestCase {

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

	private function valid_message() {
		return 'This is a perfectly adequate enquiry message for testing.';
	}

	public function test_accepts_well_formed_submission() {
		$this->assertTrue(
			nsml_contact_form_is_valid( 'Jane', 'Doe', 'jane@example.com', 'sponsorship', $this->valid_message() )
		);
	}

	public function test_rejects_short_first_or_last_name() {
		$this->assertFalse( nsml_contact_form_is_valid( 'J', 'Doe', 'jane@example.com', 'general', $this->valid_message() ) );
		$this->assertFalse( nsml_contact_form_is_valid( 'Jane', 'D', 'jane@example.com', 'general', $this->valid_message() ) );
	}

	public function test_rejects_invalid_email() {
		$this->assertFalse( nsml_contact_form_is_valid( 'Jane', 'Doe', 'not-an-email', 'general', $this->valid_message() ) );
	}

	public function test_rejects_unknown_interest_value() {
		$this->assertFalse( nsml_contact_form_is_valid( 'Jane', 'Doe', 'jane@example.com', 'not-a-real-option', $this->valid_message() ) );
		$this->assertFalse( nsml_contact_form_is_valid( 'Jane', 'Doe', 'jane@example.com', '', $this->valid_message() ) );
	}

	public function test_rejects_message_under_20_characters() {
		$this->assertFalse( nsml_contact_form_is_valid( 'Jane', 'Doe', 'jane@example.com', 'general', 'too short' ) );
	}

	public function test_rejects_message_over_500_characters() {
		$this->assertFalse( nsml_contact_form_is_valid( 'Jane', 'Doe', 'jane@example.com', 'general', str_repeat( 'a', 501 ) ) );
	}

	public function test_accepts_message_at_boundaries() {
		$this->assertTrue( nsml_contact_form_is_valid( 'Jane', 'Doe', 'jane@example.com', 'general', str_repeat( 'a', 20 ) ) );
		$this->assertTrue( nsml_contact_form_is_valid( 'Jane', 'Doe', 'jane@example.com', 'general', str_repeat( 'a', 500 ) ) );
	}

	public function test_interest_labels_cover_every_option_in_the_form() {
		$labels = nsml_contact_interest_labels();

		$this->assertSame(
			array( 'sponsorship', 'brand', 'marketing', 'contracts', 'consulting', 'procurement', 'project', 'general' ),
			array_keys( $labels )
		);
	}
}
