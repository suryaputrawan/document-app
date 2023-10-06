<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';
    protected $fillable = [
        'no_surat',
        'tgl_surat',
        'jenis_id',
        'body',
        'pengirim_diajukan_oleh',
        'status_pengirim_diajukan',
        'pengirim_disetujui_oleh',
        'status_pengirim_disetujui',
        'created_by',
    ];

    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'jenis_id');
    }

    public function diajukanOleh()
    {
        return $this->belongsTo(Karyawan::class, 'pengirim_diajukan_oleh', 'id');
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(Karyawan::class, 'pengirim_disetujui_oleh', 'id');
    }

    public function approval()
    {
        return $this->belongsToMany(
            Karyawan::class,
            'document_approval',
            'document_id',
            'karyawan_id',
        )
            ->withPivot('status_approval');
    }

    public function recipient()
    {
        return $this->belongsToMany(
            Karyawan::class,
            'document_recipient',
            'document_id',
            'karyawan_id'
        )
            ->withPivot('status_recipient');
    }
}
