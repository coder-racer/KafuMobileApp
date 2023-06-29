<?php
function view($filename): false|string
{
    $viewPath = viewDir() . '/' . $filename . '.php';

    if (file_exists($viewPath)) {
        ob_start();
        include $viewPath;
        return ob_get_clean();
    }

    return 'View not found';
}

function appDir(): string
{
    return baseDir() . '/app';
}

function storageDir(): string
{
    return baseDir() . '/storage';
}

function viewDir(): string
{
    return baseDir() . '/view';
}

function baseDir(): string
{
    $dir = str_replace("/app/Core", "", __dir__);
    $dir = str_replace("\app\Core", "", $dir);
    $dir = str_replace("\app\core", "", $dir);
    $dir = str_replace("/app/core", "", $dir);
    return normalizeSlashes($dir);
}

function normalizeSlashes($string): array|string
{
    $string = str_replace('/', '\\', str_replace('\\', '/', $string));
    return str_replace('\\', '/', $string);
}

function dump($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

function dd($data): void
{
    dump($data);
    die();
}

function env($key): mixed
{
    static $env = null;

    if ($env === null) {
        $env = parse_ini_file(baseDir() . '/' . '.env');
    }

    return $env[$key] ?? false;
}

function getPhoneFromString($string): string
{
    $phoneNumber = preg_replace("/\D/", "", $string);
    $firstDigit = substr($phoneNumber, 0, 1);
    if ($firstDigit == '7' || $firstDigit == '8') {
        $phoneNumber = '+7' . substr($phoneNumber, 1);
    }
    return $phoneNumber;
}

function validatePhone($string)
{
    $phoneNumber = getPhoneFromString($string);
    $phoneNumber = str_replace('+7', '', $phoneNumber);
    if (strlen($phoneNumber) == 10)
        return true;
    return false;
}

function formatPhoneNumber($phoneNumber)
{
    // Удаляем все символы, кроме цифр
    $phoneNumber = preg_replace("/\D/", "", $phoneNumber);

    // Проверяем длину номера и первую цифру
    if (strlen($phoneNumber) == 11 && $phoneNumber[0] == '7') {
        // Меняем формат номера
        return preg_replace("/(\d)(\d{3})(\d{3})(\d{2})(\d{2})/", "+$1 ($2) $3-$4-$5", $phoneNumber);
    } else {
        return false; // или любая другая логика обработки ошибок
    }
}

function currentTime()
{
    return time() + env("TIME_DELAY");
}