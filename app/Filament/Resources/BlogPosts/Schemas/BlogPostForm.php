<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\BlogPosts\Schemas;

use App\Enums\BlogStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // I set author_id silently to the authenticated admin — it should never be a manual field.
                Hidden::make('author_id')
                    ->default(Auth::id(...)),

                Section::make('Post Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Why Japanese Imports Are a Smart Buy in Ghana')
                            ->live(debounce: 500)
                            // The slug field is readOnly — the admin can never type into it
                            // directly — so it's always safe to keep it in sync with the
                            // title, on both create and edit.
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state)))
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->maxLength(255)
                            ->placeholder('auto-generated from title')
                            // I use readOnly() instead of disabled() — disabled fields are
                            // unreliable about reflecting programmatic $set() updates in the
                            // browser, which was why the live preview never showed up.
                            ->readOnly()
                            // Belt-and-braces: if the live preview above never fired (e.g. the
                            // title was pasted without a keyup event), I still compute the
                            // slug from the title here at save time.
                            ->dehydrateStateUsing(fn ($state, $get) => filled($state) ? $state : Str::slug($get('title')))
                            ->columnSpanFull(),

                        Select::make('blog_category_id')
                            ->relationship('category', 'name')
                            ->label('Category')
                            ->placeholder('Select a category')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->placeholder('e.g. Import Tips')
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        if (blank($get('slug'))) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),
                                TextInput::make('slug')
                                    ->required()
                                    ->placeholder('auto-generated from name')
                                    ->disabled()
                                    ->dehydrated(),
                            ])
                            ->createOptionAction(
                                fn ($action) => $action->modalWidth('sm')
                            ),

                        Select::make('status')
                            ->options(BlogStatus::class)
                            ->default(BlogStatus::Draft)
                            ->required()
                            ->live(),

                        DateTimePicker::make('published_at')
                            ->label('Publish At')
                            ->default(now())
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                // I auto-switch to Scheduled when the chosen time is in the future,
                                // and back to Published when it's now or in the past.
                                if ($state && now()->lt($state)) {
                                    $set('status', BlogStatus::Scheduled->value);
                                } else {
                                    $set('status', BlogStatus::Published->value);
                                }
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('Featured Image')
                    ->schema([
                        FileUpload::make('cover_image_path')
                            ->label('Featured Image')
                            ->image()
                            ->imagePreviewHeight('200')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(3072) // 3 MB
                            ->disk('public')
                            ->directory('blog-covers')
                            ->columnSpanFull(),

                        // I auto-populate excerpt from the first 290 chars of body so the admin
                        // doesn't have to write it separately — they can still override it.
                        Textarea::make('excerpt')
                            ->maxLength(290)
                            ->placeholder('Auto-populated from content — edit to customise')
                            ->rows(3)
                            // Belt-and-braces, same as slug below: if the live preview never
                            // fired in the browser, I still compute the excerpt from body here.
                            // I limit to 287 (not 290) since Str::limit appends a 3-char "..."
                            // suffix on top of the length it's given.
                            ->dehydrateStateUsing(fn ($state, $get) => filled($state) ? $state : Str::limit(strip_tags((string) $get('body')), 287))
                            ->columnSpanFull(),
                    ]),

                Section::make('Content')
                    ->columnSpanFull()
                    ->schema([
                        RichEditor::make('body')
                            ->required()
                            ->placeholder('Write your post content here...')
                            ->live(debounce: 800)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                // I strip HTML tags and trim to 287 chars (plus Str::limit's
                                // own "..." suffix) so the excerpt never exceeds 290 total.
                                if (blank($get('excerpt'))) {
                                    $set('excerpt', Str::limit(strip_tags($state), 287));
                                }
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
