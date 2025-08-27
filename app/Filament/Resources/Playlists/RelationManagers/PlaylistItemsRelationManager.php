<?php

namespace App\Filament\Resources\Playlists\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use App\Models\Page;
use App\Models\Layout;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use App\Models\PlaylistItem;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PlaylistItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'playlistItems';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Schema $schema): Schema
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

        if ($this->ownerRecord) {
            $id = $this->ownerRecord["id"];
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

        return $schema
            ->components($formSchema);
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
                    ->options(Page::normal()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray())->selectablePlaceholder(false),

                SelectColumn::make('layout_id')->options(
                    Layout::normal()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()
                )->selectablePlaceholder(false),

                CheckboxColumn::make('is_active')
            ])
            ->reorderable('sort')
            ->defaultSort('sort')
            ->paginated(false)
            ->headerActions([
                CreateAction::make()->modalWidth('7xl'),
            ])
            ->recordActions([
                Action::make('Move')->schema([
                    Select::make('playlist_id')
                        ->relationship('playlist', 'name', fn (Builder $query) => $query->normal())
                ])->action(fn (PlaylistItem $record, array $data) => $record->update([
                    'playlist_id' => $data['playlist_id'],
                ])),
                Action::make('Copy to..')->schema([
                    Select::make('playlist_id')
                        ->relationship('playlist', 'name', fn (Builder $query) => $query->normal())
                ])->action(fn (PlaylistItem $record, array $data) => PlaylistItem::create(array_merge($record->only([
                    'title',
                    'starts_at',
                    'ends_at',
                    'duration',
                    'page_id',
                    'layout_id',
                    'is_active',
                    'content',
                    'sort'
                ]), [
                    'playlist_id' => $data['playlist_id'],
                ]))),
                EditAction::make()->modalWidth('7xl'),
                ReplicateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
