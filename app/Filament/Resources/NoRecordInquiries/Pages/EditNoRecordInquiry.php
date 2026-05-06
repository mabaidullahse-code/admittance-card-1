<?php

namespace App\Filament\Resources\NoRecordInquiries\Pages;

use App\Filament\Resources\NoRecordInquiries\NoRecordInquiryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNoRecordInquiry extends EditRecord
{
    protected static string $resource = NoRecordInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
