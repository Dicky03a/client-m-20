<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    use HasFactory;

    protected $table = 'pengisian';

    protected $fillable = [
        'user_id',
        'subcategory_id',
        'user_file_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id', 'id_subkriteria');
    }

    public function userFile(): BelongsTo
    {
        return $this->belongsTo(UserFile::class);
    }
}
