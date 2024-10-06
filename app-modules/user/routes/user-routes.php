<?php

Route::post('/user/login', \Modules\User\Http\Controllers\LoginController::class)->name('auth.login');
