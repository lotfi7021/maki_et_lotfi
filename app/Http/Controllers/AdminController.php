<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AdminController extends Controller
{
    #[OA\Get(
        path: "/admin/users",
        tags: ["Admin"],
        summary: "Lister tous les utilisateurs",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Liste des utilisateurs"),
            new OA\Response(response: 403, description: "Acces refuse"),
        ]
    )]
    public function listUsers()
    {
        return response()->json(User::all());
    }

    #[OA\Delete(
        path: "/admin/users/{id}",
        tags: ["Admin"],
        summary: "Supprimer un utilisateur",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Utilisateur supprime"),
            new OA\Response(response: 404, description: "Utilisateur non trouve"),
            new OA\Response(response: 403, description: "Acces refuse"),
        ]
    )]
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Utilisateur supprime']);
    }

    #[OA\Put(
        path: "/admin/users/{id}/role",
        tags: ["Admin"],
        summary: "Changer le role d un utilisateur",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["role"],
                properties: [
                    new OA\Property(
                        property: "role",
                        type: "string",
                        enum: ["admin", "user"],
                        example: "admin"
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Role mis a jour"),
            new OA\Response(response: 403, description: "Acces refuse"),
        ]
    )]
    public function changeRole(Request $request, $id)
    {
        $request->validate(['role' => 'required|in:admin,user']);
        $user = User::findOrFail($id);
        $user->update(['role' => $request->role]);
        return response()->json([
            'message' => 'Role mis a jour',
            'user'    => $user,
        ]);
    }
}