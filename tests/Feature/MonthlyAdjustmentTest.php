<?php

use App\Models\MonthlyAdjustment;
use App\Models\Setting;
use App\Models\TimeEntry;
use App\Models\User;
use App\Services\TimeCalculatorService;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->settings = Setting::create([
        'user_id'        => $this->user->id,
        'hourly_rate'    => 100,
        'extra_value'    => 50,
        'discount_value' => 20,
    ]);
    $this->calculator = app(TimeCalculatorService::class);
});

describe('Endpoint PUT /monthly-adjustments/{month}', function () {
    test('usuario pode criar ajuste para um mes', function () {
        $response = $this->actingAs($this->user)->putJson('/monthly-adjustments/2026-01', [
            'hourly_rate'    => 120,
            'extra_value'    => 30,
            'discount_value' => 10,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('monthly_adjustments', [
            'user_id'         => $this->user->id,
            'month_reference' => '2026-01',
            'hourly_rate'     => 120,
            'extra_value'     => 30,
            'discount_value'  => 10,
        ]);
    });

    test('ajuste existente e atualizado (upsert)', function () {
        MonthlyAdjustment::create([
            'user_id'         => $this->user->id,
            'month_reference' => '2026-01',
            'hourly_rate'     => 100,
            'extra_value'     => 0,
            'discount_value'  => 0,
        ]);

        $this->actingAs($this->user)->putJson('/monthly-adjustments/2026-01', [
            'hourly_rate'    => 150,
            'extra_value'    => 60,
            'discount_value' => 5,
        ]);

        expect(MonthlyAdjustment::where('user_id', $this->user->id)
            ->where('month_reference', '2026-01')
            ->count()
        )->toBe(1);

        $this->assertDatabaseHas('monthly_adjustments', [
            'user_id'         => $this->user->id,
            'month_reference' => '2026-01',
            'hourly_rate'     => 150,
        ]);
    });

    test('requer autenticacao', function () {
        $response = $this->putJson('/monthly-adjustments/2026-01', [
            'hourly_rate'    => 100,
            'extra_value'    => 0,
            'discount_value' => 0,
        ]);

        $response->assertStatus(401);
    });

    test('valida campos obrigatorios', function () {
        $response = $this->actingAs($this->user)->putJson('/monthly-adjustments/2026-01', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['hourly_rate', 'extra_value', 'discount_value']);
    });

    test('nao aceita valores negativos', function () {
        $response = $this->actingAs($this->user)->putJson('/monthly-adjustments/2026-01', [
            'hourly_rate'    => -10,
            'extra_value'    => -5,
            'discount_value' => -1,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['hourly_rate', 'extra_value', 'discount_value']);
    });

    test('rejeita formato de mes invalido', function () {
        $response = $this->actingAs($this->user)->putJson('/monthly-adjustments/invalido', [
            'hourly_rate'    => 100,
            'extra_value'    => 0,
            'discount_value' => 0,
        ]);

        $response->assertStatus(422);
    });

    test('retorna stats atualizados', function () {
        $response = $this->actingAs($this->user)->putJson('/monthly-adjustments/2026-01', [
            'hourly_rate'    => 200,
            'extra_value'    => 80,
            'discount_value' => 15,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'stats' => ['hourly_rate', 'extra_value', 'discount_value', 'total_revenue', 'total_final'],
            ]);

        expect((float) $response->json('stats.hourly_rate'))->toEqual(200.0);
        expect((float) $response->json('stats.extra_value'))->toEqual(80.0);
        expect((float) $response->json('stats.discount_value'))->toEqual(15.0);
    });
});

describe('TimeCalculatorService com ajustes mensais', function () {
    test('usa settings como fallback quando nao ha ajuste para o mes', function () {
        $hourlyRate = $this->calculator->getHourlyRate($this->user->id, '2026-01');
        $extraValue = $this->calculator->getExtraValue($this->user->id, '2026-01');
        $discountValue = $this->calculator->getDiscountValue($this->user->id, '2026-01');

        expect($hourlyRate)->toBe(100.0);
        expect($extraValue)->toBe(50.0);
        expect($discountValue)->toBe(20.0);
    });

    test('usa ajuste mensal quando existe registro para o mes', function () {
        MonthlyAdjustment::create([
            'user_id'         => $this->user->id,
            'month_reference' => '2026-01',
            'hourly_rate'     => 120,
            'extra_value'     => 30,
            'discount_value'  => 5,
        ]);

        $hourlyRate = $this->calculator->getHourlyRate($this->user->id, '2026-01');
        $extraValue = $this->calculator->getExtraValue($this->user->id, '2026-01');
        $discountValue = $this->calculator->getDiscountValue($this->user->id, '2026-01');

        expect($hourlyRate)->toBe(120.0);
        expect($extraValue)->toBe(30.0);
        expect($discountValue)->toBe(5.0);
    });

    test('ajuste de fevereiro nao afeta janeiro', function () {
        MonthlyAdjustment::create([
            'user_id'         => $this->user->id,
            'month_reference' => '2026-02',
            'hourly_rate'     => 999,
            'extra_value'     => 999,
            'discount_value'  => 999,
        ]);

        // Janeiro usa settings (fallback)
        expect($this->calculator->getHourlyRate($this->user->id, '2026-01'))->toBe(100.0);
        expect($this->calculator->getExtraValue($this->user->id, '2026-01'))->toBe(50.0);
        expect($this->calculator->getDiscountValue($this->user->id, '2026-01'))->toBe(20.0);
    });

    test('alterar settings afeta mes sem ajuste especifico mas nao mes com ajuste', function () {
        MonthlyAdjustment::create([
            'user_id'         => $this->user->id,
            'month_reference' => '2026-01',
            'hourly_rate'     => 120,
            'extra_value'     => 30,
            'discount_value'  => 5,
        ]);

        // Alterar settings
        $this->settings->update(['hourly_rate' => 200, 'extra_value' => 100, 'discount_value' => 40]);

        // Janeiro tem ajuste próprio — mantém 120
        expect($this->calculator->getHourlyRate($this->user->id, '2026-01'))->toBe(120.0);

        // Fevereiro não tem ajuste — usa novo valor de settings (200)
        expect($this->calculator->getHourlyRate($this->user->id, '2026-02'))->toBe(200.0);
    });

    test('getMonthlyStats usa valor do ajuste mensal', function () {
        MonthlyAdjustment::create([
            'user_id'         => $this->user->id,
            'month_reference' => '2026-01',
            'hourly_rate'     => 150,
            'extra_value'     => 0,
            'discount_value'  => 0,
        ]);

        TimeEntry::create([
            'user_id'         => $this->user->id,
            'date'            => '2026-01-10',
            'start_time'      => '09:00',
            'end_time'        => '11:00',
            'hours'           => 2,
            'description'     => 'Teste',
            'month_reference' => '2026-01',
        ]);

        $stats = $this->calculator->getMonthlyStats($this->user->id, '2026-01');

        expect($stats['hourly_rate'])->toBe(150.0);
        expect($stats['total_revenue'])->toBe(300.0); // 2h × 150
    });
});
