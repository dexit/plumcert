<?php

declare(strict_types=1);

namespace App\Filament\Forms;

use App\Services\NominatimLookup;
use Filament\Forms\Components\Select;
use Filament\Forms\Set;

/**
 * Reusable OpenStreetMap address autocomplete field.
 *
 * Drops a searchable Select above the address fields; choosing a result fills
 * the address / town / county / postcode fields. The lookup field itself is
 * not persisted (dehydrated false).
 */
class AddressLookup
{
    /**
     * @param  string  $addressField  name of the textarea/text field for line 1
     */
    public static function make(string $addressField = 'address'): Select
    {
        return Select::make('address_lookup')
            ->label('Find address (OpenStreetMap)')
            ->placeholder('Start typing a postcode or street…')
            ->helperText('Search powered by OpenStreetMap. Pick a result to auto-fill the fields below.')
            ->searchable()
            ->dehydrated(false)
            ->live()
            ->getSearchResultsUsing(fn (string $search): array => NominatimLookup::options($search))
            ->afterStateUpdated(function (?string $state, Set $set) use ($addressField) {
                $data = NominatimLookup::decode($state);
                if (! $data) {
                    return;
                }

                $set($addressField, $data['line1']);
                $set('postcode', $data['postcode']);
                $set('town', $data['town']);
                $set('county', $data['county']);
            })
            ->columnSpanFull();
    }
}
