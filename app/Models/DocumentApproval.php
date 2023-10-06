<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentApproval extends Model
{
    use HasFactory;

    protected $table = 'document_approval';
    protected $fillable = [
        'document_id',
        'karyawan_id',
        'status_approval'
    ];
}
