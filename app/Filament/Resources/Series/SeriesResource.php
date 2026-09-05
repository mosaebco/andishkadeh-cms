<?php

namespace App\Filament\Resources\Series;

use App\Filament\Resources\Series\Pages\CreateSeries;
use App\Filament\Resources\Series\Pages\EditSeries;
use App\Filament\Resources\Series\Pages\ListSeries;
use App\Models\Series;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SeriesResource extends Resource
{
    protected static ?string $model = Series::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Series';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Series details')
                ->columns(2)
                ->schema([
                    TextInput::make('title')->required()->maxLength(180),
                    TextInput::make('slug')->nullable()->alphaDash()->unique(ignoreRecord: true)->maxLength(200)->helperText('Leave blank to generate from the title.'),
                    Textarea::make('description')->rows(5)->maxLength(1200)->columnSpanFull(),
                    FileUpload::make('cover_image_path')
                        ->label('Cover image')
                        ->image()
                        ->imageEditor()
                        ->disk(config('media.disk'))
                        ->directory('andishkadeh/series')
                        ->visibility('public')
                        ->maxSize(config('media.max_size_kb.image'))
                        ->preventFilePathTampering(),
                    TextInput::make('sort_order')->numeric()->default(0)->minValue(0),
                ]),
            Section::make('Publishing')
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                            'archived' => 'Archived',
                        ])
                        ->default('draft')
                        ->required(),
                    DateTimePicker::make('published_at')
                        ->label('Publish at')
                        ->seconds(false)
                        ->helperText('Published items appear only after this time.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('cover_image_path')->label('')->disk(config('media.disk'))->square(),
                TextColumn::make('title')->searchable()->weight('bold'),
                TextColumn::make('posts_count')->counts('posts')->label('Posts'),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('published_at')->dateTime()->sortable(),
            ])
            ->filters([SelectFilter::make('status')->options([
                'draft' => 'Draft',
                'scheduled' => 'Scheduled',
                'published' => 'Published',
                'archived' => 'Archived',
            ])])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeries::route('/'),
            'create' => CreateSeries::route('/create'),
            'edit' => EditSeries::route('/{record}/edit'),
        ];
    }
}
