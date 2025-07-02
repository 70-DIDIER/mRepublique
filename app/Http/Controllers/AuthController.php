<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AfrikSmsService;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'sometimes|nullable|string|email|unique:users',
            'password' => 'required|string|confirmed',
            'role' => 'required|in:client,livreur,admin',
            'adresse' => 'sometimes|nullable|string',
            'telephone' => 'sometimes|nullable|string|unique:users,telephone',
            'photo' => 'sometimes|nullable|string',
        ]);


        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'] ?? null,
            'password' => bcrypt($fields['password']),
            'role' => $fields['role'],
            'adresse' => $fields['adresse'] ?? null,
            'telephone' => $fields['telephone'] ?? null,
            'photo' => $fields['photo'] ?? null,
        ]);

        // Si un numéro de téléphone est fourni, générer et envoyer le code de confirmation par SMS
        if (!empty($fields['telephone'])) {
            $code = random_int(1000, 9999);
            $user->code_sms = $code;
            $user->code_expires_at = now()->addMinutes(10);
            $user->save();

            $numero = $user->telephone;
            // Ajouter automatiquement 228 si ce n'est pas déjà présent
            if (!str_starts_with($numero, '228')) {
                $numero = '228' . ltrim($numero, '0'); // Supprime 0 devant
            }
            // Préparer le message
            $message = "Bienvenue chez le restaurant M'Republique !!! Veuillez activer votre compte avec le code d'activation suivant : $code";

            // Envoyer le SMS
            app(AfrikSmsService::class)->sendSms($numero, $message);

            return response()->json([
                'message' => 'Utilisateur enregistré. Un code de confirmation a été envoyé par SMS.',
                'user' => $user
            ], 201);
        }

        // Si l'inscription s'est faite avec email (sans téléphone), ne pas envoyer de code
        return response()->json([
            'message' => 'Utilisateur enregistré.',
            'user' => $user
        ], 201);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'telephone' => 'required|string',
            'code' => 'required|string',
        ]);

        // Chercher l'utilisateur par numéro de téléphone
        $user = User::where('telephone', $request->telephone)->first();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        // Vérifier que le code est correct
        if ($user->code_sms !== $request->code) {
            return response()->json(['message' => 'Code incorrect.'], 400);
        }

        // Vérifier que le code n'a pas expiré
        if (now()->greaterThan($user->code_expires_at)) {
            return response()->json(['message' => 'Code expiré.'], 400);
        }

        // Valider l'utilisateur (par exemple changer un statut ou un champ is_verified)
        $user->is_verified = true; // ajoute ce champ dans ta table users si besoin
        $user->code_sms = null; // Nettoyer le code
        $user->code_expires_at = null; // Nettoyer l'expiration
        $user->save();

        return response()->json(['message' => 'Compte vérifié avec succès.'], 200);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'identifiant' => 'required|string',
            'password' => 'required|string',
        ]);

         // On recherche par email ou téléphone
            $user = User::where('email', $request->identifiant)
            ->orWhere('telephone', $request->identifiant)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Identifiants incorrects.'], 401);
        }

        // if (! $user->is_verified) {
        // return response()->json(['message' => 'Votre compte n\'est pas encore vérifié.'], 403);
        // }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
        'user' => $user,
        'token' => $token,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Déconnecté avec succès']);
    }
}
