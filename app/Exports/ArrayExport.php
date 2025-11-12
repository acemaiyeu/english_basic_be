<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ArrayExport implements FromArray, WithHeadings, WithTitle
{
    protected $title;
    protected $data;

    public function __construct(array $title, array $data)
    {
        $this->title = $title;
        $this->data = $data;
    }

    // Trả về dữ liệu nội dung
    public function array(): array
    {
        return $this->data;
    }

    // Trả về tiêu đề cột
    public function headings(): array
    {
        return $this->title;
    }

    // (Tuỳ chọn) tên sheet trong Excel
    public function title(): string
    {
        return 'Sheet 1';
    }
}
