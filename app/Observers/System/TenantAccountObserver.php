<?php

namespace App\Observers\System;

use App\Models\System\TenantAccount;

class TenantAccountObserver
{
    /**
     * Handle the Tenant "created" event.
     */
    public function created(TenantAccount $tenantAccount): void
    {
        //
    }

    /**
     * Handle the Tenant "updated" event.
     */
    public function updated(TenantAccount $tenantAccount): void
    {
        //
    }

    /**
     * Handle the Tenant "deleted" event.
     */
    public function deleted(TenantAccount $tenantAccount): void
    {
        $suffix = '//deleted_' . md5(uniqid());

        $tenantAccount->cpf_cnpj = !empty($tenantAccount->cpf_cnpj) ? $tenantAccount->cpf_cnpj . $suffix : null;
        $tenantAccount->save();

        // $tenant = $tenantAccount->tenant;

        // $tenant->id = $tenant->id . $suffix;
        // $tenant->save();

        // foreach ($tenant->domains as $domain) {
        //     $domain->domain = $domain->domain . $suffix;
        //     $domain->save();
        // }

        // $tenant->delete();
    }

    /**
     * Handle the Tenant "restored" event.
     */
    public function restored(TenantAccount $tenantAccount): void
    {
        //
    }

    /**
     * Handle the Tenant "force deleted" event.
     */
    public function forceDeleted(TenantAccount $tenantAccount): void
    {
        //
    }
}
