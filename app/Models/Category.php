<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Category extends Model
{
    use HasFactory;

    protected $table = 'kriteria';
    protected $primaryKey = 'id_kriteria';

    protected $fillable = [
        'name',
        'description',
    ];

    protected $appends = ['id', 'name'];
    protected $hidden = ['id_kriteria', 'nama_kriteria'];

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['id_kriteria'] ?? null,
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['nama_kriteria'] ?? null,
            set: fn (mixed $value) => ['nama_kriteria' => $value],
        );
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'category_id', 'id_kriteria');
    }
}
