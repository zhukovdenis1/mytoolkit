<?php

declare(strict_types=1);

namespace App\Modules\Patient\Jobs;

use App\Modules\Patient\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPatient implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Patient $patient) {}

    public function handle()
    {
        // Логика обработки пациента
    }
}
