<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DemoOrderResource\Pages;
use App\Models\DemoOrder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Component as LivewireComponent;

class DemoOrderResource extends Resource
{
    protected static ?string $model = DemoOrder::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    self::getBasicInfoStep(),
                    self::getDetailsStep(),
                    self::getNotesStep(),
                ])
                    ->persistStepInQueryString()
                    ->submitAction(new HtmlString(Blade::render(<<<'BLADE'
                        <x-filament::button type="submit" size="md">
                            Submit
                        </x-filament::button>
                    BLADE)))
                    ->columnSpanFull(),
            ]);
    }

    protected static function getBasicInfoStep(): Step
    {
        return Step::make('basics')
            ->label('Basic Info')
            ->schema([
                // Section with placeholders referencing the Livewire component
                // (mirrors the original's getUnitInfoSection pattern)
                Section::make('Order Summary')
                    ->schema([
                        Placeholder::make('order_preview')
                            ->label('Preview')
                            ->content(function (LivewireComponent $livewire) {
                                return 'Current form state';
                            }),

                        Placeholder::make('order_status_info')
                            ->label('Status')
                            ->content(fn (LivewireComponent $livewire) => 'Draft'),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible()
                    ->compact(),

                // Repeater with table layout and relationship
                // (mirrors the original's getSubjectsRepeater pattern)
                Section::make('Order Items')
                    ->schema([
                        self::getItemsRepeater(),
                    ])
                    ->compact()
                    ->columnSpanFull(),

                // Live date fields (mirrors started_at/ended_at pattern)
                DatePicker::make('started_at')
                    ->label('Start Date')
                    ->required()
                    ->live(),

                DatePicker::make('ended_at')
                    ->label('End Date')
                    ->afterOrEqual('started_at')
                    ->live(),

                // Conditionally visible select based on live field
                // (mirrors billing_generate_from pattern)
                Select::make('priority')
                    ->label('Priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ])
                    ->visible(fn (Get $get) => filled($get('started_at')))
                    ->required(fn (Get $get) => filled($get('started_at')))
                    ->columnSpanFull(),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->maxLength(255),

                // Section with live calculated fields
                // (mirrors the rent_section pattern)
                Section::make('Pricing')
                    ->schema([
                        TextInput::make('base_price')
                            ->label('Base Price')
                            ->numeric()
                            ->suffix('USD')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $base = floatval($get('base_price') ?? 0);
                                $tax = floatval($get('tax_rate') ?? 0);
                                $set('total_price', round($base * (1 + $tax / 100), 2));
                            }),

                        TextInput::make('tax_rate')
                            ->label('Tax Rate')
                            ->numeric()
                            ->suffix('%')
                            ->default(0)
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $base = floatval($get('base_price') ?? 0);
                                $tax = floatval($get('tax_rate') ?? 0);
                                $set('total_price', round($base * (1 + $tax / 100), 2));
                            }),

                        TextInput::make('total_price')
                            ->label('Total with Tax')
                            ->numeric()
                            ->suffix('USD')
                            ->disabled()
                            ->readOnly()
                            ->dehydrated(false),
                    ])
                    ->columns(3)
                    ->compact()
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    protected static function getDetailsStep(): Step
    {
        return Step::make('details')
            ->label('Details')
            ->schema([
                Textarea::make('description')
                    ->label('Description')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    protected static function getNotesStep(): Step
    {
        return Step::make('notes')
            ->label('Notes & Status')
            ->schema([
                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3)
                    ->columnSpanFull(),

                TextInput::make('status')
                    ->default('draft')
                    ->required(),
            ]);
    }

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('items')
            ->relationship()
            ->table([
                TableColumn::make('Product'),
                TableColumn::make('Quantity'),
                TableColumn::make('Unit Price'),
                TableColumn::make('Note'),
            ])
            ->schema([
                Select::make('product_name')
                    ->label('Product')
                    ->options([
                        'Widget A' => 'Widget A',
                        'Widget B' => 'Widget B',
                        'Widget C' => 'Widget C',
                        'Service X' => 'Service X',
                        'Service Y' => 'Service Y',
                    ])
                    ->searchable()
                    ->required()
                    ->live(),

                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->default(1)
                    ->required()
                    ->live(onBlur: true),

                TextInput::make('unit_price')
                    ->label('Unit Price')
                    ->numeric()
                    ->suffix('USD')
                    ->required()
                    ->default(0)
                    ->live(onBlur: true),

                Textarea::make('note')
                    ->label('Note')
                    ->rows(1),
            ])
            ->addActionLabel('Add Item')
            ->defaultItems(1)
            ->reorderable(false)
            ->hiddenLabel()
            ->columnSpanFull();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email'),
                TextColumn::make('status'),
                TextColumn::make('created_at')->dateTime(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDemoOrders::route('/'),
            'create' => Pages\CreateDemoOrder::route('/create'),
            'edit' => Pages\EditDemoOrder::route('/{record}/edit'),
        ];
    }
}
