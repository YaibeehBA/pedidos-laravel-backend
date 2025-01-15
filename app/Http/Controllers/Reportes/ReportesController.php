<?php

namespace App\Http\Controllers\Reportes;

use App\Models\User;
use App\Models\Orden;
use App\Models\DetalleOrden;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class ReportesController extends Controller
{
 
    public function ingresosMensuales(Request $request) {
        // Validar los parámetros (puedes pasar un rango de fechas o solo un mes)
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
    
        // Obtenemos el rango de fechas que recibe el frontend
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
    
        // Consultar los ingresos dentro de las fechas proporcionadas
        $ingresos = Orden::selectRaw('MONTH(fecha_entrega) as mes, SUM(monto_total) as ingresos')
                        ->whereBetween('fecha_entrega', [$startDate, $endDate])
                        ->groupBy('mes')
                        ->get();
    
        // Transformar la respuesta para incluir el nombre del mes
        $ingresosMensuales = $ingresos->map(function ($item) {
            return [
                'mes' => Carbon::createFromFormat('m', $item->mes)->format('F'),
                'ingresos' => $item->ingresos
            ];
        });
    
        // Obtener el total de ingresos
        $totalIngresos = $ingresosMensuales->sum('ingresos');
    
        // Devolver la respuesta con los ingresos y el total
        return response()->json([
            'ingresos_mensuales' => $ingresosMensuales,
            'total_ingresos' => $totalIngresos
        ]);
    }
    

}
