<div>
    @if ($submitted)
        <x-ui.alert type="success" title="Message sent!">
            Thank you! We've received your message and will be in touch within 24 hours.
        </x-ui.alert>
    @else
        <form wire:submit="submit" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-ui.input
                    label="Full Name"
                    :required="true"
                    wire:model="name"
                    placeholder="John Mensah"
                    :error="$errors->first('name')"
                />
                <x-ui.input
                    label="Email"
                    type="email"
                    :required="true"
                    wire:model="email"
                    placeholder="you@example.com"
                    :error="$errors->first('email')"
                />
            </div>

            <x-ui.input
                label="Phone (optional)"
                type="tel"
                wire:model="phone"
                placeholder="+233 000 000 000"
                :error="$errors->first('phone')"
            />

            <x-ui.select
                label="Subject"
                :required="true"
                wire:model="subject"
                :error="$errors->first('subject')"
            >
                @foreach (\App\Livewire\Contact\ContactForm::SUBJECTS as $subjectOption)
                    <option value="{{ $subjectOption }}">{{ $subjectOption }}</option>
                @endforeach
            </x-ui.select>

            <x-ui.textarea
                label="Message"
                :required="true"
                :rows="5"
                wire:model="message"
                placeholder="Tell us what you're looking for, or ask us anything..."
                :error="$errors->first('message')"
            />

            <x-ui.button type="submit" variant="{{ $carUuid ? 'black' : 'primary' }}" size="lg" class="w-full justify-center" :loading="$this->isProcessing ?? false">
                {{ $carUuid ? 'Send Inquiry' : 'Send Message' }}
            </x-ui.button>
        </form>
    @endif
</div>
