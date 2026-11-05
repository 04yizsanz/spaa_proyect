<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthenticationLayoutTest extends TestCase
{
    public function test_login_hides_the_main_navigation(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertDontSee('<header')
            ->assertDontSee('Navegación principal');
    }

    public function test_public_registration_displays_only_the_client_role(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Tipo de cuenta')
            ->assertSee('Cliente')
            ->assertSee('los roles del equipo y de administrador los asigna un administrador')
            ->assertDontSee('Navegación principal');
    }

    public function test_user_management_api_requires_an_administrator_session(): void
    {
        $this->getJson('/api/usuarios')->assertUnauthorized();
        $this->getJson('/api/roles')->assertUnauthorized();
    }
}
