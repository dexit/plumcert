<?php
namespace App\Filament\Resources\Jobs\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class TimeEntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'timeEntries';
    protected static ?string $title = 'Time on Site';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DateTimePicker::make('clocked_in_at')->label('Clocked In')->required(),
            DateTimePicker::make('clocked_out_at')->label('Clocked Out'),
            TextInput::make('minutes')->label('Minutes')->numeric()->readOnly(),
            TextInput::make('billable_rate')->label('Rate (£/hr)')->numeric()->prefix('£'),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Engineer'),
                Tables\Columns\TextColumn::make('clocked_in_at')->label('In')->dateTime('d M H:i'),
                Tables\Columns\TextColumn::make('clocked_out_at')->label('Out')->dateTime('d M H:i')->placeholder('Active'),
                Tables\Columns\TextColumn::make('minutes')->label('Duration')
                    ->formatStateUsing(fn ($state) => $state ? intdiv($state, 60) . 'h ' . ($state % 60) . 'm' : '—'),
                Tables\Columns\TextColumn::make('billable_rate')->label('Rate')->money('GBP')->placeholder('—'),
                Tables\Columns\TextColumn::make('notes')->limit(40)->placeholder('—'),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->defaultSort('clocked_in_at', 'desc');
    }
}
