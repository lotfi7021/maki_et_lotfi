<?php
// Routes utilisateur connecte
Route::middleware('auth:api')->prefix('notes')->group(function () {
    Route::get('/',                         [NoteController::class, 'index']);
    Route::post('/',                        [NoteController::class, 'store']);
    Route::get('/{id}',                     [NoteController::class, 'show']);
    Route::put('/{id}',                     [NoteController::class, 'update']);
    Route::delete('/{id}',                  [NoteController::class, 'destroy']);
    Route::patch('/{id}/toggle-pin',        [NoteController::class, 'togglePin']);
});