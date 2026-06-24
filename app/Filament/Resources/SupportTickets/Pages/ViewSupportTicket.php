<?php

/**
 * @author Ohene Adjei
 */

namespace App\Filament\Resources\SupportTickets\Pages;

use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Models\SupportTicket;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

/**
 * The admin's single screen for one support ticket — customer info, the
 * full message thread, a reply box, and a status toggle.
 */
class ViewSupportTicket extends ViewRecord
{
    protected static string $resource = SupportTicketResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->getRecord())
            ->components([
                Grid::make(2)
                    ->schema([
                        Section::make('Customer')
                            ->schema([
                                TextEntry::make('user.name')->label('Name'),
                                TextEntry::make('user.email')->label('Email'),
                            ]),
                        Section::make('Ticket')
                            ->schema([
                                TextEntry::make('subject'),
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Open' => 'info',
                                        'In Progress' => 'warning',
                                        'Closed' => 'success',
                                        default => 'gray',
                                    }),
                            ]),
                    ]),

                Section::make('Conversation')
                    ->schema([
                        ViewEntry::make('messages')
                            ->view('filament.infolists.ticket-messages')
                            ->viewData(['ticket' => $this->getRecord()]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        $ticket = $this->getRecord();

        return [
            Action::make('reply')
                ->label('Reply')
                ->icon('heroicon-m-arrow-uturn-left')
                ->color('primary')
                ->schema([
                    Textarea::make('message')
                        ->label('Message')
                        ->placeholder('Write your reply to the customer...')
                        ->required(),
                    FileUpload::make('attachment_path')
                        ->label('Attachment (optional)')
                        ->disk('public')
                        ->directory('tickets/attachments'),
                ])
                ->action(function (array $data) use ($ticket) {
                    $ticket->messages()->create([
                        'user_id' => Auth::id(),
                        'message' => $data['message'],
                        'is_admin' => true,
                        'attachment_path' => $data['attachment_path'] ?? null,
                    ]);

                    // A reply implicitly picks the ticket up — Open becomes
                    // In Progress, same as a human support agent claiming it.
                    if ($ticket->status === 'Open') {
                        $ticket->update(['status' => 'In Progress']);
                    } else {
                        $ticket->touch();
                    }

                    $this->redirect(static::getResource()::getUrl('view', ['record' => $ticket]));
                }),

            Action::make('closeTicket')
                ->label('Close Ticket')
                ->icon('heroicon-m-check-circle')
                ->color('danger')
                ->visible(fn () => $ticket->status !== 'Closed')
                ->requiresConfirmation()
                ->action(function () use ($ticket) {
                    $ticket->update(['status' => 'Closed']);
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $ticket]));
                }),

            Action::make('reopenTicket')
                ->label('Reopen Ticket')
                ->icon('heroicon-m-arrow-path')
                ->visible(fn () => $ticket->status === 'Closed')
                ->action(function () use ($ticket) {
                    $ticket->update(['status' => 'Open']);
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $ticket]));
                }),
        ];
    }
}
