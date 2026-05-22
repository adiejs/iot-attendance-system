<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Mahasiswa;
use Carbon\Carbon;
use App\Events\AttendanceLogged;

class AttendanceService
{
    /**
     * Memproses absensi berdasarkan UID KTP
     */
    public function processAttendance(string $uid, ?string $fotoPath = null)
    {
        // 1. Cari mahasiswa berdasarkan UID KTP
        $mahasiswa = Mahasiswa::where('rfid_uid', $uid)->first();

        $today = Carbon::today();
        $now = Carbon::now();

        if (!$mahasiswa) {
            $attendance = Attendance::create([
                'mahasiswa_id' => 9999,
                'tanggal' => $today,
                'check_in' => $now,
                'rfid_uid' => $uid,
                'foto' => $fotoPath,
            ]);

            // Trigger update tabel real-time
            event(new AttendanceLogged());

            return [
                'status' => 'error',
                'message' => 'KTP tidak terdaftar.',
                'code' => 404
            ];
        }
        // 2. Cek apakah hari ini sudah ada data absensi
        $attendance = Attendance::where('mahasiswa_id', $mahasiswa->id)
            ->where('mahasiswa_id', '!=', 9999)
            ->whereDate('tanggal', $today)
            ->first();

        // 3. Logika Check-In / Check-Out
        if (!$attendance) {
            // Belum absen sama sekali hari ini -> Proses Check-in
            $attendance = Attendance::create([
                'mahasiswa_id' => $mahasiswa->id,
                'tanggal' => $today,
                'check_in' => $now,
                'foto' => $fotoPath,
            ]);

            // Trigger update tabel real-time
            event(new AttendanceLogged());

            return [
                'status' => 'success',
                'message' => 'Berhasil Check-In: ' . $mahasiswa->nama,
                'data' => $attendance,
                'code' => 201
            ];
        } elseif (is_null($attendance->check_out)) {
            // Sudah check-in, tapi belum check-out -> Proses Check-out
            
            // Opsional: Cegah double tap terlalu cepat (misal jeda minimal 5 menit)
            if ($attendance->check_in->diffInMinutes($now) < 5) {
                return [
                    'status' => 'warning',
                    'message' => 'Anda baru saja Check-In. Tunggu beberapa saat.',
                    'code' => 400
                ];
            }

            $attendance->update([
                'check_out' => $now,
                'foto' => $fotoPath ?? $attendance->foto,
            ]);

            // Trigger update tabel real-time
            event(new AttendanceLogged());

            return [
                'status' => 'success',
                'message' => 'Berhasil Check-Out: ' . $mahasiswa->nama,
                'data' => $attendance,
                'code' => 200
            ];
        } else {
            // Sudah check-in dan check-out
            return [
                'status' => 'warning',
                'message' => 'Anda sudah menyelesaikan absensi hari ini.',
                'code' => 400
            ];
        }
    }
}