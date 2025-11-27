<?php

namespace App\Models\MigrationData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MigrationModel extends Model
{
    public $timestamps = false;
    // use => users/profiles/teachers/staff/designation/department table
    protected $connection = 'old_edu';
    // protected $connection = 'new_edu';

    public function __construct($type = null)
    {
        parent::__construct();

        $this->setTable($type);
    }
}
