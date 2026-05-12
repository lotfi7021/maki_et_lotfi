<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Http\Controllers\NoteController;  // ← ADD THIS LINE




// Routes publiques
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
});

// Routes utilisateur connecte
Route::middleware('auth:api')->prefix('auth')->group(function () {
    Route::get('me',       [AuthController::class, 'me']);
    Route::post('logout',  [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

// Routes admin seulement
Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    Route::get('users',           [AdminController::class, 'listUsers']);
    Route::delete('users/{id}',   [AdminController::class, 'deleteUser']);
    Route::put('users/{id}/role', [AdminController::class, 'changeRole']);
});

Route::prefix('notes')->group(function () {
        Route::get('/',                         [NoteController::class, 'index']);
        Route::post('/',                        [NoteController::class, 'store']);
        Route::get('/{id}',                     [NoteController::class, 'show']);
        Route::put('/{id}',                     [NoteController::class, 'update']);
        Route::delete('/{id}',                  [NoteController::class, 'destroy']);
        Route::patch('/{id}/toggle-pin',        [NoteController::class, 'togglePin']);
    });



// Vérifier l'email via le lien
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = App\Models\User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Lien invalide'], 403);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email déjà vérifié']);
    }

    $user->markEmailAsVerified();
    event(new Verified($user));

    return response()->json(['message' => 'Email vérifié avec succès ✅']);

})->middleware('signed')->name('verification.verify');

// Renvoyer l'email de vérification
Route::post('/email/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Email de vérification renvoyé']);
})->middleware('auth:api');


// Routes utilisateur connecté
Route::middleware('auth:api')->prefix('user')->group(function () {
    Route::get('profile', [UserController::class, 'profile']);
    Route::put('profile', [UserController::class, 'updateProfile']);
    Route::put('password', [UserController::class, 'updatePassword']); // ← nouveau

});