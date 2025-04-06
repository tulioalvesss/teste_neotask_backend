<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Traits\WithAuthentication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, WithAuthentication;

    /**
     * Configuração inicial para todos os testes
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling([
            'Illuminate\Auth\AuthenticationException',
            'Illuminate\Auth\Access\AuthorizationException',
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
        ]);
    }
}
