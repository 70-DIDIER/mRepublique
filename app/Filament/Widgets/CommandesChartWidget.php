<?php

namespace App\Filament\Widgets;

use App\Models\Commande;
use Filament\Widgets\ChartWidget;

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
        $labels = [];
        $data   = [];

        for ($i = 13; $i >= 0; $i--) {
            $date     = now()->subDays($i);
            $labels[] = $date->translatedFormat('D d/m');

            if ($this->filter === 'ca') {
                $data[] = (int) Commande::where('est_paye', true)
                    ->whereDate('created_at', $date)
                    ->sum('montant_total');
            } else {
                $data[] = Commande::whereDate('created_at', $date)->count();
            }
        }

        $isCA = $this->filter === 'ca';

        return [
            'datasets' => [
                [
                    'label'                => $isCA ? "CA (F CFA)" : 'Commandes',
                    'data'                 => $data,
                    'borderColor'          => '#2B7A9E',
                    'backgroundColor'      => 'rgba(43, 122, 158, 0.08)',
                    'fill'                 => true,
                    'tension'              => 0.4,
                    'pointBackgroundColor' => '#2B7A9E',
                    'pointRadius'          => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
