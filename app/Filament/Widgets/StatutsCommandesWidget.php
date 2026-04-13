<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StatutsCommandesWidget extends ChartWidget
{
    protected static ?string $heading = 'Répartition des statuts';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 1;
    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        // ── 1 seule requête GROUP BY statut ────────────────────────────────
        $rows = DB::select("
            SELECT statut, COUNT(*) AS nb
            FROM commandes
            GROUP BY statut
        ");

        $map = collect($rows)->keyBy('statut');

        $statuts = ['en_attente', 'en_cours', 'livree', 'annulee'];
        $labels  = ['En attente', 'En cours', 'Livrée', 'Annulée'];
        $colors  = ['#F59E0B', '#2B7A9E', '#10B981', '#EF4444'];

        return [
            'datasets' => [[
                'data'            => array_map(fn ($s) => (int) ($map[$s]->nb ?? 0), $statuts),
                'backgroundColor' => $colors,
                'hoverOffset'     => 6,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
