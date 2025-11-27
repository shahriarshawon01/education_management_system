<?php
use App\Http\Controllers\BillManagement\BillableStudentController;

Route::middleware(['monthly-billable.token'])->group(function () {
    Route::post('/students/billable', [BillableStudentController::class, 'getBillableStudents']);
});