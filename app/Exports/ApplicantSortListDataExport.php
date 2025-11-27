<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ApplicantSortListDataExport implements FromView
{
    protected $data;
    protected $fiscalYears;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('excel.applicantSortListExcelDownload', [
            'data' => $this->data,
        ]);
    }
}