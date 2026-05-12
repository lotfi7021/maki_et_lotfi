<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Note",
    type: "object",
    properties: [
        new OA\Property(property: "id",         type: "integer", example: 1),
        new OA\Property(property: "user_id",    type: "integer", example: 2),
        new OA\Property(property: "title",      type: "string",  example: "My first note"),
        new OA\Property(property: "content",    type: "string",  example: "Some content here..."),
        new OA\Property(property: "color",      type: "string",  example: "#FFEB3B"),
        new OA\Property(property: "theme",      type: "string",  example: "pastel"),
        new OA\Property(property: "is_pinned",  type: "boolean", example: false),
        new OA\Property(property: "created_at", type: "string",  example: "2025-01-15T10:30:00Z"),
        new OA\Property(property: "updated_at", type: "string",  example: "2025-01-15T10:30:00Z"),
    ]
)]
class Note extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'color',
        'theme',
        'is_pinned',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    /**
     * Available themes for a note.
     */
    public const THEMES = ['default', 'dark', 'pastel', 'ocean', 'forest', 'sunset'];

    /**
     * The user who owns this note.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
