<div
    wire:ignore
    x-data="{
        init() {
            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginFileValidateType,
                FilePondPluginFileValidateSize
            );

            const pond = FilePond.create(this.$refs.input, {
                allowMultiple: {{ isset($multiple) && $multiple ? 'true' : 'false' }},
                server: {
                    process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
                        @this.upload('{{ $attributes->whereStartsWith('wire:model')->first() }}', file, load, error, progress)
                    },
                    revert: (filename, load) => {
                        @this.removeUpload('{{ $attributes->whereStartsWith('wire:model')->first() }}', filename, load)
                    },
                },
                acceptedFileTypes: {!! isset($accepts) ? "['" . str_replace(',', "','", str_replace(' ', '', $accepts)) . "']" : 'null' !!},
                maxFileSize: '{{ $maxSize ?? '5MB' }}',
                labelIdle: `{{ $placeholder ?? 'Drag & Drop your files or <span class=\"filepond--label-action\">Browse</span>' }}`,
                credits: false,
            });
        }
    }"
>
    <input type="file" x-ref="input" />
</div>
