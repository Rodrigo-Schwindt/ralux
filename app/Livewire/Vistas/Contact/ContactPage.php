<?php

namespace App\Livewire\Vistas\Contact;

use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class ContactPage extends Component
{
    public $contact;

    public $name;
    public $company;
    public $email;
    public $phone;
    public $message;

    protected $rules = [
        'name' => 'required|string|max:255',
        'company' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:50',
        'message' => 'required|string|min:5',
    ];

    public function mount()
    {
        $this->contact = Contact::first();
    }

    public function submit()
    {
        $this->validate();

        try {
            Mail::raw(
                "Nuevo mensaje desde el formulario de contacto:\n\n"
                . "Nombre: {$this->name}\n"
                . "Empresa: {$this->company}\n"
                . "Email: {$this->email}\n"
                . "Telefono: {$this->phone}\n\n"
                . "Mensaje:\n{$this->message}",
                function ($m) {
                    $m->to($this->contact->mail_adm ?? 'info@tudominio.com');
                    $m->subject('Nuevo mensaje desde el sitio web');
                }
            );

            $this->reset(['name', 'company', 'email', 'phone', 'message']);
            $this->dispatch(
                'toast',
                title: 'Mensaje enviado',
                message: 'Tu consulta se envio correctamente.',
                type: 'success'
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'toast',
                title: 'Error al enviar',
                message: 'No pudimos enviar tu consulta. Intenta nuevamente.',
                type: 'error'
            );
        }
    }

    public function render()
    {
        return view('livewire.vistas.contact.contact-page');
    }
}
