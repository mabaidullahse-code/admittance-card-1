<?php

namespace App\Filament\Resources\NoRecordInquiries\Pages;

use App\Filament\Resources\NoRecordInquiries\NoRecordInquiryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNoRecordInquiries extends ListRecords
{
    protected static string $resource = NoRecordInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
