@extends('layouts.app')

@section('title', __('pages.walletStats.title'))

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.in')
            <a href="{{ route('community.wallet.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'walletId' => $wallet->id
                ]) }}">
                {{ $wallet->name }}
            </a>
            @lang('misc.for')
            <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}">
                {{ $economy->name }}
            </a>
        </div>
    </h2>

    <p>@lang('pages.walletStats.description')</p>

    <script>
        /**
        * A color wheel function.
        * Returns a color for an index.
        */
        function colorWheel(index, opacity = 1.0) {
            return 'rgba('
                + (Math.sin(index / (Math.PI / 1.5) + Math.PI * 2 / 3 * 0) + 1) * 255 / 2 + ', '
                + (Math.sin(index / (Math.PI / 1.5) + Math.PI * 2 / 3 * 1) + 1) * 255 / 2 + ', '
                + (Math.sin(index / (Math.PI / 1.5) + Math.PI * 2 / 3 * 2) + 1) * 255 / 2 + ', '
                + opacity
                + ')';
        };
    </script>

    <h3 class="ui horizontal divider header">
        @lang('pages.walletStats.purchaseDistribution')
    </h3>

    <canvas id="chartProductDist"
        height="150"
        aria-label="@lang('pages.walletStats.typeProductDist.chartName')"
        role="img"></canvas>
    <script>
        var data = JSON.parse('{!! json_encode($productDistData) !!}');
        data.datasets[0].backgroundColor = function(context) {
                return colorWheel(context.dataIndex, 0.5);
            };
        data.datasets[0].borderColor = function(context) {
                return colorWheel(context.dataIndex, 0.8);
            };
        data.datasets[0].hoverBackgroundColor = function(context) {
                return colorWheel(context.dataIndex, 0.8);
            };
        data.datasets[0].hoverBorderColor = function(context) {
                return colorWheel(context.dataIndex, 1);
            };
        var chartProductDist = new Chart(
            document.getElementById('chartProductDist').getContext('2d'),
            {
                type: 'doughnut',
                data: data,
            }
        );
    </script>

    <h3 class="ui horizontal divider header">
        @lang('pages.walletStats.purchasePerHourDay')
    </h3>

    <canvas id="chartBuyTimeHour"
        height="200"
        aria-label="@lang('pages.walletStats.typeProductDist.chartName')"
        role="img"></canvas>
    <script>
        var data = JSON.parse('{!! json_encode($buyTimeHourData) !!}');
        data.datasets[0].backgroundColor = function(context) {
                return colorWheel(context.dataIndex, 0.5);
            };
        data.datasets[0].borderColor = function(context) {
                return colorWheel(context.dataIndex, 0.8);
            };
        data.datasets[0].hoverBackgroundColor = function(context) {
                return colorWheel(context.dataIndex, 0.8);
            };
        data.datasets[0].hoverBorderColor = function(context) {
                return colorWheel(context.dataIndex, 1);
            };
        var chartBuyTimeHour = new Chart(
            document.getElementById('chartBuyTimeHour').getContext('2d'),
            {
                type: 'bar',
                data: data,
                options: {
                    legend: false,
                }
            },
        );
    </script>

    <canvas id="chartBuyTimeDay"
        height="150"
        aria-label="@lang('pages.walletStats.typeProductDist.chartName')"
        role="img"></canvas>
    <script>
        var data = JSON.parse('{!! json_encode($buyTimeDayData) !!}');
        data.datasets[0].backgroundColor = function(context) {
                return colorWheel(context.dataIndex, 0.5);
            };
        data.datasets[0].borderColor = function(context) {
                return colorWheel(context.dataIndex, 0.8);
            };
        data.datasets[0].hoverBackgroundColor = function(context) {
                return colorWheel(context.dataIndex, 0.8);
            };
        data.datasets[0].hoverBorderColor = function(context) {
                return colorWheel(context.dataIndex, 1);
            };
        var chartBuyTimeDay = new Chart(
            document.getElementById('chartBuyTimeDay').getContext('2d'),
            {
                type: 'bar',
                data: data,
                options: {
                    legend: false,
                }
            },
        );
    </script>

    <p>
        <a href="{{ route('community.wallet.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'walletId' => $wallet->id
                ]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
