<?php

namespace Database\Seeders;

use App\Services\SuperAdminProvisioner;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(SuperAdminProvisioner $provisioner): void
    {
        $superAdmin = $provisioner->ensure();

        $this->command?->info("Superadmin {$superAdmin->email} provisionné avec succès.");
    }
}
