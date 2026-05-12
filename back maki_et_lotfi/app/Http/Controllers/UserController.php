<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    #[OA\Get(
        path: "/user/profile",
        tags: ["Utilisateur"],
        summary: "Voir son propre profil",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Profil utilisateur"),
            new OA\Response(response: 401, description: "Non authentifié"),
        ]
    )]
    public function profile()
    {
        return response()->json(Auth::user());
    }

    #[OA\Put(
        path: "/user/profile",
        tags: ["Utilisateur"],
        summary: "Modifier son nom et/ou email",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name",  type: "string", example: "Ahmed Ben Ali"),
                    new OA\Property(property: "email", type: "string", example: "ahmed@test.com"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Profil mis à jour"),
            new OA\Response(response: 422, description: "Données invalides"),
            new OA\Response(response: 401, description: "Non authentifié"),
        ]
    )]
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'Profil mis à jour',
            'user'    => $user->fresh(),
        ]);
    }



#[OA\Put(
    path: "/user/password",
    tags: ["Utilisateur"],
    summary: "Changer son mot de passe",
    security: [["bearerAuth" => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["current_password", "password", "password_confirmation"],
            properties: [
                new OA\Property(property: "current_password",      type: "string", example: "123456"),
                new OA\Property(property: "password",              type: "string", example: "nouveau123"),
                new OA\Property(property: "password_confirmation", type: "string", example: "nouveau123"),
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Mot de passe mis à jour"),
        new OA\Response(response: 400, description: "Mot de passe actuel incorrect"),
        new OA\Response(response: 422, description: "Données invalides"),
        new OA\Response(response: 401, description: "Non authentifié"),
    ]
)]
public function updatePassword(Request $request)
{
    $user = User::find(Auth::id()); // ← au lieu de Auth::user()

    $request->validate([
        'current_password'      => 'required',
        'password'              => 'required|min:6|confirmed',
    ]);

    // Vérifier que l'ancien mot de passe est correct
    if (! Hash::check($request->current_password, $user->password)) {
        return response()->json([
            'message' => 'Mot de passe actuel incorrect'
        ], 400);
    }

    $user->update([
        'password' => Hash::make($request->password),
    ]);

    return response()->json([
        'message' => 'Mot de passe mis à jour avec succès'
    ]);
}




}