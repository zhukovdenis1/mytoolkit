<?php

declare(strict_types=1);

namespace App\Modules\Patient\Repositories;

use App\Modules\Patient\Models\Patient;
use Illuminate\Support\Facades\Cache;

class PatientRepository
{
    private int $cacheTtl;
    private int $cacheLimit;

    public function __construct()
    {
        $this->cacheTtl = config('patient.list_cache_ttl');
        $this->cacheLimit = config('patient.list_cache_limit');
    }
    public function get(): array
    {
//        Можно получать форматированные данные средствами SQL
//
//        $data = Cache::remember('patients_list', $this->cacheTtl, function () {
//            return Patient::query()
//                ->selectRaw("CONCAT(first_name, ' ', last_name) as name,
//                     DATE_FORMAT(birthdate, '%d.%m.%Y') as birthdate,
//                     CONCAT(age, ' ', age_type) as age")
//                ->limit($this->cacheLimit)
//                ->get()
//                ->toArray();
//        });

        /**
         * Можно хранить данные в кеше уже в готовом виде (name,age,birthdate) или универсальном (как здесь) и
         * делать преобразование уже при выводе данных.
         *
         * Можно хранить в кэше коллекцию для удобства, но здесь массив для экономии места
         */
        $data = Cache::remember('patients_list', $this->cacheTtl, function () {
            return Patient::query()
                ->limit($this->cacheLimit)
                ->get()
                ->toArray();
        });

        return $data;
    }

    public function save(Patient $patient): Patient
    {
        $patient->save();
        $patient->refresh();

        return $patient;
    }
}
