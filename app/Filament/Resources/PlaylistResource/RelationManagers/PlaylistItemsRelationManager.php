<?php

namespace App\Filament\Resources\PlaylistResource\RelationManagers;

use App\Models\PlaylistItem;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PlaylistItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'playlistItems';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        $formSchema = [
            Group::make([
                TextInput::make('duration')
                    ->numeric()
                    ->minValue(1)
                    ->suffix('seconds')
                    ->columnSpan(2)
                    ->required(),

                Select::make('page_id')
                    ->relationship('page', 'name', fn (Builder $query) => $query->normal())
                    ->columnSpan(5)
                    ->required(),

                Select::make('layout_id')
                    ->relationship('layout', 'name', fn (Builder $query) => $query->normal())
                    ->columnSpan(5)
                    ->required(),
            ])->columns(12)->columnSpanFull(),
            TextInput::make('title')->columnSpanFull(),
        ];
        if ($this->mountedTableActionRecord) {
            $id = $this->mountedTableActionRecord;
            $page = PlaylistItem::find($id)->page;
            if ($page->schema) {
                $array = [];
                foreach ($page->schema as $field) {
                    $class = 'Filament\\Forms\\Components\\'.$field['type'];
                    $array[] = $class::make('content.'.$field['property'])
                        ->label($field['name'])
                        ->columnSpanFull();
                }
                $formSchema[] = Section::make('Page Content')
                    ->schema($array)
                    ->columns(12)
                    ->columnSpanFull();
            }
        }

        return $form
            ->schema($formSchema);
    }

    /**
     * @throws \Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextInputColumn::make('title'),

                TextInputColumn::make('duration')->type('number'),

                SelectColumn::make('page_id')->options(
                    \App\Models\Page::normal()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()
                )->selectablePlaceholder(false),

                SelectColumn::make('layout_id')->options(
                    \App\Models\Layout::normal()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()
                )->selectablePlaceholder(false),
            ])
            ->reorderable('sort')
            ->paginated(false)
            ->headerActions([
                \Filament\Tables\Actions\CreateAction::make()->modalWidth('7xl'),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make()->modalWidth('7xl'),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
