<?php

namespace App\Filament\Resources\System\TenantAccountResource\Pages;

use App\Filament\Resources\System\TenantAccountResource;
use App\Models\System\Tenant;
use App\Models\System\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class CreateTenantAccount extends CreateRecord
{
    protected static string $resource = TenantAccountResource::class;

    protected ?bool $hasDatabaseTransactions = false;

    protected ?Tenant $tenant = null;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['holder_name'] = $data['user']['name'];

        return $data;
    }

    protected function beforeCreate(): void
    {
        $this->createTenant();
        $this->createDomain();
        $this->createAdminUser();
    }

    protected function afterCreate(): void
    {
        $this->createAddress();
    }

    protected function createTenant(): void
    {
        $this->tenant = Tenant::create([
            'id' => $this->data['tenant_id']
        ]);
    }

    protected function createDomain(): void
    {
        $baseDomain = config('tenancy.base_domain');
        $domain = "{$this->tenant->id}.{$baseDomain}";

        $this->tenant->domains()
            ->create([
                'domain' => $domain
            ]);
    }

    protected function createAdminUser(): void
    {
        tenancy()->initialize($this->tenant);

        if ($this->data['user']['password']) {
            $this->data['user']['password'] = Hash::make($this->data['user']['password']);
        }

        $user = User::create($this->data['user']);

        $user->assignRole('Administrador');

        tenancy()->end();
    }

    protected function createAddress(): void
    {
        $this->data['address']['is_main'] = true;

        $this->record->address()
            ->create($this->data['address']);
    }
}
