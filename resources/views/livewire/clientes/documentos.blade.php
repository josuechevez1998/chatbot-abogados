<div class="p-4">
    <h2 class="text-xl font-bold mb-4">Documentos del Cliente</h2>


    <!-- Drag & Drop -->
    <div x-data="{ dragging: false }" x-on:dragover.prevent="dragging = true" x-on:dragleave.prevent="dragging = false"
        x-on:drop.prevent="dragging = false; $wire.file = $event.dataTransfer.files[0]; $wire.save()"
        class="border-2 border-dashed rounded-lg p-6 flex flex-col items-center justify-center"
        :class="dragging ? 'bg-blue-100 border-blue-500' : 'border-gray-300'">
        <p class="text-gray-600">Arrastra un archivo aquí</p>
        <span class="text-sm text-gray-500">PDF, PNG, JPG (hasta 50MB)</span>


        <input type="file" wire:model="file" class="mt-2" />
        <button wire:click="save" class="btn btn-primary mt-3">Subir archivo</button>
    </div>


    <hr class="my-6" />


    <!-- Grid de documentos -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ($documents as $doc)
            <div class="border p-3 rounded-lg shadow hover:shadow-lg transition relative group">
                <!-- Preview -->
                @if (in_array($doc->type, ['png', 'jpg', 'jpeg']))
                    <img src="{{ asset('storage/' . $doc->path) }}" class="w-full h-32 object-cover rounded-md" />
                @else
                    <div class="w-full h-32 flex items-center justify-center bg-gray-100 rounded-md text-6xl">📄</div>
                @endif


                <div class="mt-2">
                    <p class="font-semibold text-sm truncate">{{ $doc->name }}</p>
                    <p class="text-xs text-gray-500">{{ strtoupper($doc->type) }} •
                        {{ number_format($doc->size / 1024, 2) }} KB</p>
                </div>


                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition flex gap-2">
                    <a href="{{ asset('storage/' . $doc->path) }}" target="_blank"
                        class="bg-green-600 text-white px-2 py-1 text-xs rounded">Ver</a>
                    <button wire:click="deleteDocument({{ $doc->id }})"
                        class="bg-red-600 text-white px-2 py-1 text-xs rounded">X</button>
                </div>
            </div>
        @endforeach
    </div>
</div>
