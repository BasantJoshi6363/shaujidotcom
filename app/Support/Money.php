<?php

namespace App\Support;

/**
 * All money is stored as integer paisa (100 paisa = 1 rupee). No floats anywhere.
 */
class Money
{
    /** "1,500" or "1500.50" -> 150050. Returns null if the text is not a valid amount. */
    public static function toPaisa(string $input): ?int
    {
        $clean = str_replace([',', ' '], '', trim($input));

        if (!preg_match('/^(\d{1,8})(?:\.(\d{1,2}))?$/', $clean, $m)) {
            return null;
        }

        return ((int) $m[1]) * 100 + (int) str_pad($m[2] ?? '0', 2, '0');
    }

    /** 150050 -> "NPR 1,500.50", 150000 -> "NPR 1,500" */
    public static function format(int $paisa): string
    {
        $abs = abs($paisa);
        $rupees = number_format(intdiv($abs, 100));
        $rest = $abs % 100;

        return ($paisa < 0 ? '-' : '') . 'NPR ' . $rupees . ($rest ? '.' . str_pad((string) $rest, 2, '0', STR_PAD_LEFT) : '');
    }
}