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
        $tenantAccount->tenant->id = $tenantAccount->tenant->id . '//deleted_' . md5(uniqid());
        $tenantAccount->tenant->save();

        $tenantAccount->tenant->delete();
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
