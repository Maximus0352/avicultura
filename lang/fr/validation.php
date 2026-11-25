<?php

return [
    'required' => 'Le champ :attribute est obligatoire.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    'unique' => 'La valeur du champ :attribute est déjà utilisée.',
    'in' => 'La valeur du champ :attribute est invalide.',
    'integer' => 'Le champ :attribute doit être un nombre entier.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'date' => 'Le champ :attribute doit être une date valide.',
    'exists' => 'La valeur du champ :attribute est invalide.',
    'min' => [
        'numeric' => 'Le champ :attribute doit être au moins :min.',
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'max' => [
        'numeric' => 'Le champ :attribute ne doit pas dépasser :max.',
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
    ],
    'password' => [
        'letters' => 'Le champ :attribute doit contenir au moins une lettre.',
        'numbers' => 'Le champ :attribute doit contenir au moins un chiffre.',
    ],
    'attributes' => [
        'nom' => 'nom',
        'localisation' => 'localisation',
        'type' => 'type',
        'capacite_totale' => 'capacité totale',
        'email' => 'adresse e-mail',
        'password' => 'mot de passe',
        'responsable_nom' => 'nom du responsable',
        'responsable_email' => 'e-mail du responsable',
        'responsable_password' => 'mot de passe du responsable',
        'exploitation_id' => 'exploitation',
    ],
];