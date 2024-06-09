<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'image',
        'to_do_list_id'
    ];

    /**
     * @return BelongsTo
     */
    public function todolist(): BelongsTo
    {
        return $this->belongsTo(ToDoList::class, 'to_do_list_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getPathToImage(): string
    {
        return 'storage/images/tasks/' . $this->id;
    }
}
