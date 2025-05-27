<?php

namespace App\Logging;

/**
 * Описывает общий интерфейс логирования
 * Сообщение ДОЛЖНО быть строкой или объектом, реализующим __toString().
 * Контекстный массив может содержать произвольные данные
 *
 */
interface LoggerInterface
{
    /**
     * Чрезвычайная ситуация. Система непригодна для использования.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function emergency(string $message, array $context = []);

    /**
     * Действия должны быть приняты немедленно.
     *
     * Пример: весь веб-сайт недоступен, база данных недоступна и т. д. Это должно вызвать SMS-уведомления и разбудить вас.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function alert(string $message, array $context = []);

    /**
     * Критические условия.
     *
     * Пример: компонент приложения недоступен, неожиданное исключение.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function critical(string $message, array $context = []);

    /**
     * Ошибки времени выполнения, которые не требуют немедленных действий, но обычно должны регистрироваться и отслеживаться.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error(string $message, array $context = []);

    /**
     * Исключительные случаи, не являющиеся ошибками.
     *
     * Пример: использование устаревших API, неправильное использование API, нежелательные вещи, которые не обязательно являются неправильными.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning(string $message, array $context = []);

    /**
     * Обычные, но важные события.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function notice(string $message, array $context = []);

    /**
     * Интересные события.
     *
     * Пример: вход пользователя в систему, запись лога SQL.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function info(string $message, array $context = []);

    /**
     * Подробная отладочная информация.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function debug(string $message, array $context = []);

    /**
     * Логирование с заданным уровнем.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    //public function log($level, $message, array $context = []);
}
