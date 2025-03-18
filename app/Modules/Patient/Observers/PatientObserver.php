<?php

declare(strict_types=1);

namespace App\Modules\Patient\Observers;

use App\Modules\Patient\Models\Patient;
use App\Modules\Patient\Jobs\ProcessPatient;
use Illuminate\Support\Facades\Cache;

class PatientObserver
{
    private int $cacheTtl;

    public function __construct()
    {
        $this->cacheTtl = config('patient.cache_ttl');
    }
    public function created(Patient $patient)
    {
        Cache::put("patient_{$patient->id}", $patient, $this->cacheTtl);
        Cache::forget('patients_list');
        ProcessPatient::dispatch($patient);
    }

    public function updated(Patient $patient): void
    {
        Cache::put("patient_{$patient->id}", $patient, $this->cacheTtl);
        Cache::forget('patients_list');
    }

    public function deleted(Patient $patient): void
    {
        Cache::forget("patient_{$patient->id}");
        Cache::forget('patients_list');
    }
}
