<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchedulingsResource\Pages;
use App\Filament\Resources\SchedulingsResource\RelationManagers;
use App\Models\Scheduling;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
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
                Section::make()->schema([
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
                    DateTimePicker::make('start_time')
                        ->label('Início')
                        ->seconds(false)
                        ->displayFormat('d/m/Y')
                        ->required(),
                    DateTimePicker::make('end_time')
                        ->label('Encerramento')
                        ->seconds(false)
                        ->required(),
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'Concluído' => 'Concluído',
                            'Cancelado' => 'Cancelado',
                            'Aguardando' => 'Aguardando',
                        ])
                        ->required(),
                    Select::make('services_schedulings')
                        ->label('Servicos')
                        ->multiple()
                        // ->relationship('services_schedulings.service', 'name')
                        ->options(Service::all()->pluck('name', 'id'))
                        ->searchable()
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
                    ->dateTime(),
                TextColumn::make('end_time')
                    ->label('Encerramento')
                    ->dateTime(),
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
