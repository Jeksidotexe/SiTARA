<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan_pelanggaran_kampanye', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->unsignedBigInteger('id_operator');
            $table->unsignedBigInteger('id_pimpinan')->nullable();
            $table->date('tanggal_laporan');
            $table->string('judul');

            // LAPORAN PUSKOMIN
            $table->text('deskripsi');

            // A. Penyelenggaraan Pemerintah Daerah
            $table->text('narasi_a')->nullable();
            $table->json('file_a')->nullable();

            // B. Pelaksanaan Program Pembangunan
            $table->text('narasi_b')->nullable();
            $table->json('file_b')->nullable();

            // C. Pelayanan Publik
            $table->text('narasi_c')->nullable();
            $table->json('file_c')->nullable();

            // D. Ideologi
            $table->text('narasi_d')->nullable();
            $table->json('file_d')->nullable();

            // E. Politik
            $table->text('narasi_e')->nullable();
            $table->json('file_e')->nullable();

            // F. Ekonomi
            $table->text('narasi_f')->nullable();
            $table->json('file_f')->nullable();

            // G. Sosial Budaya
            $table->text('narasi_g')->nullable();
            $table->json('file_g')->nullable();

            // H. Hankam
            $table->text('narasi_h')->nullable();
            $table->json('file_h')->nullable();

            $table->text('penutup');

            $table->enum('status_laporan', ['menunggu verifikasi', 'revisi', 'disetujui']);
            $table->text('catatan')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            // Relasi
            $table->foreign('id_operator')->references('id_users')->on('users');
            $table->foreign('id_pimpinan')->references('id_users')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_pelanggaran_kampanye');
    }
};
