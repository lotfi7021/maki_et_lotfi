<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; // ← manquait
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     @OA\Property(property="id",         type="integer", example=1),
 *     @OA\Property(property="name",       type="string",  example="Ahmed Ben Ali"),
 *     @OA\Property(property="email",      type="string",  example="ahmed@test.com"),
 *     @OA\Property(property="created_at", type="string",  example="2025-01-01T12:00:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="TokenResponse",
 *     type="object",
 *     @OA\Property(property="access_token", type="string",  example="eyJ0eXAiOiJKV1Qi..."),
 *     @OA\Property(property="token_type",   type="string",  example="bearer"),
 *     @OA\Property(property="expires_in",   type="integer", example=3600),
 *     @OA\Property(property="user",         ref="#/components/schemas/UserResource")
 * )
 */
class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable; // ← fonctionne maintenant

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden   = ['password'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}