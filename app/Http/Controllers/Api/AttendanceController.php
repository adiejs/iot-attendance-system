<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScannedUid;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use App\Events\UidScanned;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    // Inject Service melalui Constructor
    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Endpoint untuk mode Absensi Harian
     */
public function store(Request $request)
    {

        // dd($request->all());

        $request->validate([
            'uid' => 'required|string',
        ]);

        $fotoPath = null;

        // Skenario 1: Jika ESP32 mengirim foto dalam bentuk File (multipart/form-data)
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('attendances', 'public');
        } 
        // Skenario 2: Jika ESP32 mengirim foto dalam bentuk Base64 String (JSON)
        elseif ($request->filled('foto')) {
            $image = $request->foto;
            
            if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
                $image = substr($image, strpos($image, ',') + 1);
            }
            
            $image = str_replace(' ', '+', $image);
            $fotoPath = 'attendances/' . Str::random(32) . '.jpg';
            
            // Simpan file ke direktori storage/app/public/attendances
            Storage::disk('public')->put($fotoPath, base64_decode($image));
        }

        // Lempar uid dan fotoPath ke Service
        $result = $this->attendanceService->processAttendance($request->uid, $fotoPath);

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message']
        ], $result['code']);
    }

    /**
     * Endpoint untuk mode Registrasi KTP (Temporary)
     */
    public function scanForRegistration(Request $request)
    {
        $request->validate([
            'uid' => 'required|string',
        ]);

        // Simpan UID ke tabel temporary
        $scanned = ScannedUid::updateOrCreate(
            ['id' => 1], // Kita asumsikan hanya butuh 1 baris record terbaru
            ['uid' => $request->uid]
        );

        // Trigger Event Real-time ke Browser Admin
        event(new UidScanned($request->uid));

        return response()->json([
            'status' => 'success',
            'message' => 'UID berhasil ditangkap untuk registrasi.'
        ], 200);
    }
}