<?php

declare(strict_types=1);

namespace App\Helpers;
use DateTime;
use IntlDateFormatter;
use InvalidArgumentException;

class DateTimeHelper
{
    /**
     * Возвращает название месяца на русском в указанном падеже
     *
     * @param int $monthNumber Номер месяца (1-12)
     * @param string $case Падеж: 'nominative' (именительный) или 'genitive' (родительный)
     * @return string Название месяца
     * @throws InvalidArgumentException Если номер месяца или падеж указаны неверно
     */
    function getMonthName(int $monthNumber, string $case = 'genitive'): string
    {
        // Проверка корректности номера месяца
        if ($monthNumber < 1 || $monthNumber > 12) {
            throw new InvalidArgumentException('Номер месяца должен быть от 1 до 12');
        }

        // Определяем паттерн в зависимости от падежа
        $pattern = ($case === 'nominative') ? 'LLLL' : 'MMMM';

        // Создаем форматтер
        $formatter = new IntlDateFormatter(
            'ru_RU',
            IntlDateFormatter::FULL,
            IntlDateFormatter::FULL,
            null,
            null,
            $pattern
        );

        // Создаем дату с нужным месяцем (год и день не важны)
        $date = DateTime::createFromFormat('!m', str_pad((string)$monthNumber, 2, '0', STR_PAD_LEFT));

        return $formatter->format($date);
    }
}
