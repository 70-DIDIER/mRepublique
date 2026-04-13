<?php

namespace App\Filament\Widgets;

use App\Models\Commande;
use App\Models\Livraison;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $aujourd = now();
        $debutMois = $aujourd->copy()->startOfMonth();
        $debutMoisPasse = $aujourd->copy()->subMonth()->startOfMonth();
        $finMoisPasse = $aujourd->copy()->subMonth()->endOfMonth();

        // Commandes
        $commandesMois = Commande::whereBetween('created_at', [$debutMois, $aujourd])->count();
        $commandesMoisPasse = Commande::whereBetween('created_at', [$debutMoisPasse, $finMoisPasse])->count();
        $tendanceCommandes = $commandesMoisPasse > 0
            ? round((($commandesMois - $commandesMoisPasse) / $commandesMoisPasse) * 100)
            : 0;

        // Chiffre d'affaires
        $caMois = Commande::where('est_paye', true)
            ->whereBetween('created_at', [$debutMois, $aujourd])
            ->sum('montant_total');
        $caMoisPasse = Commande::where('est_paye', true)
            ->whereBetween('created_at', [$debutMoisPasse, $finMoisPasse])
            ->sum('montant_total');
        $tendanceCa = $caMoisPasse > 0
            ? round((($caMois - $caMoisPasse) / $caMoisPasse) * 100)
            : 0;

        // Livraisons en cours
        $livraisonsEnCours = Livraison::where('statut', 'en_chemin')->count();
        $livraisonsTerminees = Livraison::where('statut', 'livree')
            ->whereBetween('created_at', [$debutMois, $aujourd])
            ->count();

        // Clients
        $clients = User::where('role', 'client')->count();
        $nouveauxClients = User::where('role', 'client')
            ->whereBetween('created_at', [$debutMois, $aujourd])
            ->count();

        // Plat populaire
        $platPopulaire = DB::table('commande_plat')
            ->join('plats', 'commande_plat.plat_id', '=', 'plats.id')
            ->whereNull('commande_plat.boisson_id')
            ->select('plats.nom', DB::raw('SUM(commande_plat.quantite) as total'))
            ->groupBy('plats.id', 'plats.nom')
            ->orderByDesc('total')
            ->first();

        // Commandes en attente
        $enAttente = Commande::where('statut', 'en_attente')->count();

        // Sparkline 7 derniers jours pour commandes
        $sparkCommandes = collect(range(6, 0))->map(
            fn ($i) => Commande::whereDate('created_at', now()->subDays($i))->count()
        )->toArray();

        // Sparkline CA 7 jours
        $sparkCa = collect(range(6, 0))->map(
            fn ($i) => (int) Commande::where('est_paye', true)
                ->whereDate('created_at', now()->subDays($i))
                ->sum('montant_total')
        )->toArray();

        return [
            Stat::make('Commandes ce mois', $commandesMois)
                ->description($tendanceCommandes >= 0
                    ? "+{$tendanceCommandes}% vs mois dernier"
                    : "{$tendanceCommandes}% vs mois dernier")
                ->descriptionIcon($tendanceCommandes >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($tendanceCommandes >= 0 ? 'success' : 'danger')
                ->chart($sparkCommandes)
                ->icon('heroicon-o-shopping-cart'),

            Stat::make("CA du mois (F CFA)", number_format($caMois, 0, ',', ' '))
                ->description($tendanceCa >= 0
                    ? "+{$tendanceCa}% vs mois dernier"
                    : "{$tendanceCa}% vs mois dernier")
                ->descriptionIcon($tendanceCa >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($tendanceCa >= 0 ? 'success' : 'danger')
                ->chart($sparkCa)
                ->icon('heroicon-o-banknotes'),

            Stat::make('Livraisons en cours', $livraisonsEnCours)
                ->description("{$livraisonsTerminees} livrées ce mois")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info')
                ->icon('heroicon-o-truck'),

            Stat::make('Commandes en attente', $enAttente)
                ->description('À traiter')
                ->descriptionIcon('heroicon-m-clock')
                ->color($enAttente > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-clock'),

            Stat::make('Clients inscrits', $clients)
                ->description("+{$nouveauxClients} ce mois")
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary')
                ->icon('heroicon-o-users'),

            Stat::make('Plat populaire', $platPopulaire?->nom ?? 'Aucun')
                ->description($platPopulaire ? $platPopulaire->total . ' portions commandées' : '')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning')
                ->icon('heroicon-o-cake'),
        ];
    }
}
