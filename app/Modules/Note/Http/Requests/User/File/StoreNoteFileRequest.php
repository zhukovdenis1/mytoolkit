<?php

declare(strict_types=1);

namespace App\Modules\Note\Http\Requests\User\File;

use App\Http\Requests\BaseFormRequest;
use App\Modules\Note\Validators\NoteFileValidator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

class StoreNoteFileRequest extends BaseFormRequest
{
    private array $rules =  ['_' => 'required'];

    public function __construct()
    {
        $types = array_keys(NoteFileValidator::getFileTypes());
        $this->rules =  [
            'store_id' => 'required|integer|in:1',
            'link'     => 'nullable|string|max:1024|url|required_without:file',
            'file'     => 'nullable|file', // Базовая проверка, детали в prepareForValidation
            'type'     => 'required|string|in:' . implode(',', $types),
        ];
        parent::__construct();
    }

    public function authorize(): bool
    {
        return $this->verifyUser($this->route('note'));
    }

    public function rules(): array
    {
        return $this->rules;
    }

    /**
     * Подготавливает данные перед валидацией.
     */
    protected function prepareForValidation(): void
    {
        // Если передан link, скачиваем файл и добавляем его в request как file
        if ($this->has('link') && !$this->hasFile('file')) {
            $this->downloadFileFromLink();
        }
    }

    /**
     * Скачивает файл по ссылке и добавляет его в request как file.
     *
     * @throws \Exception
     */
    private function downloadFileFromLink(): void
    {
        $link = $this->input('link');
        $type = $this->input('type');


        $validator = Validator::make(['link' => $link], [
            'link' => $this->rules['link'],
        ]);

        if ($validator->fails()) {
            throw new \Exception('Invalid link URL.');
        }

        $validator = Validator::make(['type' => $type], [
            'type' => $this->rules['type'],
        ]);

        if ($validator->fails()) {
            throw new \Exception('Invalid type');
        }

        if (strpos($link, 'disk.yandex') !== false) {//yandexdisc
            $fileName = basename($link).'.png';
            $yaJson = $this->curlParse( "https://cloud-api.yandex.net:443/v1/disk/public/resources/download?public_key=" . urlencode( $link ), array() );
            if ($yaJson) {
                $yaImgData = json_decode($yaJson, true);
                $link = $yaImgData['href'] ?? '';
            } else {
                $link = '';
                $fileName = '';
            }
            //$fileExt = 'png';
        } else {
            //$fileExt = pathinfo(parse_url($link, PHP_URL_PATH), PATHINFO_EXTENSION);
            $fileName = basename($link);
        }


        // Скачиваем файл по ссылке
        $tempFile = tempnam(sys_get_temp_dir(), 'linkfile');

        if ($link) {
            file_put_contents($tempFile, file_get_contents(
                $link,
                false,
                stream_context_create([
                    'http' => [
                        'timeout' => 20, // Таймаут в секундах
                        'max_length' => NoteFileValidator::getMaxFileSize(), // Максимальный размер файла в байтах
                    ]
                ])));
            // Создаем объект UploadedFile из скачанного файла
            $uploadedFile = new UploadedFile(
                $tempFile, // Путь к временному файлу
                $fileName, // Оригинальное имя файла
                mime_content_type($tempFile), // MIME-тип файла
                UPLOAD_ERR_OK, // Код ошибки (0, если ошибок нет)
                true // Не перемещать файл
            );

            // Добавляем файл в request
            $this->files->set('file', $uploadedFile);
        } else {
            $uploadedFile = null;
        }

        // Валидируем скачанный файл
        $fileValidator = new NoteFileValidator();
        $fileValidator->validate($uploadedFile, $type);
    }

    private function curlParse(string $url, array $postParams = array()) : string|false
    {
        $cookieDir = sys_get_temp_dir();
        $connects = array(
            0 => array(
                'i' => 0,
                'useragent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36 OPR/104.0.0.0 (Edition Yx 05)',
                'referer' => 'https://yandex.ru/',
            ),
            1 => array(
                'i' => 1,
                'useragent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101 Firefox/91.0 SeaMonkey/2.53.17.1',
                'referer' => 'https://google.com/',
            ),
            2 => array(
                'i' => 2,
                'useragent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
                'referer' => 'https://mail.ru/',
            ),
            3 => array(
                'i' => 3,
                'useragent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36 Edg/119.0.0.0',
                'referer' => 'https://mail.ru/',
            ),
        );

        $connect = $connects[mt_rand(0,3)];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, $connect['useragent']);//Юзер агент
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);//Автоматом идём по редиректам
        //curl_setopt($ch, CURLOPT_REFERER, $connect['referer']);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_COOKIEFILE,$cookieDir . '/cookie' . $connect['i'].'.txt' );//запись
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieDir . '/cookie' . $connect['i'].'.txt' );//чтение
        curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);

        if ($postParams)
        {
            $postString = http_build_query($postParams);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $postString);
        }

        $content = curl_exec($ch);

        curl_close($ch);

        return $content;
    }
}
