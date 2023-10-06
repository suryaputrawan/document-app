<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';
    protected $fillable = ['nama', 'jabatan', 'nip', 'ttd_picture'];

    public function getTakeImageAttribute()
    {
        if ($this->ttd_picture) {
            return "/storage/" . $this->ttd_picture;
        }

        return null;
    }

    public function getTakeTtdAttribute()
    {
        if ($this->ttd_picture) {
            return "storage/" . $this->ttd_picture;
        }

        return null;
    }

    public function user()
    {
        return $this->hasOne(User::class, 'karyawan_id');
    }

    public function documentApproval()
    {
        return $this->belongsToMany(
            Document::class,
            'document_approval',
            'karyawan_id',
            'document_id'
        );
    }

    public function documentRecipient()
    {
        return $this->belongsToMany(
            Document::class,
            'document_approval',
            'karyawan_id',
            'document_id'
        );
    }
}
