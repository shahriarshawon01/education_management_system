<?php

namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Canteen\CanteenMenuRoster;
use App\Helpers\Helper;

class CanteenMenuRosterController extends Controller
{
    use Helper;
    public function __construct()
    {
        $this->model = new CanteenMenuRoster();
    }

    public function index()
    {
        if (!can('canteen_menu_rostering_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $data = $this->model->with('mealTime:id,name', 'menuItem:id,item_name')
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('month', 'Like', "%$keyword%")
                        ->orWhere('menu_date', 'Like', "%$keyword%");
                })
                ->orderBy('id', 'desc')
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
        if (!can('canteen_menu_rostering_add')) {
            return $this->notPermitted();
        }

        try {
            $input = $request->all();
            $validate = $this->model->validate($input);

            if ($validate->fails()) {
                return returnData(5000, $validate->errors());
            }

            $menuItems = [];
            foreach ($input['set_canteen_menu'] as $menuItem) {
                $menuItems[] = [
                    'year' => $input['year'],
                    'month' => $input['month'],
                    'menu_date' => $input['menu_date'],
                    'menu_start_date' => $input['menu_date'],
                    'week_number' => $input['week_number'],
                    'day' => $input['day'],
                    'meal_time_id' => $menuItem['canteen_component_id'],
                    'menu_item_id' => $menuItem['menu_item_id'],
                    'price' => $menuItem['price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $this->model->insert($menuItems);

            return returnData(2000, null, 'Successfully Inserted');

        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data = CanteenMenuRoster::with(['MealTime', 'menuItem'])->findOrFail($id);

        if (!$data) {
            return returnData(4000, 'Data not found');
        }

        $resultData = [
            'id' => $data->id,
            'year' => $data->year,
            'month' => $data->month,
            'day' => $data->day,
            'menu_date' => $data->menu_date,
            'menu_start_date' => $data->menu_start_date,
            'week_number' => $data->week_number,
            'menu_item_id' => $data->menuItem->item_name,
            'canteen_component_id' => $data->MealTime->name,
            'price' => $data->price,
        ];

        return returnData(2000, $resultData);
    }

    public function update(Request $request, $id)
    {
        if (!can('canteen_menu_rostering_update')) {
            return $this->notPermitted();
        }
        try {
            $input = $request->all();
            $canteenMenu = $request->set_canteen_menu[0];

            $validate = $this->model->validate($input);

            if ($validate->fails()) {
                return returnData(5000, $validate->errors());
            }

            $canteenMenuRoster = CanteenMenuRoster::findOrFail($id);

            if (!$canteenMenuRoster) {
                return returnData(5000, null, 'Record Not Found');
            }

            $canteenMenuRoster->update([
                'year' => $input['year'],
                'month' => $input['month'],
                'menu_date' => $input['menu_date'],
                'menu_start_date' => $input['menu_date'],
                'week_number' => $input['week_number'],
                'day' => $input['day'],

                'canteen_component_id' => $canteenMenu['canteen_component_id'],
                'menu_item_id' => $canteenMenu['menu_item_id'],
                'price' => $canteenMenu['price'],
            ]);

            return returnData(2000, null, 'Successfully Updated');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('canteen_menu_rostering_delete')) {
            return $this->notPermitted();
        }
        try {
            $data = $this->model->where('id', $id)->first();
            if ($data) {
                $data->delete();
                return returnData(2000, $data, 'Successfully Deleted');
            }
            return returnData(5000, null, 'Data Not found');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }
}
