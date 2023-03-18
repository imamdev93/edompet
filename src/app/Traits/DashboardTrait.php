<?php

namespace App\Traits;

use Carbon\Carbon;

trait DashboardTrait
{
    public function transactionByCategory($categories, $request)
    {
        $label = $this->getLabel();
        $convert = $this->convertLabel($label, 'F Y');
        $value = $this->getValue($categories, $label, $request);
        $data = $this->getChartResult($convert, $value);
        return $data;
    }

    public function transactionByWallet($wallets, $request)
    {
        $date = $request->date ?? date('m');
        $label = $wallets->pluck('name')->toArray();
        $value = [];
        $pemasukanVal = [];
        $pengeluaranVal = [];
        foreach ($wallets as $key => $val) {
            $pengeluaran = $val->historyByDate('pengeluaran', 'whereMonth', $date)->whereYear('created_at', date('Y'))->sum('amount');
            $pemasukan = $val->historyByDate('pemasukan', 'whereMonth', $date)->whereYear('created_at', date('Y'))->sum('amount');

            array_push($pemasukanVal, $pemasukan);
            array_push($pengeluaranVal, $pengeluaran);
        }
        $value = [
            'pemasukan' => $pemasukanVal,
            'pengeluaran' => $pengeluaranVal,
        ];

        $data = $this->getChartResult($label, $value);
        return $data;
    }

    private function getLabel()
    {
        $label = [];

        $periodDate = $this->getPeriod();

        for ($i = 0; $i <= $periodDate[1]; $i++) {
            array_push($label, $periodDate[0]->addMonth(1)->format('Y-m'));
        }

        return $label;
    }

    private function getValue($payloads, $label, $request)
    {
        $value = [];
        foreach ($payloads as $payload) {
            $data = $this->queryValues($payload, $label, $request);
            array_push($value, array('name' => $payload->name, 'color' => '#' . $payload->color, 'data' => $data));
        }

        return $value;
    }

    private function queryValues($payload, $label, $request)
    {
        $values = [];

        foreach ($label as $key => $row) {
            $date = Carbon::parse($row);
            $total = $payload->countTransaction($date, $request->transaksi ?: 'pengeluaran');
            $values[$key] = (int) $total;
        }

        return $values;
    }

    private function convertLabel($data, $format = "Y-m-d")
    {
        $label = [];

        foreach ($data as $row) {
            array_push($label, Carbon::parse($row)->translatedFormat($format));
        }

        return $label;
    }

    private function getPeriod()
    {
        $start_month = Carbon::now()->subMonth(3);
        $end_month = Carbon::now()->subMonth(1);
        $diff = $start_month->diffInMonths($end_month);

        return [$start_month, $diff];
    }

    private function getChartResult($label, $value)
    {
        return ['label' => $label, 'value' => $value];
    }
}
