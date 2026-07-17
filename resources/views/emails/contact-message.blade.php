<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau message de contact</title>
</head>
<body>
    <h1>Nouveau message depuis Barasira</h1>

    <p>
        <strong>Nom :</strong>
        {{ $contact['name'] }}
    </p>

    <p>
        <strong>E-mail :</strong>
        {{ $contact['email'] }}
    </p>

    <p>
        <strong>Téléphone :</strong>
        {{ $contact['phone'] ?: 'Non renseigné' }}
    </p>

    <p>
        <strong>Profil :</strong>
        {{ $contact['user_type'] ?: 'Non renseigné' }}
    </p>

    <p>
        <strong>Sujet :</strong>
        {{ $contact['subject'] }}
    </p>

    <hr>

    <p>
        {!! nl2br(e($contact['message'])) !!}
    </p>
</body>
</html>
