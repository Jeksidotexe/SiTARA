<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NotifikasiBackup;

class BackupController extends Controller
{
    public function index()
    {
        $disk = Storage::disk('google');
        $files = [];

        $backupName = config('backup.backup.name');

        try {
            if ($disk) {
                $rawFiles = $disk->allFiles($backupName);

                rsort($rawFiles);

                foreach ($rawFiles as $file) {
                    if (substr($file, -4) == '.zip') {
                        $files[] = [
                            'path' => $file,
                            'name' => basename($file),
                            'size' => $this->formatSize($disk->size($file)),
                            'date' => Carbon::createFromTimestamp($disk->lastModified($file))->locale('id')->diffForHumans(),
                            'raw_date' => $disk->lastModified($file)
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Gagal mengambil list backup: " . $e->getMessage());
        }

        return view('dashboard.admin.backup.index', compact('files'));
    }

    public function runBackup()
    {
        try {
            set_time_limit(300); // 5 Menit
            ini_set('memory_limit', '512M');

            $exitCode = Artisan::call('backup:run', [
                '--only-db' => true,
                '--disable-notifications' => true
            ]);

            $output = Artisan::output();
            Log::info("Backup Output: " . $output);

            if ($exitCode !== 0) {
                $errorMessage = 'Backup gagal. Cek log.';

                if (str_contains(strtolower($output), 'mysqldump') && (str_contains(strtolower($output), 'not found') || str_contains(strtolower($output), 'is not recognized'))) {
                    $errorMessage = 'Sistem tidak dapat menemukan "mysqldump". Cek path Environment.';
                } elseif (str_contains($output, 'Connection refused') || str_contains($output, 'Access denied')) {
                    $errorMessage = 'Gagal terhubung ke database. Cek .env.';
                }

                throw new \Exception($errorMessage);
            }

            $msg = 'Berhasil backup database ke Google Drive.';

            /** @var User $user */
            $user = Auth::user();

            try {
                if ($user) {
                    $user->notify(new NotifikasiBackup('success', $msg));
                }
            } catch (\Exception $e) {
                Log::error("Gagal mengirim notifikasi broadcast: " . $e->getMessage());
            }

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            Log::error("Backup Error: " . $e->getMessage());

            /** @var User $user */
            $user = Auth::user();

            try {
                if ($user) {
                    $user->notify(new NotifikasiBackup('error', 'Gagal Backup: ' . $e->getMessage()));
                }
            } catch (\Exception $broadcastError) {
                Log::error("Gagal mengirim notifikasi error: " . $broadcastError->getMessage());
            }

            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function download(Request $request)
    {
        $path = $request->query('path');

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('google');

        if ($disk->exists($path)) {
            return $disk->download($path);
        }

        return back()->with('error', 'File tidak ditemukan di Google Drive.');
    }

    public function destroy(Request $request)
    {
        $path = $request->query('path');

        try {
            if (Storage::disk('google')->exists($path)) {
                Storage::disk('google')->delete($path);
                return back()->with('success', 'File backup berhasil dihapus dari Google Drive.');
            }
            return back()->with('error', 'File tidak ditemukan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus file: ' . $e->getMessage());
        }
    }

    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }
}
