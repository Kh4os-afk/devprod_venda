<?php

use Illuminate\Support\Facades\Route;

Route::get('/',\App\Livewire\Vendas::class);
Route::get('/vendas',\App\Livewire\Vendas::class);
Route::get('/vendas/show',\App\Livewire\VendasShow::class)->name('vendas.show');
