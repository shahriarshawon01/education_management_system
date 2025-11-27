<?php

namespace App\Http\Controllers\Canteen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class CanteenWeeklyMenuController extends Controller
{
    public function getWeeklyMenu(Request $request)
    {
        $year = $request->year;
        $month = $request->month;

        $data = DB::table('canteen_menu_rosters')
            ->leftJoin('canteen_menu_items', 'canteen_menu_rosters.menu_item_id', '=', 'canteen_menu_items.id')
            ->leftJoin('meal_times', 'canteen_menu_rosters.meal_time_id', '=', 'meal_times.id')
            ->selectRaw(
                "canteen_menu_rosters.*,
            canteen_menu_items.item_name as menu_item_name,
            meal_times.name as canteen_component_name"
            )
            ->when($year, function ($query) use ($year) {
                $query->where('canteen_menu_rosters.year', $year);
            })
            ->when($month, function ($query) use ($month) {
                $query->where('canteen_menu_rosters.month', $month);
            })
            ->orderBy('canteen_menu_rosters.menu_start_date')
            ->get();

        $groupedData = [];
        foreach ($data as $item) {
            $groupedData[$item->week_number][] = $item;
        }

        $responseData = [
            'data' => $groupedData,
        ];

        return returnData(2000, $responseData);
    }


    public function downloadWeeklyMenu(Request $request, $month, $week)
    {
        $logHeader = $request->header('LogData');

        // Only call insertActivityLog if header exists and is valid JSON
        if ($logHeader) {
            $logData = json_decode($logHeader);

            if (is_object($logData) && isset($logData->method)) {
                $this->insertActivityLog($request);
            }
        }

        // dd("pp");
        $menuItems = DB::table('canteen_menu_rosters')
            ->leftJoin('canteen_menu_items', 'canteen_menu_rosters.menu_item_id', '=', 'canteen_menu_items.id')
            ->leftJoin('meal_times', 'canteen_menu_rosters.meal_time_id', '=', 'meal_times.id')
            ->selectRaw("
            canteen_menu_rosters.*,
            canteen_menu_items.item_name as menu_item_name,
            meal_times.name as canteen_component_name")
            ->where('week_number', $week)
            ->where('month', $month)
            ->orderBy('menu_date')
            ->get();

        // dd($menuItems);

        if ($menuItems->isEmpty()) {
            return returnData(5000, 'No data found.');
        }

        $groupedMenus = [];

        $startDate = null;
        $endDate = null;

        foreach ($menuItems as $item) {
            $date = $item->menu_date;
            $dayName = \Carbon\Carbon::parse($date)->format('l');

            if ($startDate === null || $date < $startDate) {
                $startDate = $date;
            }
            if ($endDate === null || $date > $endDate) {
                $endDate = $date;
            }

            if (!isset($groupedMenus[$date])) {
                $groupedMenus[$date] = [
                    'day' => $dayName,
                    'date' => $date,
                    'breakfast' => [],
                    'lunch' => [],
                    'dinner' => [],
                ];
            }

            $component = strtolower($item->canteen_component_name);
            if ($component === 'breakfast') {
                $groupedMenus[$date]['breakfast'][] = $item;
            } elseif ($component === 'lunch') {
                $groupedMenus[$date]['lunch'][] = $item;
            } elseif ($component === 'dinner') {
                $groupedMenus[$date]['dinner'][] = $item;
            }
        }

        $weekNumber = $menuItems[0]->week_number;
        $month = \Carbon\Carbon::createFromFormat('m', $menuItems[0]->month)->format('F');
        $year = $menuItems[0]->year;

        $weekStartDate = \Carbon\Carbon::parse($startDate)->format('d-m-Y');
        $weekEndDate = \Carbon\Carbon::parse($endDate)->format('d-m-Y');

        switch ($weekNumber) {
            case 1:
                $weekNumberText = 'First Week';
                break;
            case 2:
                $weekNumberText = 'Second Week';
                break;
            case 3:
                $weekNumberText = 'Third Week';
                break;
            case 4:
                $weekNumberText = 'Fourth Week';
                break;
            case 5:
                $weekNumberText = 'Fifth Week';
                break;
            default:
                $weekNumberText = 'Unknown Week';
                break;
        }

        // Render Blade
        $html = view('canteen-menu.canteen_menu_pdf', compact('groupedMenus', 'weekNumber', 'weekNumberText', 'month', 'year', 'weekStartDate', 'weekEndDate'))->render();

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'format' => 'A4-L',
            'fontDir' => array_merge($fontDirs, [resource_path('fonts')]),
            'fontdata' => $fontData + [
                'kalpurush' => [
                    'R' => 'kalpurush.ttf',
                ],
            ],
            'default_font' => 'kalpurush',
            'mode' => 'utf-8',
        ]);

        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $mpdf->SetFont('kalpurush');

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="canteen_menu_week_' . $month . '_' . $weekNumber . '.pdf"');
    }
}
