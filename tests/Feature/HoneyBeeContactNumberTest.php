<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HoneyBeeContactNumberTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_contact_pages_use_the_canonical_honeybee_contact_details(): void
    {
        foreach ([route('contact'), route('about'), route('gift.design'), route('events'), route('laser.work')] as $url) {
            $this->get($url)->assertOk()->assertSee('https://wa.me/94717714267');
        }

        foreach ([route('contact'), route('gift.design'), route('events'), route('laser.work')] as $url) {
            $this->get($url)->assertSee('071 771 4267')->assertSee('tel:+94717714267');
        }
    }
}
