<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchedulingsResource\Pages;
use App\Filament\Resources\SchedulingsResource\RelationManagers;
use App\Models\Scheduling;
use App\Models\Service;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SchedulingsResource extends Resource
{
    protected static ?string $model = Scheduling::class;

    protected static ?string $modelLabel = 'Agendamento';

    protected static ?string $pluralModelLabel = 'Agendamentos';

    protected static ?string $navigationGroup = 'Configurações de Agendamentos';

    protected static ?string $navigationIcon = 'heroicon-s-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome Do Cliente')
                            ->required()
                            ->minLength(2)
                            ->maxLength(255),
                        TextInput::make('whats')
                            ->label('WhatsApp')
                            ->tel()
                            ->mask('(99) 99999-9999')
                            ->maxLength(15)
                            ->required(),
                        Grid::make()
                            ->schema([
                                Flatpickr::make('start_time')
                                    ->label('Início')
                                    ->dateFormat('d/m/Y H:i')
                                    ->enableTime()
                                    // ->locale('pt')
                                    ->altInputClass('sample-pt')
                                    ->use24hr(true)
                                    ->required(),
                                Flatpickr::make('end_time')
                                    ->label('Encerramento')
                                    ->dateFormat('d/m/Y H:i')
                                    ->enableTime()
                                    ->use24hr(true)
                                    ->required(),
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'Concluído' => 'Concluido',
                                        'Cancelado' => 'Cancelado',
                                        'Aguardando' => 'Aguardando',
                                    ])
                                    ->searchable()
                                    ->required(),
                                Select::make('services_schedulings')
                                    ->label('Serviços')
                                    ->multiple()
                                    ->options(Service::all()->pluck('name', 'id'))
                                    ->searchable()
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome Do Cliente'),
                TextColumn::make('whats')
                    ->label('WhatsApp'),
                TextColumn::make('start_time')
                    ->label('Início')
                    ->dateTime('d/m/Y H:i', 'America/Sao_Paulo'),
                TextColumn::make('end_time')
                    ->label('Encerramento')
                    ->dateTime('d/m/Y H:i', 'America/Sao_Paulo'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aguardando' => 'warning',
                        'Cancelado' => 'danger',
                        'Concluído' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('services_schedulings.service.name')
                    ->label('Servicos'),
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
            'index' => Pages\ListSchedulings::route('/'),
            'create' => Pages\CreateSchedulings::route('/create'),
            'edit' => Pages\EditSchedulings::route('/{record}/edit'),
        ];
    }
}
