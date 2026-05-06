<?php

namespace App\Filament\Resources\NoRecordInquiries;

use App\Filament\Resources\NoRecordInquiries\Pages\CreateNoRecordInquiry;
use App\Filament\Resources\NoRecordInquiries\Pages\EditNoRecordInquiry;
use App\Filament\Resources\NoRecordInquiries\Pages\ListNoRecordInquiries;
use App\Filament\Resources\NoRecordInquiries\Schemas\NoRecordInquiryForm;
use App\Filament\Resources\NoRecordInquiries\Tables\NoRecordInquiriesTable;
use App\Models\NoRecordInquiry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NoRecordInquiryResource extends Resource
{
    protected static ?string $model = NoRecordInquiry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'student_name';

    public static function form(Schema $schema): Schema
    {
        return NoRecordInquiryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NoRecordInquiriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNoRecordInquiries::route('/'),
            'create' => CreateNoRecordInquiry::route('/create'),
            'edit' => EditNoRecordInquiry::route('/{record}/edit'),
        ];
    }
}
