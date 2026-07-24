<?php

namespace Database\Seeders;

use App\Models\ClientComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientCommentSeeder extends Seeder
{
    public function run(): void
    {
        $comments = [
            ['aminata.client@barasira.test', 'mariam.plombiere@barasira.test', 'Cliente disponible et précise. L’accès au logement était bien organisé et nos échanges ont été très professionnels.'],
            ['moussa.client@barasira.test', 'ibrahim.electricien@barasira.test', 'Le besoin était clairement expliqué et le client a facilité toute l’intervention dans ses bureaux.'],
            ['rokia.client@barasira.test', 'fanta.traiteur@barasira.test', 'Très bonne organisation et communication rapide avant comme pendant la prestation.'],
            ['mamadou.client@barasira.test', 'youssouf.solaire@barasira.test', 'Client sérieux, ponctuel et attentif aux recommandations techniques.'],
            ['moussa.client@barasira.test', 'boubacar.informatique@barasira.test', 'Échanges courtois et consignes détaillées. Le client répond rapidement lorsqu’une précision est nécessaire.'],
            ['mamadou.client@barasira.test', 'nana.climatisation@barasira.test', 'Le client a présenté clairement son besoin et les contraintes du logement dès le premier échange.'],
            ['assetou.client@barasira.test', 'awa.menage@barasira.test', 'Cliente accueillante, organisée et transparente sur ses attentes pour la mission.'],
        ];

        foreach ($comments as [$clientEmail, $providerEmail, $comment]) {
            ClientComment::query()->updateOrCreate([
                'client_id' => User::query()->where('email', $clientEmail)->value('id'),
                'commenter_id' => User::query()->where('email', $providerEmail)->value('id'),
            ], ['comment' => $comment]);
        }

        $this->command->info('Commentaires sur les profils clients créés avec succès.');
    }
}
