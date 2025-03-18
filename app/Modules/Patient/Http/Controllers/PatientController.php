<?php

declare(strict_types=1);

namespace App\Modules\Patient\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\Patient\Http\Requests\PatientRequest;
use App\Modules\Patient\Http\Resources\PatientListResource;
use App\Modules\Patient\Http\Resources\PatientResource;
use App\Modules\Patient\Services\PatientService;


class PatientController extends BaseController
{
    public function __construct(private readonly PatientService $service) {}

    public function store(PatientRequest $request): PatientResource
    {
        $patient = $this->service->createPatient($request->validated());
        return new PatientResource($patient);
    }

    public function index(): PatientListResource
    {
        return new PatientListResource($this->service->getPatients());
    }
}
