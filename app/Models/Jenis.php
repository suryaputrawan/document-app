<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    use HasFactory;

    protected $table = 'jenis';
    protected $fillable = ['nama'];

    public function document()
    {
        return $this->hasMany(Document::class, 'id');
    }

    public function documentTemplate()
    {
        return $this->belongsTo(DocumentTemplate::class, 'id');
    }
}
