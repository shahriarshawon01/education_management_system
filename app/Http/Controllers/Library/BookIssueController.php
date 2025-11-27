<?php

namespace App\Http\Controllers\Library;

use App\Helpers\Helper;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\Library\BookIssue;
use App\Models\Library\Membership;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Support\Facades\Auth;

class BookIssueController extends Controller
{

    use Helper;

    public function __construct()
    {
        $this->model = new BookIssue();
    }


//     public function index()
// {
//     if (!can('book_issue_view')) {
//         return $this->notPermitted();
//     }

//     try {
//         $keyword = request()->input('keyword');
//         $perPage = request()->input('perPage', 15);

//         $data = $this->model
//             ->when($keyword, function ($query) use ($keyword) {
//                 $query->where('id', 'LIKE', "%$keyword%")
//                       ->orWhere('issue_no', 'LIKE', "%$keyword%");
//             })
//             ->orderBy('id', 'DESC')
//             ->paginate($perPage);

//         foreach ($data as $item) {
//             $member = null;
//             $memberName = null;

//             switch ($item->type) {
//                 case '1': // Student
//                     $member = Student::find($item->member_id);
//                     if ($member) {
//                         $item->member_id = $member->id;
//                         $item->member_name = $member->student_name_en;
//                     }
//                     break;

//                 case '2': // Teacher
//                     $member = Teacher::find($item->member_id);
//                     if ($member) {
//                         $item->member_id = $member->id;
//                         $item->member_name = $member->name;
//                     }
//                     break;

//                  case '3': // Teacher
//                     $member = Staff::find($item->member_id);
//                     if ($member) {
//                         $item->member_id = $member->id;
//                         $item->member_name = $member->name;
//                     }
//                     break;

//                  case '4': // Teacher
//                     $member = Membership::find($item->member_id);
//                     if ($member) {
//                         $item->member_id = $member->id;
//                         $item->member_name = $member->name;
//                     }
//                     break;

//                 default:
//                     $item->member_name = null;
//                     $item->member_id = null;
//                     break;
//             }

//             // count returned books
//             $item->returned_books = DB::table('book_issues_details')
//                 ->where('issue_id', $item->id)
//                 ->where('status', 2)
//                 ->count();
//         }

//         return returnData(2000, $data);
//     } catch (\Exception $exception) {
//         return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
//     }
// }

public function index()
{
    if (!can('book_issue_view')) {
        return $this->notPermitted();
    }

    try {
        $keyword = request()->input('keyword');
        $perPage = request()->input('perPage', 15);

        $data = $this->model
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('id', 'LIKE', "%$keyword%")
                      ->orWhere('issue_no', 'LIKE', "%$keyword%");
            })
            ->orderBy('id', 'DESC')
            ->paginate($perPage);

        foreach ($data as $item) {
            $item->member_code = null;
            $item->member_name = null;

            switch ($item->type) {
                case '1': // Student
                    $member = Student::find($item->member_id);
                    if ($member) {
                        $item->member_code = $member->student_roll; // student_roll
                        $item->member_name = $member->student_name_en;
                    }
                    break;

                case '2': // Teacher
                    $member = Teacher::find($item->member_id);
                    if ($member) {
                        $item->member_code = $member->employee_id; // employee_id
                        $item->member_name = $member->name;
                    }
                    break;

                case '3': // Staff
                    $member = Staff::find($item->member_id);
                    if ($member) {
                        $item->member_code = $member->employee_id; // employee_id
                        $item->member_name = $member->name;
                    }
                    break;

                case '4': // Membership
                    $member = Membership::find($item->member_id);
                    if ($member) {
                        $item->member_code = $member->id; // membership id
                        $item->member_name = $member->name;
                    }
                    break;

                default:
                    $item->member_code = null;
                    $item->member_name = null;
                    break;
            }

            // Count returned books
            $item->returned_books = DB::table('book_issues_details')
                ->where('issue_id', $item->id)
                ->where('status', 2)
                ->count();
        }

        return returnData(2000, $data);

    } catch (\Exception $exception) {
        return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
    }
}





    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        if (!can('book_issue_add')) {
            return $this->notPermitted();
        }

        try {
            $input = $request->all();
            $schoolId = auth()->user()->school_id;
            $authUserId = auth()->user()->id;

            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(2000, $validate->errors());
            }

            // Determine member_id from form input
            if ($input['type'] == 1) {
                $member_id = $input['student_id'];   // student id
            } elseif ($input['type'] == 2) {
                $member_id = $input['teacher_id'];   // teacher id
            } elseif ($input['type'] == 3) {
                $member_id = $input['teacher_id'];    // library_membership.id
            }
            elseif ($input['type'] == 4) {
                $member_id = $input['member_id'];    // library_membership.id
            } 
             else {
                $member_id = null;
            }

            // Save book issue
            $this->model->fill($input);
            $this->model->school_id = $schoolId;
            $this->model->member_id = $member_id;
            $this->model->total_books = count($input['book']);
            $this->model->created_by = $authUserId;
            $this->model->save();

            // Save issued books
            foreach ($input['book'] as $book) {
                DB::table('book_issues_details')->insert([
                    'issue_id' => $this->model->id,
                    'book_accession_id' => $book['book_accession_id'],
                    'issue_date' => $request->issue_date,
                    'expected_return' => $request->expected_return,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('stock_books')
                    ->where('book_accession_id', $book['book_accession_id'])
                    ->decrement('available_quantity', 1);
            }

            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }




    public function show($id)
    {
        if (!can('book_issue_view')) {
            return $this->notPermitted();
        }
        $data = DB::table('book_issues_details')
            ->leftJoin('book_accessions', 'book_issues_details.book_accession_id', '=', 'book_accessions.id')
            ->where('issue_id', $id)
            ->select('book_issues_details.*', 'book_accessions.book_title')
            ->get();
        return returnData(2000, $data);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        if (!can('book_issue_delete')) {
            return $this->notPermitted();
        }
        try {
            $bookIssue = $this->model->find($id);
            $bookIssueDetails = DB::table('book_issues_details')
                ->where('issue_id', $id)
                ->get();
            DB::table('book_issues_details')
                ->where('issue_id', $id)
                ->delete();
            foreach ($bookIssueDetails as $detail) {
                DB::table('stock_books')
                    ->where('book_accession_id', $detail->book_accession_id)
                    ->increment('available_quantity', 1);
            }
            $bookIssue->delete();
            return returnData(2000, null, 'Successfully Deleted');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
    public function returnSingleBook(Request $request)
    {
        $id = $request->input('id');
        $data = DB::table('book_issues_details')->where('id', $id)->first();
        if ($data) {
            DB::table('book_issues_details')
                ->where('id', $id)
                ->update(['status' => 2, 'return_date' => now()]);
            return returnData(2000, null, 'Book returned successfully');
        } else {
            return returnData(2000, null, 'Book not found');
        }
    }

    public function returnAllBook(Request $request)
    {
        $id = $request->input('id');
        $data = DB::table('book_issues_details')->where('issue_id', $id)->where('status', 1)->get();
        if ($data) {
            foreach ($data as $bookIssueDetail) {
                DB::table('book_issues_details')
                    ->where('id', $bookIssueDetail->id)
                    ->update(['status' => 2, 'return_date' => now()]);
            }
            return returnData(2000, null, 'Book returned successfully');
        }
        return returnData(2000, null, 'Book not found');
    }
}
