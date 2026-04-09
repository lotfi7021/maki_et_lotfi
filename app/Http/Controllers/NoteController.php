<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class NoteController extends Controller
{
    // -------------------------------------------------------------------------
    // LIST all notes for the authenticated user
    // -------------------------------------------------------------------------
    #[OA\Get(
        path: "/notes",
        tags: ["Notes"],
        summary: "List all notes of the logged-in user",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "theme",
                in: "query",
                required: false,
                description: "Filter by theme (default, dark, pastel, ocean, forest, sunset)",
                schema: new OA\Schema(type: "string")
            ),
            new OA\Parameter(
                name: "pinned",
                in: "query",
                required: false,
                description: "Filter pinned notes only (1 = pinned)",
                schema: new OA\Schema(type: "integer", enum: [0, 1])
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of notes",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "total", type: "integer", example: 3),
                        new OA\Property(
                            property: "notes",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/Note")
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Auth::user()->notes()->latest();

        if ($request->filled('theme')) {
            $query->where('theme', $request->theme);
        }

        if ($request->filled('pinned')) {
            $query->where('is_pinned', (bool) $request->pinned);
        }

        $notes = $query->get();

        return response()->json([
            'total' => $notes->count(),
            'notes' => $notes,
        ]);
    }

    // -------------------------------------------------------------------------
    // CREATE a note
    // -------------------------------------------------------------------------
    #[OA\Post(
        path: "/notes",
        tags: ["Notes"],
        summary: "Create a new note",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title"],
                properties: [
                    new OA\Property(property: "title",     type: "string",  example: "Shopping list"),
                    new OA\Property(property: "content",   type: "string",  example: "Buy milk, eggs..."),
                    new OA\Property(property: "color",     type: "string",  example: "#FFEB3B", description: "Hex color code"),
                    new OA\Property(property: "theme",     type: "string",  example: "pastel",  description: "default | dark | pastel | ocean | forest | sunset"),
                    new OA\Property(property: "is_pinned", type: "boolean", example: false),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Note created",
                content: new OA\JsonContent(ref: "#/components/schemas/Note")
            ),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'content'   => 'nullable|string',
            'color'     => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme'     => 'nullable|string|in:' . implode(',', Note::THEMES),
            'is_pinned' => 'nullable|boolean',
        ]);

        $note = Auth::user()->notes()->create([
            'title'     => $validated['title'],
            'content'   => $validated['content']   ?? null,
            'color'     => $validated['color']      ?? '#FFFFFF',
            'theme'     => $validated['theme']      ?? 'default',
            'is_pinned' => $validated['is_pinned']  ?? false,
        ]);

        return response()->json($note, 201);
    }

    // -------------------------------------------------------------------------
    // SHOW a single note
    // -------------------------------------------------------------------------
    #[OA\Get(
        path: "/notes/{id}",
        tags: ["Notes"],
        summary: "Get a specific note",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Note found",
                content: new OA\JsonContent(ref: "#/components/schemas/Note")
            ),
            new OA\Response(response: 404, description: "Note not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $note = Auth::user()->notes()->find($id);

        if (! $note) {
            return response()->json(['message' => 'Note not found'], 404);
        }

        return response()->json($note);
    }

    // -------------------------------------------------------------------------
    // UPDATE a note
    // -------------------------------------------------------------------------
    #[OA\Put(
        path: "/notes/{id}",
        tags: ["Notes"],
        summary: "Update a note",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "title",     type: "string",  example: "Updated title"),
                    new OA\Property(property: "content",   type: "string",  example: "Updated content"),
                    new OA\Property(property: "color",     type: "string",  example: "#4CAF50"),
                    new OA\Property(property: "theme",     type: "string",  example: "dark"),
                    new OA\Property(property: "is_pinned", type: "boolean", example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Note updated",
                content: new OA\JsonContent(ref: "#/components/schemas/Note")
            ),
            new OA\Response(response: 404, description: "Note not found"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        $note = Auth::user()->notes()->find($id);

        if (! $note) {
            return response()->json(['message' => 'Note not found'], 404);
        }

        $validated = $request->validate([
            'title'     => 'sometimes|required|string|max:255',
            'content'   => 'nullable|string',
            'color'     => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme'     => 'nullable|string|in:' . implode(',', Note::THEMES),
            'is_pinned' => 'nullable|boolean',
        ]);

        $note->update($validated);

        return response()->json($note);
    }

    // -------------------------------------------------------------------------
    // DELETE a note
    // -------------------------------------------------------------------------
    #[OA\Delete(
        path: "/notes/{id}",
        tags: ["Notes"],
        summary: "Delete a note",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "Note deleted"),
            new OA\Response(response: 404, description: "Note not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $note = Auth::user()->notes()->find($id);

        if (! $note) {
            return response()->json(['message' => 'Note not found'], 404);
        }

        $note->delete();

        return response()->json(['message' => 'Note deleted successfully']);
    }

    // -------------------------------------------------------------------------
    // TOGGLE PIN
    // -------------------------------------------------------------------------
    #[OA\Patch(
        path: "/notes/{id}/toggle-pin",
        tags: ["Notes"],
        summary: "Toggle the pinned state of a note",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "Pin toggled"),
            new OA\Response(response: 404, description: "Note not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function togglePin(int $id): JsonResponse
    {
        $note = Auth::user()->notes()->find($id);

        if (! $note) {
            return response()->json(['message' => 'Note not found'], 404);
        }

        $note->update(['is_pinned' => ! $note->is_pinned]);

        return response()->json([
            'message'   => $note->is_pinned ? 'Note pinned' : 'Note unpinned',
            'is_pinned' => $note->is_pinned,
        ]);
    }
}
