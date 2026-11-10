<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\WorkspaceSetupController;
use App\Http\Middleware\RequireWorkspace;
use App\Http\Middleware\VerifyWorkspaceOrigin;
use Illuminate\Support\Facades\Route;

Route::middleware(VerifyWorkspaceOrigin::class)->group(function (): void {
    Route::get('/', [WorkspaceSetupController::class, 'show'])->block(30, 10);
    Route::prefix('install')->controller(WorkspaceSetupController::class)->group(function (): void {
        Route::get('/', 'show')->name('install')->block(30, 10);
        Route::get('/admin', 'admin')->name('install.admin')->block(30, 10);
        Route::middleware('throttle:12,1,workspace-install')->group(function (): void {
            Route::post('/verify', 'verify')->name('install.verify')->block(30, 10);
            Route::post('/edit', 'edit')->name('install.edit')->block(30, 10);
            Route::post('/admin', 'finish')->name('install.finish')->block(120, 10);
        });
    });

    Route::middleware(RequireWorkspace::class)->group(function (): void {
        Route::middleware('guest')->group(function (): void {
            Route::view('/login', 'account.login')->name('login');
            Route::view('/register', 'account.register')->name('register');
            Route::view('/forgot-password', 'account.forgot-password')->name('password.request');
            Route::get('/reset-password/{token}', [AccountController::class, 'resetForm'])->name('password.reset');
            Route::post('/login', [AccountController::class, 'signIn'])
                ->middleware('throttle:8,1,workspace-login')->name('login.store');
            Route::post('/register', [AccountController::class, 'register'])
                ->middleware('throttle:6,1,workspace-registration')->name('register.store');
            Route::middleware('throttle:8,1,workspace-recovery')->controller(AccountController::class)->group(function (): void {
                Route::post('/forgot-password', 'sendResetLink')->name('password.email');
                Route::post('/reset-password', 'resetPassword')->name('password.update');
            });
        });

        Route::middleware(['auth', 'auth.session'])->group(function (): void {
            Route::view('/dashboard', 'workspace.dashboard')->name('dashboard');
            Route::post('/logout', [AccountController::class, 'signOut'])->name('logout');
        });
    });
});
