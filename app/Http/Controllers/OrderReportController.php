<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdersExport;

class OrderReportController extends Controller
{
    public function pdf()
    {
        // Fetch flattened data matching the requirement
        $items = OrderItem::with(['order.customer', 'product'])
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->orderByDesc('orders.order_date')
            ->select('order_items.*') // Select items to prevent ID ambiguity, but we rely on ordering
            ->get();

        $globalTotal = Order::sum('total_amount');

        $html = view('reports.orders_pdf', [
            'items' => $items,
            'globalTotal' => $globalTotal
        ])->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L', // Landscape
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output('reporte_pedidos.pdf', 'I'); // Inline
    }

    public function excel()
    {
        return Excel::download(new OrdersExport, 'reporte_pedidos.xlsx');
    }
}
