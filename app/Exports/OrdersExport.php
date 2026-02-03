<?php

namespace App\Exports;

use App\Models\OrderItem;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OrdersExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting, WithMapping, WithEvents
{
    protected $rowCount = 0;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $items = OrderItem::with(['order.customer', 'product'])
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->orderByDesc('orders.order_date')
            ->select('order_items.*')
            ->get();
        
        $this->rowCount = $items->count();

        return $items;
    }

    public function map($item): array
    {
        return [
            $item->order->customer->name,
            $item->order->order_date->format('d/m/Y'),
            $item->product->name,
            $item->quantity,
            $item->unit_price,
            $item->subtotal,
            $item->order->total_amount,
            ucfirst($item->order->status),
        ];
    }

    public function headings(): array
    {
        return [
            'Cliente',
            'Fecha Pedido',
            'Producto',
            'Cantidad',
            'Precio Unitario',
            'Subtotal',
            'Total del Pedido',
            'Estado',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE0E0E0']]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => '[$$-409]#,##0.00', // Unit Price
            'F' => '[$$-409]#,##0.00', // Subtotal
            'G' => '[$$-409]#,##0.00', // Order Total
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Calculate Global Total row
                $totalRow = $this->rowCount + 2;
                $globalTotal = Order::sum('total_amount'); // Recalculate or sum from sheet

                $event->sheet->setCellValue("A{$totalRow}", "Total Global Ventas:");
                $event->sheet->setCellValue("G{$totalRow}", $globalTotal);
                
                $event->sheet->getStyle("G{$totalRow}")->getNumberFormat()->setFormatCode('[$$-409]#,##0.00');
                $event->sheet->getStyle("A{$totalRow}:H{$totalRow}")->getFont()->setBold(true);
            },
        ];
    }
}
