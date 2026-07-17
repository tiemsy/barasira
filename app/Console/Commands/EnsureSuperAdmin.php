<?php

namespace App\Console\Commands;

use App\Services\SuperAdminProvisioner;
use Illuminate\Console\Command;
use Throwable;

class EnsureSuperAdmin extends Command
{
    protected $signature = 'superadmin:ensure';

    protected $description = 'Crée ou met à jour le compte superadministrateur depuis les variables d’environnement';

    public function handle(SuperAdminProvisioner $provisioner): int
    {
        try {
            $superAdmin = $provisioner->ensure();
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Superadmin {$superAdmin->email} provisionné avec succès.");

        return self::SUCCESS;
    }
}
