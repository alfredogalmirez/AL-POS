<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles, WithColumnFormatting
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Order::with(['items.product', 'cashier'])->get();
    }

    public function headings(): array
    {
        return ['Date', 'Invoice', 'Cashier', 'Product', 'Qty', 'Price', 'Total', 'Payment'];
    }

    public function map($order): array
    {
        $rows = [];
        // Since one Order has many items, we create a row for each item
        foreach ($order->items as $item) {
            $rows[] = [
                $order->created_at->format('Y-m-d H:i'),
                $order->invoice_id,
                $order->cashier->name ?? 'System',
                $item->product->name ?? 'N/A',
                $item->quantity,
                (float)$item->price,
                (float)($item->quantity * $item->price),
                strtoupper($order->payment_method ?? 'CASH'),
            ];
        }
        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22, // Date
            'B' => 15, // Invoice
            'C' => 20, // Cashier
            'D' => 45, // Product (Extra wide)
            'H' => 15, // Payment
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Price: 1,234.56
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Total: 1,234.56
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set the Header row height to 30
        $sheet->getRowDimension(1)->setRowHeight(30);

        return [
            // Style row 1 (the header)
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['vertical' => 'center', 'horizontal' => 'center'],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['argb' => 'F2F2F2'] // Light gray background
                ]
            ],
        ];
    }
}
