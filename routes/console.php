<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Marca como entregados los pedidos cuya fecha_entrega ya llegó (corre cada día a medianoche)
Schedule::command('pedidos:marcar-entregados')->dailyAt('00:01');
