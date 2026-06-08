<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateSchedulePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class PdfExportController extends Controller
{

    public function export(Request $request)
    {
        $userId = Auth::id();

        Cache::put("pdf_status_{$userId}", 'pending', 3600);

        GenerateSchedulePdf::dispatch($userId);

        return redirect()->route('dashboard')
            ->with('status', 'PDF programı kuyruğa eklendi. Hazırlandığında indirme linki belirecektir.');
    }

    public function download()
    {
        $userId = Auth::id();
        $fileName = Cache::get("pdf_path_{$userId}");

        if (!$fileName) {
            return redirect()->route('dashboard')
                ->with('error', 'İndirilecek program dosyası bulunamadı. Lütfen önce PDF çıktısı alın.');
        }

        $fileName = basename($fileName);
        $filePath = storage_path('app/public/schedules/' . $fileName);

        if (!File::exists($filePath)) {

            Cache::forget("pdf_status_{$userId}");
            Cache::forget("pdf_path_{$userId}");

            return redirect()->route('dashboard')
                ->with('error', 'Dosya sunucuda bulunamadı. Lütfen programı tekrar üretin.');
        }

        return response()->download($filePath, 'sinav_programi.pdf', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="sinav_programi.pdf"',
        ]);
    }
}
