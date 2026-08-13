<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class ContactManager extends Controller
{
    public function index()
    {
        $contact = Contact::first();

        return view('livewire.contact.contact-manager', [
            'contact' => $contact
        ]);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'direction_adm' => 'nullable|string|max:255',
            'direction_sale' => 'nullable|string|max:255',
            'phone_amd'     => 'nullable|string|max:50',
            'phone_sale'    => 'nullable|string|max:50',
            'maps_adm'      => 'nullable|string|max:50',
            'maps_sale'     => 'nullable|string|max:50',
            'frame_adm'     => 'nullable|string',
            'mail_adm'      => 'nullable|string|max:255',
            'mail_comercial' => 'nullable|string|max:255',
            'mail_tecnico'  => 'nullable|string|max:255',
            'mail_compras'  => 'nullable|string|max:255',
            'mail_logistica' => 'nullable|string|max:255',
            'wssp'          => 'nullable|string|max:255',
            'facebook'      => 'nullable|string|max:255',
            'insta'         => 'nullable|string|max:255',
            'linkedin'      => 'nullable|string|max:255',
            'youtube'       => 'nullable|string|max:255',
            'icono_1_temp'  => 'nullable|mimes:jpg,jpeg,png,webp,gif,svg|max:4096',
            'icono_2_temp'  => 'nullable|mimes:jpg,jpeg,png,webp,gif,svg|max:4096',
            'icono_3_temp'  => 'nullable|mimes:jpg,jpeg,png,webp,gif,svg|max:4096',
        ]);

        $contact = Contact::first() ?? new Contact();

        $contact->fill($validated);

        foreach ([1, 2, 3] as $i) {
            $temp = "icono_{$i}_temp";

            if ($request->hasFile($temp)) {
                if ($contact->{"icono_$i"}) {
                    Storage::disk('public')->delete($contact->{"icono_$i"});
                }
                $contact->{"icono_$i"} = $request->file($temp)->store('contact', 'public');
            }

            if ($request->has("remove_icono_$i")) {
                if ($contact->{"icono_$i"}) {
                    Storage::disk('public')->delete($contact->{"icono_$i"});
                }
                $contact->{"icono_$i"} = null;
            }
        }

        $contact->save();

        if ($contact->mail_adm) {
            try {
                $mailData = [
                    'nombre' => 'Admin (Actualización de datos)',
                    'empresa' => 'Sistema Interno',
                    'email' => $contact->mail_adm,
                    'celular' => $contact->wssp,
                    'mensaje' => 'Se han actualizado los datos de contacto en el panel administrativo.'
                ];

                Mail::to($contact->mail_adm)->send(new ContactMail($mailData));
            } catch (\Throwable $e) {
                \Log::error('ContactManager mail error: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('toast', [
            'message' => 'Datos guardados correctamente',
            'type' => 'success'
        ]);
    }
}
