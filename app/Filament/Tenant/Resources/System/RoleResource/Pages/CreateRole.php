<?php

namespace App\Filament\Tenant\Resources\System\RoleResource\Pages;

use App\Filament\Tenant\Resources\System\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
