@extends('admin.layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-6">
                    <div class="widget style1 yellow-bg">
                        <div class="row">
                            <div class="col-4">
                                <i class="fa fa-money fa-4x"></i>
                            </div>
                            <div class="col-8 text-right">
                                <h3> Total Pengeluaran </h3>
                                <h2 class="font-bold" style="font-size: 20px">Rp.
                                    {{ number_format($expense, 0, '.', '.') }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="widget style1 yellow-bg">
                        <div class="row">
                            <div class="col-4">
                                <i class="fa fa-money fa-4x"></i>
                            </div>
                            <div class="col-8 text-right">
                                <h3> Total Pemasukan </h3>
                                <h2 class="font-bold" style="font-size: 20px">Rp.
                                    {{ number_format($income, 0, '.', '.') }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                @foreach ($wallets as $wallet)
                    <a class="col-lg-3" href="{{ route('wallet.show', $wallet->id) }}">
                        <div class="widget style1 navy-bg">
                            <div class="row">
                                <div class="col-4">
                                    <i class="fa fa-money fa-4x"></i>
                                </div>
                                <div class="col-8 text-right">
                                    <h5> {{ $wallet->name }} </h5>
                                    <h2 class="font-bold" style="font-size: 20px">Rp.
                                        {{ number_format($wallet->balance, 0, '.', '.') }}</h2>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Transaksi {{ ucfirst($transaksi) }} Berdasarkan Kategori ({{ count($label) }} Bulan terakhir)</h5>
                    <div class="ibox-tools">
                        <select name="" id="select-transaksi" class="form-control">
                            <option value="pengeluaran" {{ $transaksi == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran
                            </option>
                            <option value="pemasukan" {{ $transaksi == 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                        </select>
                    </div>
                </div>
                <div class="ibox-content">
                    <div id="transaction_by_category"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Transaksi Pengeluaran dan Pemasukan Berdasarkan Dompet Bulan Ini</h5>
                    <div class="ibox-tools">

                    </div>
                </div>
                <div class="ibox-content">
                    <div id="transaction_by_wallet"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Pengeluaran dan Pemasukan Dompet Hari Ini</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-wrench"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">

                            <li><a href="#" class="dropdown-item">Config option 1</a>
                            </li>
                            <li><a href="#" class="dropdown-item">Config option 2</a>
                            </li>
                        </ul>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="%">#</th>
                                <th width="25%">Dompet</th>
                                <th width="25%">Pemasukan</th>
                                <th width="25%">Pengeluaran</th>
                                <th width="20%">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($walletHistoryToday) > 0)
                                @foreach ($walletHistoryToday as $today)
                                    <tr>
                                        <td>#</td>
                                        <td>{{ $today->name }}</td>
                                        <td>Rp.
                                            {{ number_format($today->historyByDate(\App\Enums\TypeStatusEnum::pemasukan())->sum('amount'), 0, '.', '.') }}
                                        </td>
                                        <td>Rp.
                                            {{ number_format($today->historyByDate(\App\Enums\TypeStatusEnum::pengeluaran())->sum('amount'), 0, '.', '.') }}
                                        </td>
                                        <td>Rp. {{ number_format($today->balance, 0, '.', '.') }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center">Data tidak ditemukan</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        $('#select-transaksi').change(function() {
            var val = $(this).val();
            window.location.href = '/e-dompet/dashboard?transaksi=' + val
        })
        var options = {
            series: {!! json_encode($value) !!},
            chart: {
                type: 'bar',
                height: 450,
                // stacked: true,
            },

            colors: {!! json_encode($color) !!},
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'left'
            },
            yaxis: {
                labels: {
                    formatter: function(value) {
                        return formatRupiah('"' + value + '"');
                    }
                },
            },
            xaxis: {
                categories: {!! json_encode($label) !!},
                labels: {
                    formatter: function(value) {
                        return value;
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: "horizontal",
                    shadeIntensity: 0.25,
                    gradientToColors: undefined,
                    inverseColors: true,
                    opacityFrom: 0.85,
                    opacityTo: 0.85,
                    stops: [50, 0, 100]
                },
            },
            plotOptions: {
                bar: {
                    borderRadius: 5,
                    columnWidth: '80%',
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#transaction_by_category"), options);
        chart.render();


        var optionsWallet = {
            series: [{
                    name: 'Pengeluaran',
                    data: {!! json_encode($transactionByWallet['value']['pengeluaran']) !!}
                },
                {
                    name: 'Pemasukan',
                    data: {!! json_encode($transactionByWallet['value']['pemasukan']) !!}
                }
            ],
            chart: {
                type: 'bar',
                height: 350,
                // stacked: true,
            },

            colors: {!! json_encode($color) !!},
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'left'
            },
            yaxis: {
                labels: {
                    formatter: function(value) {
                        return formatRupiah('"' + value + '"');
                    }
                },
            },
            xaxis: {
                categories: {!! json_encode($transactionByWallet['label']) !!},
                labels: {
                    formatter: function(value) {
                        return value;
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: "horizontal",
                    shadeIntensity: 0.25,
                    gradientToColors: undefined,
                    inverseColors: true,
                    opacityFrom: 0.85,
                    opacityTo: 0.85,
                    stops: [50, 0, 100]
                },
            },
            plotOptions: {
                bar: {
                    borderRadius: 5,
                    columnWidth: '80%',
                }
            }
        };

        var chartWallet = new ApexCharts(document.querySelector("#transaction_by_wallet"), optionsWallet);
        chartWallet.render();

        function getRandomColor($total) {
            const categories = [];
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var j = 0; j < $total; j++) {
                for (var i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                categories.push(color);
                color = '#';
            }

            return categories;
        }

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            // tambahkan titik jika yang di input sudah menjadi angka ribuan
            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }
    </script>
@endpush
