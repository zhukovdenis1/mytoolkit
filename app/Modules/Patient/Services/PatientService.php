<?php

declare(strict_types=1);

namespace App\Modules\Patient\Services;

use App\Modules\Patient\Repositories\PatientRepository;
use App\Modules\Patient\Models\Patient;
use Illuminate\Support\Carbon;

class PatientService
{
    public function __construct(private PatientRepository $repository) {}

    public function createPatient(array $data): Patient
    {
        $birthdate = Carbon::parse($data['birthdate']);
        [$age, $type] = $this->calculateAge($birthdate);

        $patient = new Patient([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'birthdate' => $birthdate->toDateString(),
            'age' => $age,
            'age_type' => $type,
        ]);

        return $this->repository->save($patient);
    }

    public function getPatients(): array
    {
        return $this->repository->get();
    }

    private function calculateAge(Carbon $birthdate): array
    {
        $now = Carbon::now();

        if ($birthdate->diffInDays($now) < 30) {
            return [$birthdate->diffInDays($now), 'день'];
        } elseif ($birthdate->diffInMonths($now) < 12) {
            return [$birthdate->diffInMonths($now), 'месяц'];
        } else {
            return [$birthdate->diffInYears($now), 'год'];
        }
    }
}
