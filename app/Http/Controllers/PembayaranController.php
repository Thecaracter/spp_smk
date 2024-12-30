<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        // Get current month and the latest year from database
        $currentMonth = now()->month;
        $latestYear = User::join('tagihan', 'users.id', '=', 'tagihan.user_id')
            ->selectRaw('YEAR(tanggal_jatuh_tempo) as year')
            ->orderBy('year', 'desc')
            ->value('year') ?? now()->year;

        $month = $request->get('month', $currentMonth);
        $year = $request->get('year', $latestYear);

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
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'nim' => $user->nit,
                    'kelas' => $user->kelas,
                    'status' => $user->tagihan->isEmpty()
                        ? 'Tidak Ada Tagihan'
                        : ($user->tagihan->where('status', '!=', 'lunas')->isEmpty() ? 'Lunas' : 'Belum Lunas'),
                    'tagihan' => $user->tagihan->map(function ($tag) {
                        return [
                            'id' => $tag->id,
                            'jenis' => $tag->jenis_pembayaran->nama,
                            'total_tagihan' => $tag->total_tagihan,
                            'total_terbayar' => $tag->total_terbayar,
                            'status' => $tag->status,
                            'pembayaran' => $tag->pembayaran
                        ];
                    })
                ];
            });

        if ($request->ajax()) {
            return response()->json([
                'payments' => $payments,
                'currentMonth' => $currentMonth,
                'latestYear' => $latestYear
            ]);
        }

        return view('pages.pembayaran', compact(
            'payments',
            'availableYears',
            'currentMonth',
            'latestYear'
        ));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'monthly');
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $startMonth = $request->get('start_month');
        $endMonth = $request->get('end_month');
        $startYear = $request->get('start_year');
        $endYear = $request->get('end_year');

        $query = User::where('role', 'siswa')
            ->with(['tagihan.pembayaran', 'tagihan.jenis_pembayaran']);

        if ($type === 'monthly') {
            $query->whereHas('tagihan', function ($q) use ($month, $year) {
                $q->whereMonth('tanggal_jatuh_tempo', $month)
                    ->whereYear('tanggal_jatuh_tempo', $year);
            });
        } elseif ($type === 'range') {
            $query->whereHas('tagihan', function ($q) use ($startMonth, $endMonth, $startYear, $endYear) {
                $q->whereBetween('tanggal_jatuh_tempo', [
                    "$startYear-$startMonth-01",
                    date('Y-m-t', strtotime("$endYear-$endMonth-01"))
                ]);
            });
        }

        $payments = $query->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'nim' => $user->nit,
                    'kelas' => $user->kelas,
                    'status' => $user->tagihan->isEmpty()
                        ? 'Tidak Ada Tagihan'
                        : ($user->tagihan->where('status', '!=', 'lunas')->isEmpty() ? 'Lunas' : 'Belum Lunas'),
                    'tagihan' => $user->tagihan->map(function ($tag) {
                        return [
                            'id' => $tag->id,
                            'jenis' => $tag->jenis_pembayaran->nama,
                            'total_tagihan' => $tag->total_tagihan,
                            'total_terbayar' => $tag->total_terbayar,
                            'status' => $tag->status,
                            'pembayaran' => $tag->pembayaran,
                            'tanggal_jatuh_tempo' => $tag->tanggal_jatuh_tempo
                        ];
                    })
                ];
            });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set Title based on type
        $title = match ($type) {
            'monthly' => sprintf(
                'LAPORAN PEMBAYARAN SISWA BULAN %s %d',
                strtoupper(date('F', mktime(0, 0, 0, $month, 1))),
                $year
            ),
            'range' => sprintf(
                'LAPORAN PEMBAYARAN SISWA PERIODE %s %d - %s %d',
                strtoupper(date('F', mktime(0, 0, 0, $startMonth, 1))),
                $startYear,
                strtoupper(date('F', mktime(0, 0, 0, $endMonth, 1))),
                $endYear
            ),
            default => 'LAPORAN PEMBAYARAN SISWA KESELURUHAN'
        };

        // Set Judul
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);

        // Headers
        $headers = ['No', 'Nama', 'NIS', 'Kelas', 'Jenis Pembayaran', 'Status', 'Nominal', 'Tanggal Bayar'];
        foreach (array_values($headers) as $index => $header) {
            $sheet->setCellValue(chr(65 + $index) . '3', $header);
        }

        // Style headers
        $sheet->getStyle('A3:H3')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        // Data
        $row = 4;
        $no = 1;
        foreach ($payments as $payment) {
            foreach ($payment['tagihan'] as $tagihan) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $payment['name']);
                $sheet->setCellValue('C' . $row, $payment['nim']);
                $sheet->setCellValue('D' . $row, $payment['kelas']);
                $sheet->setCellValue('E' . $row, $tagihan['jenis']);
                $sheet->setCellValue('F' . $row, strtoupper($tagihan['status']));

                // Set nilai dan format untuk nominal
                $sheet->setCellValue('G' . $row, $tagihan['total_tagihan']);
                $sheet->getStyle('G' . $row)->getNumberFormat()
                    ->setFormatCode('#,##0');

                // Set tanggal bayar
                if ($tagihan['status'] === 'lunas' && !empty($tagihan['pembayaran'])) {
                    $sheet->setCellValue(
                        'H' . $row,
                        date('d/m/Y', strtotime($tagihan['pembayaran'][0]['created_at']))
                    );
                } else {
                    $sheet->setCellValue('H' . $row, '-');
                }

                // Style for status
                $statusColor = match ($tagihan['status']) {
                    'lunas' => '86EFAC', // Light green
                    'cicilan' => 'FCD34D', // Light yellow
                    default => 'FCA5A5'  // Light red
                };

                $sheet->getStyle('F' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $statusColor]
                    ]
                ]);

                $row++;
            }
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add borders to all cells
        $sheet->getStyle('A3:H' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        // Set judul print area
        $sheet->getPageSetup()->setPrintArea('A1:H' . ($row - 1));
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        $writer = new Xlsx($spreadsheet);
        $filename = match ($type) {
            'monthly' => "pembayaran_{$year}_{$month}.xlsx",
            'range' => "pembayaran_{$startYear}{$startMonth}_sampai_{$endYear}{$endMonth}.xlsx",
            default => "pembayaran_semua.xlsx"
        };

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}