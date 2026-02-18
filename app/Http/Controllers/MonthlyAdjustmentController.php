<?php

namespace App\Http\Controllers;

use App\Models\MonthlyAdjustment;
use App\Services\TimeCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonthlyAdjustmentController extends Controller
{
    public function __construct(
        protected TimeCalculatorService $calculator
    ) {}

    public function update(Request $request, string $month): JsonResponse
    {
        $validated = $request->validate([
            'hourly_rate'    => ['required', 'numeric', 'min:0'],
            'extra_value'    => ['required', 'numeric', 'min:0'],
            'discount_value' => ['required', 'numeric', 'min:0'],
        ], [
            'hourly_rate.required'    => 'O valor por hora é obrigatório.',
            'hourly_rate.numeric'     => 'O valor por hora deve ser numérico.',
            'hourly_rate.min'         => 'O valor por hora não pode ser negativo.',
            'extra_value.required'    => 'O acréscimo é obrigatório.',
            'extra_value.numeric'     => 'O acréscimo deve ser numérico.',
            'extra_value.min'         => 'O acréscimo não pode ser negativo.',
            'discount_value.required' => 'O desconto é obrigatório.',
            'discount_value.numeric'  => 'O desconto deve ser numérico.',
            'discount_value.min'      => 'O desconto não pode ser negativo.',
        ]);

        // Validar formato do mês (YYYY-MM)
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            return response()->json(['success' => false, 'message' => 'Mês inválido.'], 422);
        }

        $userId = auth()->id();

        MonthlyAdjustment::updateOrCreate(
            ['user_id' => $userId, 'month_reference' => $month],
            $validated
        );

        $stats = $this->calculator->getMonthlyStats($userId, $month);

        return response()->json([
            'success' => true,
            'message' => 'Ajustes do mês salvos com sucesso.',
            'stats'   => [
                'hourly_rate'            => $stats['hourly_rate'],
                'extra_value'            => $stats['extra_value'],
                'discount_value'         => $stats['discount_value'],
                'total_revenue'          => $stats['total_revenue'],
                'total_with_extra'       => $stats['total_with_extra'],
                'total_final'            => $stats['total_final'],
                'total_final_with_on_call' => $stats['total_final_with_on_call'],
            ],
        ]);
    }
}
