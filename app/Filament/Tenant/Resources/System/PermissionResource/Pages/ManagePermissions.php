<?php

namespace App\Filament\Tenant\Resources\System\PermissionResource\Pages;

use App\Filament\Tenant\Resources\System\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePermissions extends ManageRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
