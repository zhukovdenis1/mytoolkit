<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ErrorException;
use App\Models\BaseModel;

class BaseService
{
    protected BaseModel $model;


//    public function setModel(Model $model): void
//    {
//        $this->model = $model;
//    }

    public function save(BaseModel $model, array $attributes): BaseModel
    {
        $model->fill($attributes);
        $model->save();
        $model->refresh();

        return $model;
    }

    public function update(BaseModel $model, array $attributes): BaseModel
    {
        $model->update($attributes);
        if (!$model->wasChanged()) {
            throw new ErrorException('Data was not changed');
        }
        $model->refresh();

        return $model;
    }


    public function destroy(BaseModel $model): true
    {
        if (!$model->delete()) {
            throw new ErrorException('Delete failed');
        }

        return true;
    }


//    public function massDestroy(array $ids): int
//    {
//        return $this->model->destroy($ids);
//    }

}
