<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BaseService
{
    /**
     * References model.
     */
    protected Model $model;

    /**
     * Store a newly created resource in storage.
     *
     * @param Model $model
     */
    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param array $attributes
     * @return Model $model
     */
    public function create(array $attributes): Model
    {
        $model = $this->model;

        $model->fill($attributes);
        $model->save();
        $model->refresh();

        return $model;
    }

    /**
     * Find the specified resource in storage.
     *
     * @param int $id
     * @return mixed $model
     */
    public function find(int $id): mixed
    {
        return $this->model->where('id', $id)->first();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param array $attributes
     * @param int $id
     * @return mixed
     */
    public function update(array $attributes, int $id): mixed
    {
        $model = $this->find($id);
        $model->update($attributes);
        $model->refresh();

        return $model;
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param int $id
     * @return mixed
     */
    public function destroy(int $id): mixed
    {
        return $this->find($id)->delete();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param array $ids
     * @return int
     */
    public function massDestroy(array $ids): int
    {
        return $this->model->destroy($ids);
    }

    /**
     * Prepare validated data with nullable fields.
     *
     * @param array $validated
     * @param array $nullableFields
     * @return array
     */
    protected function prepareUpdateValidated(array $validated, array $nullableFields): array
    {
        foreach ($nullableFields as $nullableField) {
            if (!isset($validated[$nullableField])) {
                $validated[$nullableField] = null;
            }
        }

        return $validated;
    }

    /**
     * Remove the specified file from the folder.
     *
     * @param $url
     * @param string $folderName
     * @return bool
     */
    public function deleteFile($url, string $folderName): bool
    {
        if ($url !== null) {
            $deleteFileName = explode('/', $url);
            $deleteFileName = $deleteFileName[count($deleteFileName) - 1];
            Storage::disk('yandexCloud')->delete($folderName . '/' . $deleteFileName);

            return true;
        }

        return false;
    }

    /**
     * Store the specified file in the folder and return path.
     *
     * @param UploadedFile $file
     * @param string $folderName
     * @param string $customFileName
     * @return string
     */
    public function addFile(UploadedFile $file, string $folderName, string $customFileName = ''): string
    {
        $yandexS3Url = config('inStudy.yandex_s3_url');

        if ($customFileName !== '') {
            $fileName = $customFileName . '.' . $file->getClientOriginalExtension();
        } else {
            $fileName = time() . '.' . $file->getClientOriginalExtension();
        }

        $filePath = Storage::disk('yandexCloud')->putFileAs($folderName, $file, $fileName);

        return $yandexS3Url . $filePath;
    }

    /**
     * Return specified error message
     *
     * @param string $key
     * @param string $message
     * @return \array[][]
     */
    public function getErrorMessage(string $key, string $message): array
    {
        return [
            'errors' => [
                $key => [
                    $message
                ]
            ]
        ];
    }
}
