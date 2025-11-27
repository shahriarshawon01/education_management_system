<?php

namespace App\Models\Library;

use App\Models\Scopes\ModelScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class StockBooks extends Model
{
    protected $table = 'stock_books';
    protected $fillable = ['school_id','book_accession_id','stock_date','net_price','purchase_price','discount','quantity','available_quantity'];
    use ModelScopes;
    use HasFactory;
    public function validate($input = [])
    {
        $validate = Validator::make($input, [
            'book_accession_id' => 'required',
            'stock_date' => 'required',
            'purchase_price' => 'required',
            'quantity' => 'required',
        ]);
        return $validate;
    }

    public function booksInfo()
    {
        return $this->belongsTo(BookAccession::class, 'book_id','id');
    }
}
