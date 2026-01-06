<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CharacterInvitationController;
use App\Http\Controllers\CharacterSwitchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function() {
    Route::get('/login', [LoginController::class, 'show'])->name('auth.login');
    Route::post('/login', [LoginController::class, 'process']);
    Route::get('/register', [RegisterController::class, 'show'])->name('auth.register');
    Route::post('/register', [RegisterController::class, 'process']);
});

// Character selection (requires web auth only)
Route::middleware(['auth:web'])->group(function() {
    Route::get('/select-character', [LoginController::class, 'showCharacterSelect'])->name('character.select');
    Route::get('/select-character/{character}', [LoginController::class, 'selectCharacter'])->name('character.select.process');
});

// Public invitation routes (signed)
Route::get('/invitation/{token}/accept', [CharacterInvitationController::class, 'accept'])
    ->name('invitation.accept')
    ->middleware('signed');
Route::post('/invitation/{token}/decline', [CharacterInvitationController::class, 'decline'])
    ->name('invitation.decline')
    ->middleware('signed');

Route::middleware(['auth:web,character'])->group(function() {
    Route::get('/', [DashboardController::class, 'show'])->name('dashboard');
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');

    // Character switching (needs both guards)
    Route::get('/characters', [CharacterSwitchController::class, 'list'])->name('characters.list');
    Route::post('/characters/{character}/switch', [CharacterSwitchController::class, 'switch'])->name('characters.switch');

    // Character invitations
    Route::get('/invitations', [CharacterInvitationController::class, 'index'])->name('invitations.index');
    Route::post('/invitations', [CharacterInvitationController::class, 'create'])->name('invitations.create');
    Route::post('/invitations/{invitation}/revoke', [CharacterInvitationController::class, 'revoke'])->name('invitations.revoke');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'deleteAccount'])->name('profile.delete');
    Route::delete('/characters/{character}', [ProfileController::class, 'deleteCharacter'])->name('characters.delete');

    Route::prefix('/admin')->group(function() {
//        Route::get('/', [DashboardController::class, 'adminShow'])->name('admin.dashboard');
        Route::prefix('users')->middleware('characterCanAny:'.App\Helpers\Permissions::USERS_SHOW)->group(function() {
            Route::get('/', [UserController::class, 'list'])->name('admin.users.list');
        });

        Route::get('/media', [MediaController::class, 'gallery'])
            ->middleware('can:'.App\Helpers\Permissions::MEDIA_GALLERY_VIEW)
            ->name('admin.media.gallery');
    });
});

// Locale switching (available for all users)
Route::post('/locale/switch', [LocaleController::class, 'switch'])->name('locale.switch');
