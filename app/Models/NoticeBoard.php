<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NoticeBoard extends Model
{
    use HasFactory;

    protected $table = 'notice_boards';
    protected $fillable = ['title', 'slug', 'subject', 'author', 'notice_to', 'notice', 'file'];


    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'title' => 'required',
            'subject' => 'required',
            'author' => 'required',
            'notice_to' => 'required',
            'notice' => 'required',
        ]);
        return $validate;
    }
}
