<?php

namespace App\Filament\Resources\Playlists\RelationManagers;

use App\Models\Layout;
use App\Models\Page;
use App\Models\PlaylistItem;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class PlaylistItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'playlistItems';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                        ->required()
                        ->reactive(),

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

                Section::make('Page Content')
                    ->columnSpanFull()
                    ->hidden(fn (Get $get) => ! $this->shouldDisplayPageContent($get('page_id')))
                    ->schema(fn (Get $get) => $this->buildPageContentSchema($get('page_id'))),
            ]);
    }

    private function shouldDisplayPageContent(?int $pageId): bool
    {
        if (! $pageId) {
            return false;
        }

        $page = Page::find($pageId);

        return $page && $page->schema;
    }

    /**
     * @throws Exception
     */
    private function buildPageContentSchema(?int $pageId): array
    {
        if (! $pageId) {
            return [];
        }

        $page = Page::find($pageId);

        if (! $page || ! $page->schema) {
            return [];
        }

        $contentSchema = [];

        foreach ($page->schema as $field) {
            if ($field['type'] === 'ImageInput') {
                $item = FileUpload::make('content.' . $field['property'])->image();
            } else {
                $class = 'Filament\\Forms\\Components\\' . $field['type'];

                if (! class_exists($class)) {
                    Log::error("Class {$class} does not exist. Load attempted by page: {$page->name} ({$pageId})");

                    continue;
                }

                $item = $class::make('content.' . $field['property']);
            }

            if ($field['type'] === 'Select') {
                $item = $item->options($field['options']);
            }

            if (isset($field['required']) && $field['required']) {
                $item = $item->required();
            }

            $item = $item->label($field['name'])
                ->columnSpanFull();

            $contentSchema[] = $item;
        }

        return $contentSchema;
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
