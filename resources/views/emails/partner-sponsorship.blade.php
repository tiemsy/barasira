<h1>Nouvelle demande de publication sponsorisée</h1>
<p><strong>Entreprise :</strong> {{ $requestData['company_name'] }}</p>
<p><strong>Contact :</strong> {{ $requestData['contact_name'] }}</p>
<p><strong>Email :</strong> {{ $requestData['email'] }}</p>
<p><strong>Téléphone :</strong> {{ $requestData['phone'] }}</p>
<p><strong>Site web :</strong> {{ $requestData['website_url'] ?: 'Non renseigné' }}</p>
<p><strong>Catégorie :</strong> {{ $requestData['category'] === 'other' ? $requestData['other_category'] : $requestData['category'] }}</p>
<p><strong>Formule :</strong> {{ $requestData['plan'] }} — {{ number_format($plan['price'], 0, ',', ' ') }} FCFA / {{ $plan['duration_days'] }} jours</p>
@if (! empty($requestData['message']))
    <p><strong>Message :</strong></p>
    <p>{{ $requestData['message'] }}</p>
@endif
