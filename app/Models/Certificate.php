<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $table = 'certificates';

    protected $fillable = [
        'name',
        'certificate_number',
        'start_date',
        'end_date',
        'employee_name',
        'certificate_type_id',
        'file',
        'user_created'
    ];

    public function getTakeFileAttribute()
    {
        if ($this->file) {
            return "/storage/" . $this->file;
        }

        return null;
    }

    public function userCreated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_created');
    }

    public function certificateType(): BelongsTo
    {
        return $this->belongsTo(CertificateType::class, 'certificate_type_id');
    }
}
