<?php

declare(strict_types=1);

namespace App\Modules\Console\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Modules\Console\Http\Requests\User\RunCommandRequest;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;


class ConsoleController extends BaseController
{
    public function runCommand(RunCommandRequest $request)//: \Illuminate\Http\JsonResponse
    {

        $command = $request->category ? $request->category . ':' . $request->command : $request->command;

        $allData = $request->all();

        $symfonyCommand = Artisan::all()[$command];

        $arguments = $symfonyCommand->getDefinition()->getArguments();
        $options = $symfonyCommand->getDefinition()->getOptions();
        $argumentNames = array_map(fn (InputArgument $arg) => $arg->getName(), $arguments);
        $optionNames = array_map(fn (InputOption $arg) => $arg->getName(), $options);

        $filteredArguments = array_filter($allData, function($key) use ($argumentNames) {
            return in_array($key, $argumentNames);
        }, ARRAY_FILTER_USE_KEY);

        $filteredOptions = array_filter($allData, function($key) use ($optionNames) {
            return in_array($key, $optionNames);
        }, ARRAY_FILTER_USE_KEY);



        $params = array_merge($filteredArguments, $this->addPrefixToKeys($filteredOptions));

        Artisan::call($command, $params);

        // Получение вывода команды
        $output = Artisan::output();

        // Возврат вывода в ответе
//        return response()->json([
//            'output' => $output,
//        ]);
        // Возврат HTML-вывода
        return response($output, 200)
            ->header('Content-Type', 'text/html');
    }

    private function addPrefixToKeys(array $array, string $prefix = '--'): array
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            $newKey = $prefix . $key;
            $newArray[$newKey] = $value;
        }
        return $newArray;
    }
}
