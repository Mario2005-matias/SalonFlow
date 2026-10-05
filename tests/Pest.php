<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Todo o teste herda do TestCase do Laravel para ter acesso ao container,
| helpers ($this->get, $this->actingAs), e assertions específicas.
|
*/

uses(Tests\TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| RefreshDatabase
|--------------------------------------------------------------------------
|
| Só os testes de Feature tocam na BD. Aplicamos RefreshDatabase apenas
| a essa pasta — testes Unit puros ficam rápidos e isolados.
|
*/

uses(Illuminate\Foundation\Testing\RefreshDatabase::class)->in('Feature');
