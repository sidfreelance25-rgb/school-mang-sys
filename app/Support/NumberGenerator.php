<?php

namespace App\Support;

use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Str;

class NumberGenerator
{
    public static function admissionNumber(): string
    {
        do {
            $number = 'ADM'.now()->format('Y').Str::padLeft((string) random_int(1, 9999), 4, '0');
        } while (Student::query()->where('admission_no', $number)->exists());

        return $number;
    }

    public static function employeeId(string $prefix = 'TCH'): string
    {
        do {
            $number = $prefix.now()->format('y').Str::padLeft((string) random_int(1, 999), 3, '0');
        } while (Teacher::query()->where('employee_id', $number)->exists());

        return $number;
    }

    public static function invoiceNumber(): string
    {
        do {
            $number = 'INV'.now()->format('Ymd').Str::padLeft((string) random_int(1, 9999), 4, '0');
        } while (FeeInvoice::query()->where('invoice_no', $number)->exists());

        return $number;
    }

    public static function receiptNumber(): string
    {
        do {
            $number = 'RCP'.now()->format('Ymd').Str::padLeft((string) random_int(1, 9999), 4, '0');
        } while (FeePayment::query()->where('receipt_no', $number)->exists());

        return $number;
    }
}
