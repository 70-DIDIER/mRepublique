<h2>Nouvelle commande reçue</h2>

<p><strong>Client :</strong> {{ $commande->user->name }}</p>
<p><strong>Email :</strong> {{ $commande->user->email }}</p>
<p><strong>Téléphone :</strong> {{ $commande->user->telephone }}</p>
<p><strong>Montant :</strong> {{ $commande->montant_total }} FCFA</p>
<p><strong>Type de livraison :</strong> {{ $commande->type_livraison }}</p>
<p><strong>Adresse :</strong> {{ $commande->adresse_livraison }}</p>
<p><strong>Date :</strong> {{ $commande->created_at->format('d/m/Y H:i') }}</p>
<p>Consultez le <a href="https://apirestaurant.mrepublique.com/commandes">tableau de bord</a>  pour gérer cette commande.</p>