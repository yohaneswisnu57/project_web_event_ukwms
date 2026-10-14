<?php

namespace App\Filament\Pages;

use App\Http\Middleware\ApplyLandingContent;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property-read Schema $form
 */
class ManageLandingContent extends Page
{
    public const PHOTO_DISK = 'public';

    public const PHOTO_DIRECTORY = 'landing';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?string $navigationLabel = 'Konten Landing Page';

    protected static ?string $title = 'Konten Landing Page';

    protected static ?string $slug = 'landing-content';

    protected static ?int $navigationSort = 3;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->currentContent());
    }

    /**
     * Isian admin yang tersimpan, dilengkapi nilai bawaan dari config/retina.php.
     *
     * @return array<string, mixed>
     */
    protected function currentContent(): array
    {
        $stored = Setting::getJson(Setting::LANDING_CONTENT);

        $content = [];

        foreach (ApplyLandingContent::FIELDS as $field => $configKey) {
            $content[$field] = array_key_exists($field, $stored) ? $stored[$field] : config($configKey);
        }

        $content['ends_at_time'] = array_key_exists('ends_at_time', $stored)
            ? $stored['ends_at_time']
            : (filled(config('retina.ends_at')) ? Carbon::parse(config('retina.ends_at'))->format('H:i') : null);

        return $content;
    }

    public function form(Schema $schema): Schema
    {
        $startsAt = Carbon::parse(config('retina.starts_at'));

        return $schema
            ->components([
                Section::make('Pembicara')
                    ->description('Nama pembicara: '.config('retina.speaker.name').'. Kolom yang dikosongkan tidak ditampilkan di landing page.')
                    ->schema([
                        Grid::make(['default' => 1, 'lg' => 3])
                            ->schema([
                                FileUpload::make('speaker_photo')
                                    ->label('Foto')
                                    ->helperText('Rasio 4:5 (potret). Tanpa foto, landing page menampilkan inisial.')
                                    ->disk(self::PHOTO_DISK)
                                    ->directory(self::PHOTO_DIRECTORY)
                                    ->visibility('public')
                                    ->image()
                                    ->imageEditor()
                                    ->imageAspectRatio('4:5')
                                    ->automaticallyOpenImageEditorForAspectRatio()
                                    ->maxSize(2048),
                                Grid::make(1)
                                    ->schema([
                                        TextInput::make('speaker_role')
                                            ->label('Jabatan / keahlian')
                                            ->placeholder('Contoh: Dokter & pelatih public speaking')
                                            ->maxLength(120),
                                        Textarea::make('speaker_bio')
                                            ->label('Bio singkat')
                                            ->helperText('2–3 kalimat.')
                                            ->rows(5)
                                            ->maxLength(600),
                                    ])
                                    ->columnSpan(['lg' => 2]),
                            ]),
                    ]),

                Section::make('Materi')
                    ->description('Tampil sebagai "babak" di landing page, sesuai urutan di bawah. Seret untuk mengubah urutan.')
                    ->schema([
                        Repeater::make('sessions')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul')
                                    ->required()
                                    ->maxLength(60),
                                Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->required()
                                    ->rows(2)
                                    ->maxLength(200),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->addActionLabel('Tambah babak')
                            ->minItems(1)
                            ->maxItems(6)
                            ->defaultItems(0)
                            ->reorderableWithButtons()
                            ->collapsible(),
                    ]),

                Section::make('Acara')
                    ->description('Tanggal, tempat, dan biaya mengikuti data tiket & email, jadi tidak diubah di sini.')
                    ->schema([
                        TimePicker::make('ends_at_time')
                            ->label('Jam selesai')
                            ->helperText('Mulai pukul '.$startsAt->format('H.i').' WIB. Kosongkan jika belum pasti.')
                            ->seconds(false)
                            ->after($startsAt->format('H:i')),
                        TextInput::make('perks')
                            ->label('Fasilitas peserta')
                            ->placeholder('Contoh: Termasuk sertifikat & goodie bag')
                            ->maxLength(80),
                    ])
                    ->columns(2),

                Section::make('Narahubung')
                    ->description('Tampil di FAQ dan footer sebagai tautan WhatsApp.')
                    ->schema([
                        TextInput::make('contact_name')
                            ->label('Nama')
                            ->maxLength(60),
                        TextInput::make('contact_whatsapp')
                            ->label('Nomor WhatsApp')
                            ->tel()
                            ->placeholder('081234567890')
                            ->regex('/^(\+62|62|0)8[0-9]{7,12}$/')
                            ->requiredWith('contact_name')
                            ->maxLength(20),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Simpan')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ])->sticky(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewLandingPage')
                ->label('Lihat landing page')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('gray')
                ->url(route('home'))
                ->openUrlInNewTab(),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $previousPhoto = Setting::getJson(Setting::LANDING_CONTENT)['speaker_photo'] ?? null;

        Setting::putJson(Setting::LANDING_CONTENT, [
            'speaker_role' => $this->nullIfBlank($data['speaker_role']),
            'speaker_bio' => $this->nullIfBlank($data['speaker_bio']),
            'speaker_photo' => $data['speaker_photo'] ?: null,
            'sessions' => collect($data['sessions'])
                ->map(fn (array $session): array => [
                    'title' => trim($session['title']),
                    'description' => trim($session['description']),
                ])
                ->values()
                ->all(),
            'ends_at_time' => filled($data['ends_at_time']) ? Carbon::parse($data['ends_at_time'])->format('H:i') : null,
            'perks' => $this->nullIfBlank($data['perks']),
            'contact_name' => $this->nullIfBlank($data['contact_name']),
            'contact_whatsapp' => $this->nullIfBlank($data['contact_whatsapp']),
        ]);

        if (filled($previousPhoto) && $previousPhoto !== $data['speaker_photo']) {
            Storage::disk(self::PHOTO_DISK)->delete($previousPhoto);
        }

        Notification::make()
            ->title('Konten landing page disimpan')
            ->body('Perubahan langsung tampil di landing page.')
            ->success()
            ->send();
    }

    protected function nullIfBlank(?string $value): ?string
    {
        return filled($value) ? trim($value) : null;
    }
}
