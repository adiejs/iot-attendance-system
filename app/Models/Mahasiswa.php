<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    protected $fillable = ['nim', 'nama', 'rfid_uid'];

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}