<!DOCTYPE html>
<html>

<head>
    <title>Canteen Menu</title>
    <style>
        body {
            font-family: 'kalpurush', sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table,
        th,
        td {
            border: 1px solid black;
            text-align: center;
        }

        th,
        td {
            padding: 6px;
        }

        th {
            background-color: #f2f2f2;
        }

        h1 {
            text-align: center;
            margin-bottom: 0;
        }

        h3 {
            text-align: center;
            margin-top: 0;
        }

        h2 {
            text-align: center;
        }

        .meal-section {
            margin-bottom: 15px;
        }

        .meal-item {
            text-align: left;
        }
    </style>
</head>

<body>

    <h2 style="text-align: center;">TPSC Cafeteria - Weekly Menu</h2>
    <h3 style="text-align: center;">Joypur Para, Bogura-5800</h3>
    <p style="text-align: center;"><strong>Year :</strong> {{ $year }}</p>
    {{-- <p style="text-align: center;"><strong>Month :</strong>
        {{ \Carbon\Carbon::parse('2025-' . $month . '-01')->format('F') }}</p> --}}
    <p style="text-align: center;"><strong>Month : </strong> {{ $month }}</p>
    <p style="text-align: center;"><strong>Week :</strong> {{ $weekNumber }}</p>
    <p style="text-align: center;"><strong>Week Date :</strong> {{ $weekStartDate }} To {{ $weekEndDate }}</p>

    <table>
        <thead>
            <tr>
                <th rowspan="2">SL</th>
                <th rowspan="2">Day & Date</th>
                <th colspan="3">Breakfast</th>
                <th colspan="3">Lunch</th>
                <th colspan="3">Dinner</th>
            </tr>
            <tr>
                <!-- For Breakfast -->
                <th>Food Item</th>
                <th>Amount</th>
                <th>Total Amount</th>

                <!-- For Lunch -->
                <th>Food Item</th>
                <th>Amount</th>
                <th>Total Amount</th>

                <!-- For Dinner -->
                <th>Food Item</th>
                <th>Amount</th>
                <th>Total Amount</th>
            </tr>
        </thead>

        {{-- <tbody>
            @php $sl = 1; @endphp
            @foreach ($groupedMenus as $menu)
                @php
                    $breakfast = $menu['breakfast'];
                    $lunch = $menu['lunch'];
                    $dinner = $menu['dinner'];

                    $maxRows = max(count($breakfast), count($lunch), count($dinner));

                    // Pre-calculate totals
                    $breakfastTotal = array_sum(array_column($breakfast, 'price'));
                    $lunchTotal = array_sum(array_column($lunch, 'price'));
                    $dinnerTotal = array_sum(array_column($dinner, 'price'));
                @endphp

                @for ($i = 0; $i < $maxRows; $i++)
                    <tr>
                        @if ($i === 0)
                            <!-- SL and Day & Date with rowspan -->
                            <td rowspan="{{ $maxRows }}">{{ $sl++ }}</td>
                            <td rowspan="{{ $maxRows }}">
                                {{ $menu['day'] }} <br>
                                {{ \Carbon\Carbon::parse($menu['date'])->format('d-m-Y') }}
                            </td>
                        @endif

                        <!-- Breakfast Columns -->
                        @if (isset($breakfast[$i]))
                            <td>{{ $breakfast[$i]->menu_item_name }}</td>
                            <td>{{ $breakfast[$i]->price }}</td>
                            @if ($i === 0)
                                <td rowspan="{{ count($breakfast) }}">{{ $breakfastTotal }}</td>
                            @endif
                        @else
                            @if ($i === 0)
                                <td colspan="3">No items</td>
                            @else
                                <td></td>
                                <td></td>
                            @endif
                        @endif

                        <!-- Lunch Columns -->
                        @if (isset($lunch[$i]))
                            <td>{{ $lunch[$i]->menu_item_name }}</td>
                            <td>{{ $lunch[$i]->price }}</td>
                            @if ($i === 0)
                                <td rowspan="{{ count($lunch) }}">{{ $lunchTotal }}</td>
                            @endif
                        @else
                            @if ($i === 0)
                                <td colspan="3">No items</td>
                            @else
                                <td></td>
                                <td></td>
                            @endif
                        @endif

                        <!-- Dinner Columns -->
                        @if (isset($dinner[$i]))
                            <td>{{ $dinner[$i]->menu_item_name }}</td>
                            <td>{{ $dinner[$i]->price }}</td>
                            @if ($i === 0)
                                <td rowspan="{{ count($dinner) }}">{{ $dinnerTotal }}</td>
                            @endif
                        @else
                            @if ($i === 0)
                                <td colspan="3">No items</td>
                            @else
                                <td></td>
                                <td></td>
                            @endif
                        @endif
                    </tr>
                @endfor
            @endforeach
        </tbody> --}}

        <tbody>
            @php $sl = 1; @endphp
            @foreach ($groupedMenus as $menu)
                @php
                    $maxRows = max(count($menu['breakfast']), count($menu['lunch']), count($menu['dinner']));
                @endphp
                @for ($i = 0; $i < $maxRows; $i++)
                    <tr>
                        @if ($i == 0)
                            <td rowspan="{{ $maxRows }}">{{ $sl++ }}</td>
                            <td rowspan="{{ $maxRows }}">{{ $menu['day'] }} <br> {{ \Carbon\Carbon::parse($menu['date'])->format('d-m-Y') }}</td>
                        @endif
    
                        @if (isset($menu['breakfast'][$i]))
                            <td>{{ $menu['breakfast'][$i]->menu_item_name }}</td>
                            <td>{{ $menu['breakfast'][$i]->price }}</td>
                            @if ($i == 0)
                                <td rowspan="{{ count($menu['breakfast']) }}">
                                    @php
                                        $breakfastTotal = 0;
                                        foreach ($menu['breakfast'] as $breakfastItem) {
                                            $breakfastTotal += $breakfastItem->price;
                                        }
                                    @endphp
                                    {{ $breakfastTotal }}
                                </td>
                            @endif
                        @else
                            <td></td>
                            <td></td>
                            @if($i == 0 && count($menu['breakfast']) == 0)
                                <td>0</td>
                            @endif
                        @endif
    
                        @if (isset($menu['lunch'][$i]))
                            <td>{{ $menu['lunch'][$i]->menu_item_name }}</td>
                            <td>{{ $menu['lunch'][$i]->price }}</td>
                            @if ($i == 0)
                                <td rowspan="{{ count($menu['lunch']) }}">
                                    @php
                                        $lunchTotal = 0;
                                        foreach ($menu['lunch'] as $lunchItem) {
                                            $lunchTotal += $lunchItem->price;
                                        }
                                    @endphp
                                    {{ $lunchTotal }}
                                </td>
                            @endif
                        @else
                            <td></td>
                            <td></td>
                            @if($i == 0 && count($menu['lunch']) == 0)
                                <td>0</td>
                            @endif
                        @endif
    
                        @if (isset($menu['dinner'][$i]))
                            <td>{{ $menu['dinner'][$i]->menu_item_name }}</td>
                            <td>{{ $menu['dinner'][$i]->price }}</td>
                            @if ($i == 0)
                                <td rowspan="{{ count($menu['dinner']) }}">
                                    @php
                                        $dinnerTotal = 0;
                                        foreach ($menu['dinner'] as $dinnerItem) {
                                            $dinnerTotal += $dinnerItem->price;
                                        }
                                    @endphp
                                    {{ $dinnerTotal }}
                                </td>
                            @endif
                        @else
                            <td></td>
                            <td></td>
                            @if($i == 0 && count($menu['dinner']) == 0)
                                <td>0</td>
                            @endif
                        @endif
                    </tr>
                @endfor
            @endforeach
        </tbody>


    </table>

</body>

</html>
