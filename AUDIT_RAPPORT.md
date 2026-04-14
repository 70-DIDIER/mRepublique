# 🔍 RAPPORT D'AUDIT - M'République

**Date:** 13 avril 2026  
**Projet:** Plateforme de commande et livraison de restaurant  
**Stack:** Laravel 12, PHP 8.2, Filament 3, MySQL

---

## 📊 RÉSUMÉ EXÉCUTIF

Ce projet est une application de restauration avec commande, paiement mobile et livraison. L'audit révèle plusieurs vulnérabilités de sécurité critiques et des problèmes de qualité de code qui nécessitent une attention immédiate.

### Niveau de risque global: 🔴 ÉLEVÉ

---

## 🚨 PROBLÈMES CRITIQUES (Priorité 1)

### 1. **Sécurité - Codes SMS stockés en clair**
**Fichier:** `app/Http/Controllers/AuthController.php` (ligne 30-32)  
**Risque:** 🔴 CRITIQUE

```php
$code = random_int(1000, 9999);
$user->code_sms = $code; // ❌ Stocké en clair dans la base
$user->code_expires_at = now()->addMinutes(30);
```

**Impact:** Les codes de vérification SMS sont stockés en clair dans la base de données. En cas de compromission de la base, un attaquant peut accéder aux comptes utilisateurs.

**Recommandation:**
```php
$code = random_int(1000, 9999);
$user->code_sms = Hash::make($code); // ✅ Hasher le code
$user->code_expires_at = now()->addMinutes(30);
```

---

### 2. **Sécurité - Validation d'email désactivée**
**Fichier:** `app/Http/Controllers/AuthController.php` (ligne 95-97)  
**Risque:** 🔴 CRITIQUE

```php
// if (! $user->is_verified) {
// return response()->json(['message' => 'Votre compte n\'est pas encore vérifié.'], 403);
// }
```

**Impact:** La vérification du compte est commentée, permettant à n'importe qui de se connecter sans valider son numéro de téléphone.

**Recommandation:** Réactiver cette vérification immédiatement.

---

### 3. **Sécurité - Clés API en variables d'environnement non validées**
**Fichier:** `app/Services/PayGateService.php` (ligne 12)  
**Risque:** 🔴 CRITIQUE

```php
$this->authToken = env('PAYGATE_AUTH_TOKEN'); // ❌ Pas de validation
```

**Impact:** Si la clé API n'est pas définie, l'application continue de fonctionner avec une valeur null, causant des erreurs silencieuses ou des failles de sécurité.

**Recommandation:**
```php
$this->authToken = config('paygate.auth_token');
// Dans config/paygate.php
throw_if(empty($authToken), new \Exception('PAYGATE_AUTH_TOKEN non configuré'));
```

---

### 4. **Sécurité - Injection SQL potentielle**
**Fichier:** `app/Http/Controllers/CommandeController.php` (ligne 73)  
**Risque:** 🟠 ÉLEVÉ

```php
DB::table('commande_plat')->insert([
    'commande_id' => $commande->id,
    'plat_id' => null,
    'boisson_id' => $boisson->id, // ❌ Pas de validation stricte
    'quantite' => $quantite,
```

**Impact:** Bien que Laravel protège contre les injections SQL de base, l'utilisation directe de `DB::table()->insert()` contourne les protections Eloquent.

**Recommandation:** Utiliser les relations Eloquent ou valider strictement les IDs.

---

### 5. **Sécurité - Pas de rate limiting sur les endpoints sensibles**
**Fichier:** `routes/api.php`  
**Risque:** 🟠 ÉLEVÉ

```php
Route::post('/register', [AuthController::class, 'register']); // ❌ Pas de throttle
Route::post('/login', [AuthController::class, 'login']); // ❌ Pas de throttle
Route::post('/verify-code', [AuthController::class, 'verifyCode']); // ❌ Pas de throttle
```

**Impact:** Vulnérable aux attaques par force brute et au spam de SMS.

**Recommandation:**
```php
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-code', [AuthController::class, 'verifyCode']);
});
```

---

## ⚠️ PROBLÈMES MAJEURS (Priorité 2)

### 6. **Logique métier - Calcul de frais de livraison incohérent**
**Fichier:** `app/Http/Controllers/CommandeController.php` (ligne 51)  
**Risque:** 🟡 MOYEN

```php
$fraisLivraison = ceil(($distance * 100)*2); // ❌ Pourquoi *2 ?
```

**Impact:** Le README indique 125 F/km, mais le code applique 200 F/km (100*2). Incohérence commerciale.

**Recommandation:** Clarifier et documenter la formule, ou utiliser une constante configurable.

---

### 7. **Sécurité - Email non obligatoire mais utilisé pour reset password**
**Fichier:** `app/Http/Controllers/AuthController.php` (ligne 14)  
**Risque:** 🟡 MOYEN

```php
'email' => 'sometimes|nullable|string|email|unique:users',
```

**Impact:** Les utilisateurs peuvent s'inscrire sans email, rendant impossible la récupération de mot de passe.

**Recommandation:** Rendre l'email OU le téléphone obligatoire, et implémenter une récupération par SMS.

---

### 8. **Performance - Pas de mise en cache**
**Fichiers:** `app/Http/Controllers/PlatController.php`, `BoissonController.php`  
**Risque:** 🟡 MOYEN

**Impact:** Les listes de plats et boissons sont rechargées à chaque requête, causant des requêtes DB inutiles.

**Recommandation:**
```php
public function index()
{
    return Cache::remember('plats.all', 3600, function () {
        return Plat::where('is_active', true)->get();
    });
}
```

---

### 9. **Sécurité - Pas de validation CSRF sur le callback PayGate**
**Fichier:** `app/Http/Controllers/PaiementController.php` (ligne 62)  
**Risque:** 🟡 MOYEN

```php
public function callback(Request $request)
{
    $data = $request->all(); // ❌ Accepte n'importe quelle requête
```

**Impact:** Un attaquant pourrait envoyer de fausses notifications de paiement.

**Recommandation:** Implémenter une signature HMAC ou un secret partagé avec PayGate.

---

### 10. **Code quality - Migrations redondantes**
**Fichiers:** `database/migrations/`  
**Risque:** 🟡 MOYEN

Migrations problématiques:
- `2025_04_24_151113_remove_total_from_commandes_table.php`
- `2025_04_24_152115_remove_total_from_table.php`

**Impact:** Migrations qui suppriment puis rajoutent des colonnes, indiquant un manque de planification.

**Recommandation:** Nettoyer les migrations avant la production.

---

## 📝 PROBLÈMES MINEURS (Priorité 3)

### 11. **Code quality - Commentaires en français dans le code**
**Impact:** Réduit la maintenabilité pour les développeurs internationaux.

### 12. **Code quality - Pas de tests automatisés**
**Impact:** Aucun test PHPUnit détecté, augmentant le risque de régression.

### 13. **Configuration - SQLite en développement, MySQL en production**
**Fichier:** `.env.example` vs `.env`  
**Impact:** Risque de comportements différents entre environnements.

### 14. **Sécurité - Pas de validation des coordonnées GPS**
**Fichier:** `app/Http/Controllers/CommandeController.php`  
**Impact:** Coordonnées GPS non validées (latitude: -90 à 90, longitude: -180 à 180).

### 15. **Performance - N+1 queries potentielles**
**Fichier:** `app/Http/Controllers/CommandeController.php` (ligne 158)
```php
$commandes = Commande::where('user_id', $user->id)->with('plats')->get();
// ❌ Manque ->with('boissons', 'user', 'paiement')
```

---

## 🔒 RECOMMANDATIONS DE SÉCURITÉ

### Immédiat (Cette semaine)
1. ✅ Hasher les codes SMS
2. ✅ Réactiver la vérification de compte
3. ✅ Ajouter rate limiting sur auth endpoints
4. ✅ Valider les clés API au démarrage

### Court terme (Ce mois)
5. ✅ Implémenter la signature des callbacks PayGate
6. ✅ Ajouter validation stricte des coordonnées GPS
7. ✅ Créer un fichier de configuration pour PayGate
8. ✅ Ajouter logs de sécurité (tentatives de connexion, etc.)

### Moyen terme (3 mois)
9. ✅ Écrire des tests automatisés (couverture minimale 70%)
10. ✅ Implémenter un système de cache Redis
11. ✅ Ajouter monitoring (Sentry, New Relic)
12. ✅ Audit de pénétration externe

---

## 📈 MÉTRIQUES DE QUALITÉ

| Métrique | Valeur | Cible |
|----------|--------|-------|
| Couverture de tests | 0% | 70% |
| Vulnérabilités critiques | 5 | 0 |
| Vulnérabilités majeures | 5 | 0 |
| Dette technique (jours) | ~15 | <5 |
| Score de sécurité | 4/10 | 8/10 |

---

## ✅ POINTS POSITIFS

1. ✅ Utilisation de Laravel Sanctum pour l'authentification API
2. ✅ Middleware de rôles bien implémenté
3. ✅ Vérification indépendante des paiements (ne fait pas confiance au callback seul)
4. ✅ Utilisation de transactions DB pour les commandes
5. ✅ Documentation API avec Scribe
6. ✅ Structure MVC respectée
7. ✅ Utilisation de Filament pour l'admin (moderne et efficace)

---

## 🎯 PLAN D'ACTION PRIORITAIRE

### Semaine 1
- [ ] Corriger les 5 problèmes critiques
- [ ] Ajouter rate limiting
- [ ] Créer config/paygate.php

### Semaine 2
- [ ] Implémenter la signature des callbacks
- [ ] Ajouter validation GPS
- [ ] Nettoyer les migrations

### Semaine 3-4
- [ ] Écrire tests unitaires pour AuthController
- [ ] Écrire tests pour CommandeController
- [ ] Implémenter cache Redis

---

## 📚 RESSOURCES RECOMMANDÉES

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Security Best Practices](https://laravel.com/docs/12.x/security)
- [PHP Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html)

---

**Auditeur:** Kiro AI  
**Contact:** Pour toute question sur ce rapport


---

## 🔧 ANNEXE: CORRECTIFS RECOMMANDÉS

### Correctif 1: Sécuriser les codes SMS

**Fichier:** `app/Http/Controllers/AuthController.php`

```php
// ❌ AVANT
$code = random_int(1000, 9999);
$user->code_sms = $code;

// ✅ APRÈS
$code = random_int(100000, 999999); // 6 chiffres au lieu de 4
$user->code_sms = Hash::make($code);

// Dans verifyCode()
if (!Hash::check($request->code, $user->code_sms)) {
    return response()->json(['message' => 'Code incorrect.'], 400);
}
```

---

### Correctif 2: Configuration PayGate sécurisée

**Créer:** `config/paygate.php`

```php
<?php

return [
    'auth_token' => env('PAYGATE_AUTH_TOKEN'),
    'base_url' => env('PAYGATE_BASE_URL', 'https://paygateglobal.com/api/v1'),
    'callback_url' => env('PAYGATE_CALLBACK_URL'),
    'webhook_secret' => env('PAYGATE_WEBHOOK_SECRET'), // Pour signature
    'timeout' => env('PAYGATE_TIMEOUT', 300),
    
    // Validation au boot
    'validate' => function() {
        if (empty(config('paygate.auth_token'))) {
            throw new \RuntimeException('PAYGATE_AUTH_TOKEN non configuré');
        }
        if (empty(config('paygate.callback_url'))) {
            throw new \RuntimeException('PAYGATE_CALLBACK_URL non configuré');
        }
    }
];
```

**Modifier:** `app/Services/PayGateService.php`

```php
public function __construct()
{
    $this->authToken = config('paygate.auth_token');
    $this->baseUrl = config('paygate.base_url');
    $this->callbackUrl = config('paygate.callback_url');
    
    // Validation
    if (empty($this->authToken)) {
        throw new \RuntimeException('PayGate non configuré correctement');
    }
}
```

---

### Correctif 3: Rate Limiting

**Fichier:** `routes/api.php`

```php
// Endpoints publics avec rate limiting strict
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-code', [AuthController::class, 'verifyCode']);
});

// Endpoints publics avec rate limiting modéré
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/plats', [PlatController::class, 'index']);
    Route::get('/plats/categorie/{categorie}', [PlatController::class, 'platcategorie']);
    Route::get('/boissons', [BoissonController::class, 'index']);
});

// Webhook sans rate limiting mais avec signature
Route::post('/paygate/callback', [PaiementController::class, 'callback']);
```

---

### Correctif 4: Validation des coordonnées GPS

**Fichier:** `app/Http/Controllers/CommandeController.php`

```php
public function store(Request $request)
{
    $request->validate([
        'articles' => 'required|array|min:1',
        'articles.*.type' => 'required|in:plat,boisson',
        'articles.*.id' => 'required|integer',
        'articles.*.quantite' => 'required|integer|min:1',
        // ✅ Validation GPS stricte
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
        'adresse_livraison' => 'nullable|string|max:500',
        'commentaire' => 'nullable|string|max:1000',
    ]);
    
    // ✅ Validation supplémentaire: distance maximale
    $distance = $this->calculerDistance(
        config('restaurant.latitude', 6.184575120133669),
        config('restaurant.longitude', 1.2069011861319983),
        $request->latitude,
        $request->longitude
    );
    
    if ($distance > 50) { // 50 km max
        return response()->json([
            'message' => 'Zone de livraison trop éloignée (max 50 km)',
            'distance' => round($distance, 2) . ' km'
        ], 400);
    }
    
    // ... reste du code
}
```

---

### Correctif 5: Signature des callbacks PayGate

**Fichier:** `app/Http/Controllers/PaiementController.php`

```php
public function callback(Request $request)
{
    $data = $request->all();
    
    // ✅ Vérification de la signature
    $signature = $request->header('X-PayGate-Signature');
    $expectedSignature = hash_hmac('sha256', json_encode($data), config('paygate.webhook_secret'));
    
    if (!hash_equals($expectedSignature, $signature)) {
        Log::warning('Callback PayGate: signature invalide', [
            'ip' => $request->ip(),
            'data' => $data
        ]);
        return response()->json(['message' => 'Signature invalide'], 403);
    }
    
    // ✅ Vérification de l'IP (si PayGate fournit une liste d'IPs)
    $allowedIps = config('paygate.allowed_ips', []);
    if (!empty($allowedIps) && !in_array($request->ip(), $allowedIps)) {
        Log::warning('Callback PayGate: IP non autorisée', ['ip' => $request->ip()]);
        return response()->json(['message' => 'IP non autorisée'], 403);
    }
    
    // ... reste du code existant
}
```

---

### Correctif 6: Optimisation des requêtes (N+1)

**Fichier:** `app/Http/Controllers/CommandeController.php`

```php
public function index(Request $request)
{
    $user = $request->user();
    
    // ✅ Eager loading complet
    $commandes = Commande::where('user_id', $user->id)
        ->with([
            'plats',
            'boissons',
            'paiement',
            'livraison.livreur:id,name,telephone'
        ])
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($commandes);
}

public function toutes()
{
    // ✅ Eager loading pour admin
    $commandes = Commande::with([
            'user:id,name,telephone,adresse',
            'plats',
            'boissons',
            'paiement',
            'livraison.livreur:id,name'
        ])
        ->where('statut', 'en_cours')
        ->orderBy('created_at', 'desc')
        ->get();
        
    return response()->json($commandes);
}
```

---

### Correctif 7: Constantes configurables

**Créer:** `config/restaurant.php`

```php
<?php

return [
    // Coordonnées du restaurant
    'latitude' => env('RESTAURANT_LATITUDE', 6.184575120133669),
    'longitude' => env('RESTAURANT_LONGITUDE', 1.2069011861319983),
    
    // Frais de livraison
    'frais_livraison_par_km' => env('FRAIS_LIVRAISON_PAR_KM', 125),
    'frais_livraison_minimum' => env('FRAIS_LIVRAISON_MIN', 500),
    'distance_livraison_max' => env('DISTANCE_LIVRAISON_MAX', 50), // km
    
    // SMS
    'code_sms_longueur' => 6,
    'code_sms_expiration' => 30, // minutes
    
    // Email admin
    'admin_email' => env('ADMIN_EMAIL', 'admin@mrepublique.com'),
];
```

**Utilisation:**

```php
$fraisLivraison = ceil($distance * config('restaurant.frais_livraison_par_km'));
$fraisLivraison = max($fraisLivraison, config('restaurant.frais_livraison_minimum'));
```

---

### Correctif 8: Logs de sécurité

**Créer:** `app/Http/Middleware/LogSecurityEvents.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogSecurityEvents
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Logger les échecs d'authentification
        if ($request->is('api/login') && $response->status() === 401) {
            Log::warning('Tentative de connexion échouée', [
                'identifiant' => $request->input('identifiant'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }
        
        // Logger les tentatives de code SMS invalides
        if ($request->is('api/verify-code') && $response->status() === 400) {
            Log::warning('Code SMS invalide', [
                'telephone' => $request->input('telephone'),
                'ip' => $request->ip(),
            ]);
        }
        
        return $response;
    }
}
```

**Enregistrer dans:** `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(\App\Http\Middleware\LogSecurityEvents::class);
})
```

---

## 🧪 TESTS RECOMMANDÉS

### Test 1: AuthController

**Créer:** `tests/Feature/AuthControllerTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_phone()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'telephone' => '90123456',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'user']);
        
        $this->assertDatabaseHas('users', [
            'telephone' => '90123456',
        ]);
    }

    public function test_login_requires_verified_account()
    {
        $user = User::factory()->create([
            'telephone' => '90123456',
            'password' => Hash::make('password123'),
            'is_verified' => false,
        ]);

        $response = $this->postJson('/api/login', [
            'identifiant' => '90123456',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Votre compte n\'est pas encore vérifié.']);
    }

    public function test_rate_limiting_on_login()
    {
        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/api/login', [
                'identifiant' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);
        }

        $response->assertStatus(429); // Too Many Requests
    }
}
```

---

### Test 2: CommandeController

**Créer:** `tests/Feature/CommandeControllerTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plat;
use App\Models\Boisson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class CommandeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_order()
    {
        $user = User::factory()->create(['role' => 'client']);
        $plat = Plat::factory()->create(['prix' => 2000]);
        
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/commandes', [
            'articles' => [
                ['type' => 'plat', 'id' => $plat->id, 'quantite' => 2]
            ],
            'latitude' => 6.1,
            'longitude' => 1.2,
            'adresse_livraison' => '123 Rue Test',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'commande']);
    }

    public function test_order_rejects_invalid_coordinates()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/commandes', [
            'articles' => [['type' => 'plat', 'id' => 1, 'quantite' => 1]],
            'latitude' => 91, // ❌ Invalide
            'longitude' => 1.2,
        ]);

        $response->assertStatus(422);
    }
}
```

---

## 📊 CHECKLIST DE DÉPLOIEMENT

Avant de déployer en production, vérifier:

### Sécurité
- [ ] Tous les codes SMS sont hashés
- [ ] Vérification de compte activée
- [ ] Rate limiting configuré
- [ ] Clés API validées au démarrage
- [ ] Signature des callbacks PayGate implémentée
- [ ] HTTPS activé (certificat SSL valide)
- [ ] CORS configuré correctement
- [ ] Variables d'environnement sécurisées

### Performance
- [ ] Cache Redis configuré
- [ ] Eager loading sur toutes les relations
- [ ] Index de base de données optimisés
- [ ] Queue workers configurés pour les emails/SMS

### Monitoring
- [ ] Logs centralisés (Papertrail, Loggly)
- [ ] Monitoring d'erreurs (Sentry)
- [ ] Monitoring de performance (New Relic, Scout)
- [ ] Alertes configurées (downtime, erreurs critiques)

### Tests
- [ ] Tests unitaires passent (>70% couverture)
- [ ] Tests d'intégration passent
- [ ] Tests de charge effectués
- [ ] Tests de sécurité (OWASP ZAP, Burp Suite)

### Documentation
- [ ] README à jour
- [ ] Documentation API à jour (Scribe)
- [ ] Variables d'environnement documentées
- [ ] Procédures de déploiement documentées

---

**FIN DU RAPPORT D'AUDIT**
