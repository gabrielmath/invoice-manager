<?php

use App\Models\Invoice;
use Illuminate\Support\Str;

if (!function_exists('generate_number')) {
    function generate_number(int $length = 9): string
    {
        $number = Str::random($length);

        while (Invoice::whereNumber($number)->exists()) {
            $number = Str::random($length);
        }

        return $number;
    }
}

if (!function_exists('mask')) {
    function mask($value, $mask): string
    {
        $masked = '';
        $k = 0;

        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($value[$k])) {
                    $masked .= $value[$k++];
                }
            } else {
                if (isset($mask[$i])) {
                    $masked .= $mask[$i];
                }
            }
        }
        return $masked;
    }
}
