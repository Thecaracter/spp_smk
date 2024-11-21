<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Get available years from tagihan
        $availableYears = User::join('tagihan', 'users.id', '=', 'tagihan.user_id')
            ->selectRaw('YEAR(tanggal_jatuh_tempo) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $payments = User::where('role', 'siswa')
            ->with([
                'tagihan' => function ($query) use ($month, $year) {
                    $query->whereMonth('tanggal_jatuh_tempo', $month)
                        ->whereYear('tanggal_jatuh_tempo', $year);
                },
                'tagihan.pembayaran',
                'tagihan.jenis_pembayaran'
            ])
            ->get()
            ->map(function ($user) {
                $tagihanBulanIni = $user->tagihan->first();
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'nim' => $user->nisn,
                    'kelas' => $user->kelas,
                    'status' => $tagihanBulanIni ?
                        ($tagihanBulanIni->status === 'lunas' ? 'Sudah Bayar' : 'Belum Bayar')
                        : 'Belum Ada Tagihan',
                    'tagihan' => $tagihanBulanIni,
                ];
            });

        if ($request->ajax()) {
            return response()->json($payments);
        }

        return view('pages.pembayaran', compact('payments', 'availableYears'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'monthly');
        $month = $request->get('month');
        $year = $request->get('year');
        $startMonth = $request->get('start_month');
        $endMonth = $request->get('end_month');
        $startYear = $request->get('start_year');
        $endYear = $request->get('end_year');

        $query = User::where('role', 'siswa')->with(['tagihan.pembayaran', 'tagihan.jenis_pembayaran']);

        if ($type === 'monthly') {
            $query->whereHas('tagihan', function ($q) use ($month, $year) {
                $q->whereMonth('tanggal_jatuh_tempo', $month)
                    ->whereYear('tanggal_jatuh_tempo', $year);
            });
        } else {
            $query->whereHas('tagihan', function ($q) use ($startMonth, $endMonth, $startYear, $endYear) {
                $q->whereBetween('tanggal_jatuh_tempo', [
                    "$startYear-$startMonth-01",
                    date('Y-m-t', strtotime("$endYear-$endMonth-01"))
                ]);
            });
        }

        $payments = $query->get()->map(function ($user) {
            $tagihanBulanIni = $user->tagihan->first();
            return [
                'name' => $user->name,
                'nim' => $user->nisn,
                'kelas' => $user->kelas,
                'status' => $tagihanBulanIni ?
                    ($tagihanBulanIni->status === 'lunas' ? 'Sudah Bayar' : 'Belum Bayar')
                    : 'Belum Ada Tagihan',
                'tagihan' => $tagihanBulanIni,
            ];
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'NIS');
        $sheet->setCellValue('D1', 'Kelas');
        $sheet->setCellValue('E1', 'Status');
        $sheet->setCellValue('F1', 'Nominal');
        $sheet->setCellValue('G1', 'Tanggal Bayar');

        // Style headers
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0']
            ]
        ]);

        // Data
        $row = 2;
        foreach ($payments as $index => $payment) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $payment['name']);
            $sheet->setCellValue('C' . $row, $payment['nim']);
            $sheet->setCellValue('D' . $row, $payment['kelas']);
            $sheet->setCellValue('E' . $row, $payment['status']);
            $sheet->setCellValue('F' . $row, $payment['tagihan'] ? $payment['tagihan']['total_tagihan'] : '-');
            $sheet->setCellValue(
                'G' . $row,
                $payment['tagihan'] && $payment['status'] === 'Sudah Bayar'
                ? date('d/m/Y', strtotime($payment['tagihan']['pembayaran'][0]['created_at']))
                : '-'
            );
            $row++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = $type === 'monthly'
            ? "pembayaran_{$year}_{$month}.xlsx"
            : "pembayaran_{$startYear}{$startMonth}_sampai_{$endYear}{$endMonth}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}