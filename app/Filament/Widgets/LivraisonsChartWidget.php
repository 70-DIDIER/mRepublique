<?php

namespace App\Filament\Widgets;

use App\Models\Livraison;
use Filament\Widgets\ChartWidget;

class LivraisonsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Livraisons — 7 derniers jours';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 2;
    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $labels      = [];
        $enAttente   = [];
        $enChemin    = [];
        $livrees      = [];

        for ($i = 6; $i >= 0; $i--) {
            $date     = now()->subDays($i);
            $labels[] = $date->translatedFormat('D d/m');

            $enAttente[] = Livraison::whereDate('created_at', $date)->where('statut', 'en_attente')->count();
            $enChemin[]  = Livraison::whereDate('created_at', $date)->where('statut', 'en_chemin')->count();
            $livrees[]    = Livraison::whereDate('created_at', $date)->where('statut', 'livree')->count();
        }

        return [
            'datasets' => [
                [
                    'label'           => 'En attente',
                    'data'            => $enAttente,
                    'backgroundColor' => '#F59E0B',
                ],
                [
                    'label'           => 'En chemin',
                    'data'            => $enChemin,
                    'backgroundColor' => '#2B7A9E',
                ],
                [
                    'label'           => 'Livrées',
                    'data'            => $livrees,
                    'backgroundColor' => '#10B981',
                ],
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
