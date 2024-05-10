<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkingHoursResource\Pages;
use App\Filament\Resources\WorkingHoursResource\RelationManagers;
use App\Models\WorkingHours;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class WorkingHoursResource extends Resource
{
    protected static ?string $model = WorkingHours::class;

    protected static ?string $modelLabel = 'Jornada de trabalho';

    protected static ?string $pluralModelLabel = 'Jornadas de trabalho';

    protected static ?string $navigationGroup = 'Configurações de Agendamentos';

    protected static ?string $navigationIcon = 'heroicon-s-clock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Select::make('weekday')
                        ->label('Dias da semana')
                        ->options([
                            '0' => 'Domingo',
                            '1' => 'Segunda-feira',
                            '2' => 'Terça-feira',
                            '3' => 'Quarta-feira',
                            '4' => 'Quinta-feira',
                            '5' => 'Sexta-feira',
                            '6' => 'Sábado',
                        ])
                        ->required(),
                    TextInput::make('open_time')
                        ->label('Início')
                        ->mask('99:99')
                        ->placeholder('--:--')
                        ->required(),
                    TextInput::make('close_time')
                        ->label('Encerramento')
                        ->placeholder('--:--')
                        ->mask('99:99')
                        ->required()
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SelectColumn::make('weekday')
                    ->label('Nome Do Cliente')
                    ->options([
                        '0' => 'Domingo',
                        '1' => 'Segunda-feira',
                        '2' => 'Terça-feira',
                        '3' => 'Quarta-feira',
                        '4' => 'Quinta-feira',
                        '5' => 'Sexta-feira',
                        '6' => 'Sábado',
                    ])
                    ->selectablePlaceholder(false),
                TextColumn::make('open_time')
                    ->label('Início'),
                TextColumn::make('close_time')
                    ->label('Encerramento'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListWorkingHours::route('/'),
            'create' => Pages\CreateWorkingHours::route('/create'),
            'edit' => Pages\EditWorkingHours::route('/{record}/edit'),
        ];
    }
}
