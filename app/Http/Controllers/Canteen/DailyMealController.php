<?php

namespace App\Http\Controllers\Canteen;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\Canteen\DailyMeal;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DailyMealController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new DailyMeal();
    }

    public function index()
    {
        if (!can('daily_meal_members_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');

            $data = $this->model
                ->with([
                    'teachers:id,name,employee_id',
                    'staffs:id,name,employee_id',
                    'students:id,student_name_en,student_roll',
                    'guests:id,guest_id,guest_name',
                ])
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->whereHas('students', function ($query) use ($keyword) {
                            $query->where('student_name_en', 'like', "%$keyword%");
                        })
                            ->orWhereHas('teachers', function ($query) use ($keyword) {
                                $query->where('name', 'like', "%$keyword%");
                            })
                            ->orWhereHas('staffs', function ($query) use ($keyword) {
                                $query->where('name', 'like', "%$keyword%");
                            })
                            ->orWhereHas('guests', function ($query) use ($keyword) {
                                $query->where('guest_name', 'like', "%$keyword%");
                            });
                    });
                })
                ->paginate(input('perPage'));

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
        if (!can('daily_meal_members_add')) {
            return $this->notPermitted();
        }

        try {
            $input = $request->all();
            $validate = $this->model->validate($input);

            if ($validate->fails()) {
                return returnData(2000, $validate->errors());
            }

            $consumerType = $input['consumer_type'];
            if ($consumerType === 'guest') {
                do {
                    $newGuestId = mt_rand(100000, 999999);
                } while (Guest::where('guest_id', $newGuestId)->exists());

                $guest = Guest::create([
                    'guest_id' => $newGuestId,
                    'guest_name' => $input['guest_name'] ?? null,
                    'email' => $input['email'] ?? null,
                    'guest_institute' => $input['guest_institute'] ?? null,
                    'phone' => $input['guest_phone'] ?? null,
                    'nid' => $input['guest_nid'] ?? null,
                    'guest_department' => $input['guest_department'] ?? null,
                    'guest_designation' => $input['guest_designation'] ?? null,
                ]);

                $consumerId = $guest->id;
            } else {
                $exists = $this->model
                    ->where('consumer_type', $consumerType)
                    ->where('consumer_id', $input['consumer_id'])
                    ->whereIn('meal_date', $input['meal_dates'] ?? [])
                    ->exists();

                if ($exists) {
                    return returnData(5000, null, "$consumerType already has meal assigned for selected date(s).");
                }

                $consumerId = $input['consumer_id'];
            }

            foreach ($input['meal_dates'] as $date) {
                $this->model->create([
                    'consumer_type' => $consumerType,
                    'consumer_id' => $consumerId,
                    'meal_date' => date('Y-m-d', strtotime($date)),
                    'meal_time' => json_encode($input['meal_time']),
                    'meal_count' => count($input['meal_time']),
                    'meal_day' => date('l', strtotime($date)),
                ]);
            }

            return returnData(2000, null, 'Meal assigned successfully.');

        } catch (\Exception $e) {
            return returnData(5000, $e->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function show($id)
    {
        //
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
        if (!can('daily_meal_members_delete')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model->where('id', $id)->first();
            if ($data) {
                $data->delete();
                return returnData(2000, $data, 'Successfully Deleted');
            }
            return returnData(5000, null, 'Record not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function getMealTimes()
    {
        $components = DB::table('canteen_components')
            ->where('status', 1)
            ->select('id', 'name')
            ->get();

        return response()->json(['status' => 1, 'data' => $components]);
    }
}
