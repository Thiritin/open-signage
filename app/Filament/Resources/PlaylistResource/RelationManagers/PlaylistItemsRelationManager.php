<?php

namespace App\Filament\Resources\PlaylistResource\RelationManagers;

use App\Models\PlaylistItem;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\CreateAction;
use Filament\Pages\Actions\EditAction;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Support\Facades\Route;

class PlaylistItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'playlistItems';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
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
                    ->relationship('page', 'name')
                    ->columnSpan(5)
                    ->required(),

                Select::make('layout_id')
                    ->relationship('layout', 'name')
                    ->columnSpan(5)
                    ->required(),
            ])->columns(12)->columnSpanFull(),
            TextInput::make('title')->columnSpanFull(),
        ];

        if (isset(request()->get('updates')[0]['payload']['params'][1]) || isset(request()->get('serverMemo')['data']['mountedTableActionRecord'])) {
            $id = request()->get('updates')[0]['payload']['params'][1] ?? request()->get('serverMemo')['data']['mountedTableActionRecord'];
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
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextInputColumn::make('title'),

                SelectColumn::make('page_id')->options(
                    \App\Models\Page::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()
                )->disablePlaceholderSelection(),

                SelectColumn::make('layout_id')->options(
                    \App\Models\Layout::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()
                )->disablePlaceholderSelection(),
            ])
            ->headerActions([
                \Filament\Tables\Actions\CreateAction::make()->modalWidth('7xl')
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make()->modalWidth('7xl'),
                DeleteAction::make()
            ])
            ->bulkActions([
                DeleteBulkAction::make()
            ]);
    }
}
