<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\Cars\Schemas;

use App\Enums\CarBodyType;
use App\Enums\CarStatus;
use App\Models\Car;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Vehicle Details')
                    ->columns(2)
                    ->schema([
                        Select::make('make_id')
                            ->label('Make')
                            ->relationship('make', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($set) => $set('car_model_id', null))
                            ->placeholder('Select or create a make')
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->unique('makes', 'name')
                                    ->placeholder('e.g. Toyota')
                                    ->maxLength(100),
                                FileUpload::make('icon_path')
                                    ->label('Brand Icon')
                                    ->image()
                                    ->imagePreviewHeight('80')
                                    ->acceptedFileTypes(['image/png', 'image/svg+xml', 'image/webp', 'image/avif'])
                                    ->maxSize(512)
                                    ->disk('public')
                                    ->directory('make-icons'),
                            ])
                            ->createOptionAction(fn ($action) => $action->modalWidth('sm')),

                        Select::make('car_model_id')
                            ->label('Model')
                            ->required()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn ($set) => $set('car_trim_id', null))
                            ->placeholder(fn ($get) => $get('make_id') ? 'Select or add a model' : 'Select a make first')
                            ->disabled(fn ($get) => ! $get('make_id'))
                            ->options(fn ($get) => $get('make_id')
                                ? \App\Models\CarModel::where('make_id', $get('make_id'))
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                : []
                            )
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->placeholder('e.g. Corolla')
                                    ->maxLength(100),
                            ])
                            ->createOptionUsing(function (array $data, $get) {
                                return \App\Models\CarModel::firstOrCreate([
                                    'make_id' => $get('make_id'),
                                    'name'    => $data['name'],
                                ])->id;
                            })
                            ->createOptionAction(fn ($action) => $action->modalWidth('sm')),

                        Select::make('car_trim_id')
                            ->label('Trim')
                            ->searchable()
                            ->live()
                            ->placeholder(fn ($get) => $get('car_model_id') ? 'Select or add a trim' : 'Select a model first')
                            ->disabled(fn ($get) => ! $get('car_model_id'))
                            ->options(fn ($get) => $get('car_model_id')
                                ? \App\Models\CarTrim::where('car_model_id', $get('car_model_id'))
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                : []
                            )
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->placeholder('e.g. Sport, LE, XLE, SE')
                                    ->maxLength(100),
                            ])
                            ->createOptionUsing(function (array $data, $get) {
                                return \App\Models\CarTrim::firstOrCreate([
                                    'car_model_id' => $get('car_model_id'),
                                    'name'         => $data['name'],
                                ])->id;
                            })
                            ->createOptionAction(fn ($action) => $action->modalWidth('sm')),
                        Select::make('year')
                            ->required()
                            ->placeholder('Select year')
                            ->options(
                                array_combine(
                                    $years = range((int) date('Y'), 2000),
                                    $years
                                )
                            ),
                        TextInput::make('engine_capacity')
                            ->required()
                            ->placeholder('e.g. 1800cc')
                            ->maxLength(50),
                        Select::make('transmission')
                            ->required()
                            ->placeholder('Select transmission')
                            ->options(array_combine(Car::TRANSMISSIONS, Car::TRANSMISSIONS)),
                        Select::make('fuel_type')
                            ->required()
                            ->placeholder('Select fuel type')
                            ->options(array_combine(Car::FUEL_TYPES, Car::FUEL_TYPES)),
                        TextInput::make('mileage')
                            ->required()
                            ->numeric()
                            ->placeholder('e.g. 45000')
                            ->suffix('km'),
                        TextInput::make('colour')
                            ->required()
                            ->placeholder('e.g. Pearl White')
                            ->maxLength(50),
                        Select::make('country_of_origin')
                            ->required()
                            ->placeholder('Select country')
                            ->options(array_combine(Car::COUNTRIES_OF_ORIGIN, Car::COUNTRIES_OF_ORIGIN)),
                        Select::make('body_type')
                            ->label('Body Type')
                            ->required()
                            ->placeholder('Select body type')
                            ->options(CarBodyType::class),
                        Select::make('status')
                            ->required()
                            ->options(CarStatus::class)
                            ->default(CarStatus::Available),
                    ]),

                Section::make('Images')
                    ->description('Upload at least 3 photos. Drag to reorder — the first image is used as the cover.')
                    ->schema([
                        // I bind this to a transient 'image_paths' field rather than the images relationship
                        // directly, since FileUpload's multiple() stores one array of paths per field —
                        // CreateCar/EditCar then sync that array into individual CarImage rows via CarService.
                        FileUpload::make('image_paths')
                            ->label('Photos')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->panelLayout('grid')
                            ->imagePreviewHeight('160')
                            // I don't accept AVIF here — these go through ImageOptimizer's GD
                            // driver, and GD on this server has no AVIF decode support, so an
                            // AVIF upload would crash the save instead of being optimized.
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120) // 5 MB per image
                            ->disk('public')
                            ->directory('cars')
                            ->minFiles(3)
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Features')
                    ->schema([
                        TagsInput::make('special_features')
                            ->label('Special Features')
                            ->placeholder('Type a feature and press Enter')
                            ->columnSpanFull(),
                    ]),

                Section::make('Pricing')
                    ->columns(2)
                    ->schema([
                        // I take USD input and convert to cents on save — Filament works in display dollars,
                        // the database always stores integer cents.
                        TextInput::make('price_usd_cents')
                            ->label('Price (USD)')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->step(0.01)
                            ->live(onBlur: true)
                            ->afterStateHydrated(fn ($state, $set) => $set('price_usd_cents', $state / 100))
                            ->dehydrateStateUsing(fn ($state) => (int) round($state * 100)),
                        TextInput::make('shipping_cost_usd_cents')
                            ->label('Shipping Cost (USD)')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00')
                            ->step(0.01)
                            ->afterStateHydrated(fn ($state, $set) => $set('shipping_cost_usd_cents', $state / 100))
                            ->dehydrateStateUsing(fn ($state) => (int) round($state * 100)),
                        // I show a read-only GHS preview so the admin can see what the customer
                        // will actually pay, without storing it — the canonical price is USD cents.
                        Placeholder::make('price_ghs_preview')
                            ->label('Price (GHS equivalent)')
                            ->content(fn ($get) => 'GH₵' . number_format(
                                ((float) ($get('price_usd_cents') ?? 0)) * Car::currentExchangeRate(),
                                2
                            ))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
