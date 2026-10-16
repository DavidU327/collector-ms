<?php

namespace Tests\Feature;

use Tests\TestCase;

class MailConfigurationTest extends TestCase
{
    public function test_mail_configuration_has_default_sender_and_driver(): void
    {
        $this->assertSame('log', config('mail.default'));
        $this->assertSame('hello@example.com', config('mail.from.address'));
        $this->assertSame('Laravel', config('mail.from.name'));
    }
}
