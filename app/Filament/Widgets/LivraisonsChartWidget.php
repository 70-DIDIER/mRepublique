<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class LivraisonsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Livraisons — 7 derniers jours';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 2;
    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $debut = now()->subDays(6)->startOfDay();

        // ── 1 seule requête GROUP BY jour + statut ──────────────────────────
        $rows = DB::select("
            SELECT
                DATE(created_at) AS jour,
                statut,
                COUNT(*) AS nb
            FROM livraisons
            WHERE created_at >= ?
            GROUP BY DATE(created_at), statut
        ", [$debut]);

        // Indexer par [jour][statut]
        $map = [];
        foreach ($rows as $row) {
            $map[$row->jour][$row->statut] = (int) $row->nb;
        }

        $labels    = [];
        $enAttente = [];
        $enChemin  = [];
        $livrees   = [];

        for ($i = 6; $i >= 0; $i--) {
            $date      = now()->subDays($i);
            $key       = $date->toDateString();
            $labels[]  = $date->translatedFormat('D d/m');
            $enAttente[] = $map[$key]['en_attente'] ?? 0;
            $enChemin[]  = $map[$key]['en_chemin']  ?? 0;
            $livrees[]   = $map[$key]['livree']      ?? 0;
        }

        return [
            'datasets' => [
                ['label' => 'En attente', 'data' => $enAttente, 'backgroundColor' => '#F59E0B'],
                ['label' => 'En chemin',  'data' => $enChemin,  'backgroundColor' => '#2B7A9E'],
                ['label' => 'Livrées',    'data' => $livrees,   'backgroundColor' => '#10B981'],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => ['stacked' => true],
                'y' => ['stacked' => true, 'beginAtZero' => true],
            ],
        ];
    }
}
