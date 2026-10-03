<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Subcategory extends Model
{
    use HasFactory;

    protected $table = 'subkriteria';
    protected $primaryKey = 'id_subkriteria';

    protected $fillable = [
        'category_id',
        'name',
        'description',
    ];

    protected $appends = ['id', 'name'];
    protected $hidden = ['id_subkriteria', 'nama_subkriteria'];

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['id_subkriteria'] ?? null,
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['nama_subkriteria'] ?? null,
            set: fn (mixed $value) => ['nama_subkriteria' => $value],
        );
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id_kriteria');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'subcategory_id', 'id_subkriteria');
    }
}
