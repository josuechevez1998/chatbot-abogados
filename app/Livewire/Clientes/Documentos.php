<?php

namespace App\Livewire\Clientes;


use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Cliente;
use App\Models\ClienteDocumento;
use Illuminate\Support\Facades\Storage;


class Documentos extends Component
{
    use WithFileUploads;


    public Cliente $cliente;
    public $uploading = false;


    #[Validate('file|max:51200|mimes:png,jpg,jpeg,pdf')]
    public $file;


    public function save()
    {
        $this->validate();


        $path = $this->file->store("clientes/{$this->cliente->id}", 'public');


        ClienteDocumento::create([
            'cliente_id' => $this->cliente->id,
            'name' => $this->file->getClientOriginalName(),
            'type' => $this->file->getClientOriginalExtension(),
            'size' => $this->file->getSize(),
            'path' => $path,
        ]);


        $this->reset('file');
        $this->dispatch('uploaded');
    }


    public function deleteDocument(ClienteDocumento $document)
    {
        Storage::disk('public')->delete($document->path);
        $document->delete();
        $this->dispatch('deleted');
    }


    public function render()
    {
        return view('livewire.clientes.documentos', [
            'documents' => $this->cliente->documentos()->latest()->get(),
        ]);
    }
}
