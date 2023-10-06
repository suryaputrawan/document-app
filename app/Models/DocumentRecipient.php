<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRecipient extends Model
{
    use HasFactory;

    protected $table = 'document_recipient';
    protected $fillable = [
        'document_id',
        'karyawan_id',
        'status_recipient'
    ];
}
