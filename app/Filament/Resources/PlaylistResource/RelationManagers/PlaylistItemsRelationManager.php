<?php

namespace App\Filament\Resources\PlaylistResource\RelationManagers;

use App\Models\PlaylistItem;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Columns\CheckboxColumn;
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
                    ->label('Page')
                    ->columnSpan(5)
                    ->required(),

                Select::make('layout_id')
                    ->relationship('layout', 'name', fn (Builder $query) => $query->normal())
                    ->label('Layout')
                    ->columnSpan(5)
                    ->required(),

                DateTimePicker::make('starts_at')
                    ->columnSpan(6),

                DateTimePicker::make('ends_at')
                    ->columnSpan(6),
            ])->columns(12)->columnSpanFull(),
            TextInput::make('title')->columnSpanFull(),
        ];
        if ($this->mountedTableActionRecord) {
            $id = $this->mountedTableActionRecord;
            $page = PlaylistItem::find($id)->page;
            if ($page->schema) {
                $array = [];
                foreach ($page->schema as $field) {
                    if($field['type'] === "ImageInput") {
                        $item = FileUpload::make('content.' . $field['property'])->image();
                    } else {
                        $class = 'Filament\\Forms\\Components\\' . $field['type'];
                        $item = $class::make('content.' . $field['property']);
                    }

                    if($field['type'] === "Select") {
                        $item = $item->options($field['options']);
                    }

                    if(isset($field['required']) && $field['required']) {
                        $item = $item->required();
                    }

                    $item = $item->label($field['name'])
                        ->columnSpanFull();
                    $array[] = $item;
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
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextInputColumn::make('title'),

                TextInputColumn::make('duration')->type('number'),

                SelectColumn::make('page_id')
                    ->options(\App\Models\Page::normal()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray())->selectablePlaceholder(false),

                SelectColumn::make('layout_id')->options(
                    \App\Models\Layout::normal()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()
                )->selectablePlaceholder(false),

                CheckboxColumn::make('is_active')
            ])
            ->reorderable('sort')
            ->paginated(false)
            ->headerActions([
                \Filament\Tables\Actions\CreateAction::make()->modalWidth('7xl'),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make()->modalWidth('7xl'),
                ReplicateAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
