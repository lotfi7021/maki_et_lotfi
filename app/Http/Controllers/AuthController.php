<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: "/auth/register",
        tags: ["Authentification"],
        summary: "Creer un nouveau compte",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "name",     type: "string",  example: "Ahmed Ben Ali"),
                    new OA\Property(property: "email",    type: "string",  example: "admin@test.com"),
                    new OA\Property(property: "password", type: "string",  example: "123456"),
                    new OA\Property(property: "password_confirmation", type: "string", example: "123456"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Compte cree avec succes"),
            new OA\Response(response: 422, description: "Donnees invalides"),
        ]
    )]
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->sendEmailVerificationNotification(); // ← envoie l'email

        return response()->json([
            'message' => 'Compte créé. Vérifiez votre email avant de vous connecter.',
            'user'    => $user,
        ]);
    }

    #[OA\Post(
        path: "/auth/login",
        tags: ["Authentification"],
        summary: "Se connecter et obtenir un token JWT",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email",    type: "string", example: "admin@test.com"),
                    new OA\Property(property: "password", type: "string", example: "123456"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Connexion reussie"),
            new OA\Response(response: 401, description: "Identifiants incorrects"),
            new OA\Response(response: 403, description: "Email non verifie"),
        ]
    )]
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Identifiants incorrects'], 401);
        }

        // ← bloque si email non vérifié
        if (! Auth::user()->hasVerifiedEmail()) {
            Auth::logout();
            return response()->json([
                'message' => 'Veuillez vérifier votre email avant de vous connecter.'
            ], 403);
        }

        return $this->respondWithToken($token, Auth::user());
    }

    #[OA\Get(
        path: "/auth/me",
        tags: ["Authentification"],
        summary: "Obtenir l utilisateur connecte",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Profil utilisateur"),
            new OA\Response(response: 401, description: "Non authentifie"),
        ]
    )]
    public function me()
    {
        return response()->json(Auth::user());
    }

    #[OA\Post(
        path: "/auth/logout",
        tags: ["Authentification"],
        summary: "Se deconnecter",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Deconnecte avec succes"),
            new OA\Response(response: 401, description: "Non authentifie"),
        ]
    )]
    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Deconnecte avec succes']);
    }

    #[OA\Post(
        path: "/auth/refresh",
        tags: ["Authentification"],
        summary: "Rafraichir le token JWT",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Nouveau token genere"),
            new OA\Response(response: 401, description: "Token invalide"),
        ]
    )]
    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh(), Auth::user());
    }

    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => Auth::factory()->getTTL() * 60,
        ]);
    }
}