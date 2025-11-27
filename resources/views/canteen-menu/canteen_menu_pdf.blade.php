<!DOCTYPE html>
<html>

<head>
    <title>Canteen Menu</title>
    <style>
        body {
            font-family: 'kalpurush';
        }

        .report-header {
            text-align: center;
            margin: 0;
            padding: 8px 0;
            font-family: 'Times New Roman', Times, serif;
        }

        .report-header .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
        }

        .report-header .school-address {
            font-size: 14px;
            color: #2c3e50;
            margin: 0;
            font-weight: bold;
        }

        .report-header .report-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            padding: 2px 0;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            display: inline-block;
            margin: 8px 0 10px 0;
        }

        .date-details-card {
            text-align: center;
            margin: 0;
            font-family: 'Times New Roman', Times, serif;
            padding-top: 1px;
        }

        .menu-date-detail {
            font-size: 12px;
            font-family: 'Times New Roman', Times, serif;
            color: #2c3e50;
            line-height: 1.2;
            margin: 5px 0;
        }

        .menu-date-detail span {
            margin-left: 6px;
            font-weight: bold;
            color: #2c3e50;
            font-family: 'Times New Roman', Times, serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-family: 'Times New Roman', Times, serif;
        }

        table,
        th,
        td {
            border: 1px solid #000;
            text-align: center;
            font-family: 'Times New Roman', Times, serif;
        }

        th,
        td {
            padding: 2px;
            font-size: 14px;
        }

        table thead th {
            background-color: #cccbcb;
            text-align: center;
        }

        .signature_section {
            position: absolute;
            bottom: 0;
            right: 0;
            text-align: center;
            width: 200px;
            margin-right: 30px;
            margin-bottom: 10px;
        }

        .signature-line {
            width: 150px;
            border-top: 1px solid #000;
            margin: 0 auto;
        }

        .signature-text {
            margin-top: 2px;
            font-weight: bold;
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
        }

        @media print {
            .date-details-card {
                margin-bottom: -20px;
                margin-left: 15px;
                margin-top: -20px;
            }

            table,
            th,
            td {
                border: 1px solid #000;
                border-collapse: collapse;
            }

            .signature_section {
                position: absolute;
                bottom: 40px;
                right: 40px;
                text-align: center;
            }

            .signature-line {
                width: 150px;
                border-top: 1px solid #000;
                margin: 0 auto;
            }

            .signature-text {
                margin-top: 4px;
                font-weight: bold;
            }
        }
    </style>
</head>

<body>

    <div class="report-header">
        <p class="school-name">TPSC Cafeteria</p>
        <p class="school-address">Joypur Para, Bogura-5800</p>
        <p class="report-title">Weekly Food Menu Plan - <span>{{ $month }}</span>,
            <span>{{ $year }}</span></p>
    </div>

    <div class="date-details-card">
        <p class="menu-date-detail"><strong>Week : </strong><span>{{ $weekNumberText }}</span></p>
        <p class="menu-date-detail"><strong>Week Date : </strong><span>{{ $weekStartDate }} To {{ $weekEndDate }}</span>
        </p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th rowspan="2">SL</th>
                <th rowspan="2">Day & Date</th>
                <th colspan="3">Breakfast</th>
                <th colspan="3">Lunch</th>
                <th colspan="3">Dinner</th>
            </tr>
            <tr>
                <th>Food Item</th>
                <th>Amount</th>
                <th>Total</th>
                <th>Food Item</th>
                <th>Amount</th>
                <th>Total</th>
                <th>Food Item</th>
                <th>Amount</th>
                <th>Total</th>
            </tr>
        </thead>
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
                            <td rowspan="{{ $maxRows }}">{{ $menu['day'] }} <br>
                                {{ \Carbon\Carbon::parse($menu['date'])->format('d-m-Y') }}</td>
                        @endif

                        {{-- Breakfast --}}
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
                            @if ($i == 0 && count($menu['breakfast']) == 0)
                                <td>0</td>
                            @endif
                        @endif

                        {{-- Lunch --}}
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
                            @if ($i == 0 && count($menu['lunch']) == 0)
                                <td>0</td>
                            @endif
                        @endif

                        {{-- Dinner --}}
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
                            @if ($i == 0 && count($menu['dinner']) == 0)
                                <td>0</td>
                            @endif
                        @endif
                    </tr>
                @endfor
            @endforeach
        </tbody>
    </table>

    <div class="signature_section">
        <hr class="signature-line">
        <p class="signature-text">Manager</p>
    </div>

</body>

</html>
