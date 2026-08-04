<?php

namespace App\Filament\Resources\Photos\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PhotoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('path')
                    ->image()
                    ->directory('photos')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('caption'),
                Select::make('phase')
                    ->options([
                        'start' => 'Start',
                        'during' => 'During',
                        'finish' => 'Finish',
                        'defect' => 'Defect',
                    ])
                    ->default('during'),
                Select::make('uploaded_by_user_id')
                    ->relationship('uploadedBy', 'name'),
            ]);
    }
}
