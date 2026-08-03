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
            ['aminata.client@BaraSira.test', 'mariam.plombiere@BaraSira.test', 'Cliente disponible et précise. L’accès au logement était bien organisé et nos échanges ont été très professionnels.'],
            ['moussa.client@BaraSira.test', 'ibrahim.electricien@BaraSira.test', 'Le besoin était clairement expliqué et le client a facilité toute l’intervention dans ses bureaux.'],
            ['rokia.client@BaraSira.test', 'fanta.traiteur@BaraSira.test', 'Très bonne organisation et communication rapide avant comme pendant la prestation.'],
            ['mamadou.client@BaraSira.test', 'youssouf.solaire@BaraSira.test', 'Client sérieux, ponctuel et attentif aux recommandations techniques.'],
            ['moussa.client@BaraSira.test', 'boubacar.informatique@BaraSira.test', 'Échanges courtois et consignes détaillées. Le client répond rapidement lorsqu’une précision est nécessaire.'],
            ['mamadou.client@BaraSira.test', 'nana.climatisation@BaraSira.test', 'Le client a présenté clairement son besoin et les contraintes du logement dès le premier échange.'],
            ['assetou.client@BaraSira.test', 'awa.menage@BaraSira.test', 'Cliente accueillante, organisée et transparente sur ses attentes pour la mission.'],
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
