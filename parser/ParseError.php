<?php

enum ParserError: int
{
    case EmptyIdQueue = 1;//
    case NotFound = 2;
    case WrongPage = 3;
    case OutOfStock = 4;
    case ValidationError = 5;

    case SQLError = 6;

    public function message(): string
    {
        return match ($this) {
            self::EmptyIdQueue => 'Не передан id queue',
            self::NotFound   => 'Страница не найдена: 404',
            self::WrongPage  => 'Не является детальной страницей товара',
            self::OutOfStock => 'Товар закончился',
            self::ValidationError => 'Ошибка валидации полученных данных',
            self::SQLError => 'Ошибка сохранения данных в бд'
        };
    }

    // Новый метод: получение сообщения по коду ошибки
    public static function getMessageByCode(int $code): string
    {
        $case = self::tryFrom($code); // Пытаемся получить enum-case по значению
        if ($case === null) {
            throw new InvalidArgumentException("Неизвестный код ошибки: $code");
        }
        return $case->message(); // Возвращаем сообщение
    }
}
