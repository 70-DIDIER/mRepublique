<?php

namespace App\Filament\Widgets;

use App\Models\Commande;
use Filament\Widgets\ChartWidget;

class StatutsCommandesWidget extends ChartWidget
{
    protected static ?string $heading = 'Répartition des statuts';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 1;
    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $statuts = [
            'en_attente' => Commande::where('statut', 'en_attente')->count(),
            'en_cours'   => Commande::where('statut', 'en_cours')->count(),
            'livree'     => Commande::where('statut', 'livree')->count(),
            'annulee'    => Commande::where('statut', 'annulee')->count(),
        ];

        return [
            'datasets' => [
                [
                    'data'            => array_values($statuts),
                    'backgroundColor' => [
                        '#F59E0B', // en_attente — amber
                        '#2B7A9E', // en_cours — bleu
                        '#10B981', // livree — vert
                        '#EF4444', // annulee — rouge
                    ],
                    'hoverOffset' => 6,
                ],
            ],
            'labels' => ['En attente', 'En cours', 'Livrée', 'Annulée'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
