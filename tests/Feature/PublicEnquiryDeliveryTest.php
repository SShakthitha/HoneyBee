<?php

namespace Tests\Feature;

use App\Models\GiftDesign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicEnquiryDeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_gift_enquiry_is_validated_and_delivered_to_the_configured_whatsapp_number(): void
    {
        $response = $this->post(route('gift.enquiry'), [
            'name' => 'Taylor Customer',
            'phone' => '0771234567',
            'email' => 'taylor@example.com',
            'product' => 'Custom Frame Work',
            'message' => 'Please use blue and white.',
        ]);

        $response->assertRedirect();
        $destination = rawurldecode($response->headers->get('Location'));
        $this->assertStringContainsString('https://wa.me/'.config('honeybee.contact.whatsapp_number'), $destination);
        $this->assertStringContainsString('Phone: 0771234567', $destination);
        $this->assertStringContainsString('Please use blue and white.', $destination);
    }

    public function test_contact_enquiry_delivers_the_submitted_phone_number(): void
    {
        $response = $this->post(route('contact.send'), [
            'name' => 'Taylor Customer',
            'email' => 'taylor@example.com',
            'phone' => '0771234567',
            'message' => 'I have a question.',
        ]);

        $response->assertRedirect();
        $destination = rawurldecode($response->headers->get('Location'));
        $this->assertStringContainsString('Phone: 0771234567', $destination);
        $this->assertStringContainsString('I have a question.', $destination);
    }

    public function test_invalid_enquiry_does_not_redirect_to_whatsapp(): void
    {
        $this->from(route('gift.design'))
            ->post(route('gift.enquiry'), ['name' => '', 'phone' => '', 'product' => 'Gift'])
            ->assertRedirect(route('gift.design'))
            ->assertSessionHasErrors(['name', 'phone']);
    }

    public function test_product_names_are_html_escaped_in_the_cart_controls(): void
    {
        GiftDesign::create([
            'item_name' => '<img src=x onerror=alert(1)>',
            'category' => 'Gift',
            'price' => 1500,
        ]);

        $this->get(route('gift.design'))
            ->assertOk()
            ->assertSee('data-product-name="&lt;img src=x onerror=alert(1)&gt;"', false)
            ->assertDontSee('<img src=x onerror=alert(1)>', false);
    }
}
