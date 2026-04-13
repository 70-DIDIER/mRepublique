<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CommandesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Commandes — 14 derniers jours';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 2;
    protected static ?string $maxHeight = '280px';

    public ?string $filter = 'commandes';

    protected function getFilters(): ?array
    {
        return [
            'commandes' => 'Commandes',
            'ca'        => "Chiffre d'affaires",
        ];
    }

    protected function getData(): array
    {
        $debut = now()->subDays(13)->startOfDay();

        // ── 1 seule requête GROUP BY jour ──────────────────────────────────
        $rows = DB::select("
            SELECT
                DATE(created_at) AS jour,
                COUNT(*) AS nb,
                SUM(CASE WHEN est_paye = 1 THEN montant_total ELSE 0 END) AS ca
            FROM commandes
            WHERE created_at >= ?
            GROUP BY DATE(created_at)
        ", [$debut]);

        $map    = collect($rows)->keyBy('jour');
        $labels = [];
        $data   = [];
        $isCA   = $this->filter === 'ca';

        for ($i = 13; $i >= 0; $i--) {
            $date     = now()->subDays($i);
            $key      = $date->toDateString();
            $labels[] = $date->translatedFormat('D d/m');
            $data[]   = $isCA
                ? (int) ($map[$key]->ca ?? 0)
                : (int) ($map[$key]->nb ?? 0);
        }

        return [
            'datasets' => [[
                'label'                => $isCA ? 'CA (F CFA)' : 'Commandes',
                'data'                 => $data,
                'borderColor'          => '#2B7A9E',
                'backgroundColor'      => 'rgba(43, 122, 158, 0.08)',
                'fill'                 => true,
                'tension'              => 0.4,
                'pointBackgroundColor' => '#2B7A9E',
                'pointRadius'          => 4,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
