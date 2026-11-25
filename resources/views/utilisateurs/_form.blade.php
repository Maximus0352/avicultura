<x-champ nom="nom" label="Nom complet" :valeur="$utilisateur->nom ?? null" />
<x-champ nom="email" label="Adresse e-mail" type="email" :valeur="$utilisateur->email ?? null" />
<x-champ nom="password"
         :label="isset($utilisateur) ? 'Nouveau mot de passe (laisser vide pour ne pas le changer)' : 'Mot de passe (8 caractères minimum, lettres et chiffres)'"
         type="password" autocomplete="new-password" />