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
        $debutMois      = now()->startOfMonth();
        $debutMoisPasse = now()->subMonth()->startOfMonth();
        $finMoisPasse   = now()->subMonth()->endOfMonth();

        // ── 1 requête : tous les agrégats commandes en une fois ──────────────
        $stats = DB::selectOne("
            SELECT
                COUNT(*) AS total_commandes,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) AS commandes_mois,
                SUM(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) AS commandes_mois_passe,
                SUM(CASE WHEN est_paye = 1 AND created_at >= ? THEN montant_total ELSE 0 END) AS ca_mois,
                SUM(CASE WHEN est_paye = 1 AND created_at BETWEEN ? AND ? THEN montant_total ELSE 0 END) AS ca_mois_passe,
                SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) AS en_attente
            FROM commandes
        ", [
            $debutMois,
            $debutMoisPasse, $finMoisPasse,
            $debutMois,
            $debutMoisPasse, $finMoisPasse,
        ]);

        // ── 1 requête : livraisons ──────────────────────────────────────────
        $livraisons = DB::selectOne("
            SELECT
                SUM(CASE WHEN statut = 'en_chemin' THEN 1 ELSE 0 END) AS en_cours,
                SUM(CASE WHEN statut = 'livree' AND created_at >= ? THEN 1 ELSE 0 END) AS livrees_mois
            FROM livraisons
        ", [$debutMois]);

        // ── 1 requête : clients ─────────────────────────────────────────────
        $clients = DB::selectOne("
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) AS nouveaux_mois
            FROM users WHERE role = 'client'
        ", [$debutMois]);

        // ── 1 requête : plat populaire ──────────────────────────────────────
        $platPopulaire = DB::selectOne("
            SELECT p.nom, SUM(cp.quantite) AS total
            FROM commande_plat cp
            JOIN plats p ON cp.plat_id = p.id
            WHERE cp.boisson_id IS NULL
            GROUP BY p.id, p.nom
            ORDER BY total DESC
            LIMIT 1
        ");

        // ── 1 requête : sparklines 7 jours (commandes + CA) ────────────────
        $sparkRows = DB::select("
            SELECT
                DATE(created_at) AS jour,
                COUNT(*) AS nb,
                SUM(CASE WHEN est_paye = 1 THEN montant_total ELSE 0 END) AS ca
            FROM commandes
            WHERE created_at >= ?
            GROUP BY DATE(created_at)
        ", [now()->subDays(6)->startOfDay()]);

        $sparkMap = collect($sparkRows)->keyBy('jour');
        $sparkNb  = [];
        $sparkCa  = [];
        for ($i = 6; $i >= 0; $i--) {
            $day       = now()->subDays($i)->toDateString();
            $sparkNb[] = (int) ($sparkMap[$day]->nb ?? 0);
            $sparkCa[] = (int) ($sparkMap[$day]->ca ?? 0);
        }

        // ── Calcul tendances ────────────────────────────────────────────────
        $tendanceCmd = $stats->commandes_mois_passe > 0
            ? round((($stats->commandes_mois - $stats->commandes_mois_passe) / $stats->commandes_mois_passe) * 100)
            : 0;
        $tendanceCa = $stats->ca_mois_passe > 0
            ? round((($stats->ca_mois - $stats->ca_mois_passe) / $stats->ca_mois_passe) * 100)
            : 0;

        return [
            Stat::make('Commandes ce mois', (int) $stats->commandes_mois)
                ->description($tendanceCmd >= 0 ? "+{$tendanceCmd}% vs mois dernier" : "{$tendanceCmd}% vs mois dernier")
                ->descriptionIcon($tendanceCmd >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($tendanceCmd >= 0 ? 'success' : 'danger')
                ->chart($sparkNb)
                ->icon('heroicon-o-shopping-cart'),

            Stat::make('CA du mois (F CFA)', number_format((int) $stats->ca_mois, 0, ',', ' '))
                ->description($tendanceCa >= 0 ? "+{$tendanceCa}% vs mois dernier" : "{$tendanceCa}% vs mois dernier")
                ->descriptionIcon($tendanceCa >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($tendanceCa >= 0 ? 'success' : 'danger')
                ->chart($sparkCa)
                ->icon('heroicon-o-banknotes'),

            Stat::make('Livraisons en cours', (int) ($livraisons->en_cours ?? 0))
                ->description(($livraisons->livrees_mois ?? 0) . ' livrées ce mois')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info')
                ->icon('heroicon-o-truck'),

            Stat::make('Commandes en attente', (int) $stats->en_attente)
                ->description('À traiter')
                ->descriptionIcon('heroicon-m-clock')
                ->color($stats->en_attente > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-clock'),

            Stat::make('Clients inscrits', (int) $clients->total)
                ->description('+' . (int) $clients->nouveaux_mois . ' ce mois')
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
