<?php

namespace App\Http\Controllers\Clientes;

use App\Http\Controllers\Controller;
use App\Mail\CuentaAprobadaMail;
use App\Models\Cliente;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('usuario', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('cuit', 'like', "%{$search}%")
                    ->orWhere('cuil', 'like', "%{$search}%");
            });
        }

        $sortField = $request->get('sortField', 'created_at');
        $sortDirection = $request->get('sortDirection', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $clientes = $query->paginate(10);

        if ($request->ajax()) {
            $html = view('livewire.clientes.partials.table', compact('clientes'))->render();
            $pagination = view('livewire.clientes.partials.pagination', compact('clientes'))->render();

            return response()->json([
                'html' => $html,
                'pagination' => $pagination,
            ]);
        }

        return view('livewire.clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('livewire.clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'usuario' => 'required|string|max:255|unique:clientes,usuario',
            'password' => 'required|string|min:8|confirmed',
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clientes,email',
            'cuil' => 'nullable|string|max:255',
            'cuit' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'domicilio' => 'nullable|string|max:255',
            'localidad' => 'nullable|string|max:255',
            'provincia' => 'nullable|string|max:255',
            'descuento' => 'nullable|numeric|min:0|max:100',
            'descuento2' => 'nullable|numeric|min:0|max:100',
            'descuento3' => 'nullable|numeric|min:0|max:100',
            'activo' => 'required|boolean',
        ]);

        Cliente::create([
            'usuario' => $request->usuario,
            'password' => Hash::make($request->password),
            'nombre' => $request->nombre,
            'email' => $request->email,
            'cuil' => $request->cuil,
            'cuit' => $request->cuit,
            'telefono' => $request->telefono,
            'domicilio' => $request->domicilio,
            'localidad' => $request->localidad,
            'provincia' => $request->provincia,
            'descuento' => $request->descuento ?? 0,
            'descuento2' => $request->descuento2 ?? 0,
            'descuento3' => $request->descuento3 ?? 0,
            'activo' => $request->activo,
        ]);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado exitosamente');
    }

    public function edit(Cliente $cliente)
    {
        return view('livewire.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $rules = [
            'usuario' => 'required|string|max:255|unique:clientes,usuario,'.$cliente->id,
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clientes,email,'.$cliente->id,
            'cuil' => 'nullable|string|max:255',
            'cuit' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'domicilio' => 'nullable|string|max:255',
            'localidad' => 'nullable|string|max:255',
            'provincia' => 'nullable|string|max:255',
            'descuento' => 'nullable|numeric|min:0|max:100',
            'descuento2' => 'nullable|numeric|min:0|max:100',
            'descuento3' => 'nullable|numeric|min:0|max:100',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        $wasInactive = ! $cliente->activo;
        $willBeActive = $request->has('activo') && $request->activo == 1;

        $data = [
            'usuario' => $request->usuario,
            'nombre' => $request->nombre,
            'email' => $request->email,
            'cuil' => $request->cuil,
            'cuit' => $request->cuit,
            'telefono' => $request->telefono,
            'domicilio' => $request->domicilio,
            'localidad' => $request->localidad,
            'provincia' => $request->provincia,
            'descuento' => $request->descuento ?? 0,
            'descuento2' => $request->descuento2 ?? 0,
            'descuento3' => $request->descuento3 ?? 0,
            'activo' => $willBeActive,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $cliente->update($data);

        if ($wasInactive && $willBeActive) {
            $this->notifyCuentaAprobada($cliente);
        }

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado exitosamente');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cliente eliminado exitosamente',
        ]);
    }

    public function toggleActivo(Cliente $cliente)
    {
        $wasInactive = ! $cliente->activo;
        $notified = false;

        $cliente->activo = ! $cliente->activo;
        $cliente->save();

        if ($wasInactive && $cliente->activo) {
            $notified = $this->notifyCuentaAprobada($cliente);
        }

        return response()->json([
            'success' => true,
            'activo' => $cliente->activo,
            'message' => $cliente->activo
                ? ($notified ? 'Cliente activado y notificado por email' : 'Cliente activado')
                : 'Cliente desactivado',
        ]);
    }

    private function notifyCuentaAprobada(Cliente $cliente): bool
    {
        if ($cliente->cuenta_aprobada_notificada_at || empty($cliente->email)) {
            return false;
        }

        $contactData = Contact::first();
        $mailData = [
            'nombre' => $cliente->nombre,
            'usuario' => $cliente->usuario,
            'email' => $cliente->email,
            'descuento' => $cliente->descuento ?? 0,
            'url_login' => route('home'),
            'contacto_email' => $contactData->mail_adm ?? 'info@ralux.com',
            'contacto_telefono' => $contactData->phone_amd ?? null,
            'contacto_whatsapp' => $contactData->wssp ?? null,
        ];

        try {
            Mail::to($cliente->email)->send(new CuentaAprobadaMail($mailData));

            $cliente->forceFill([
                'cuenta_aprobada_notificada_at' => now(),
            ])->save();

            return true;
        } catch (\Throwable $e) {
            Log::error('Error al enviar email de aprobacion: '.$e->getMessage(), [
                'cliente_id' => $cliente->id,
                'email' => $cliente->email,
            ]);

            return false;
        }
    }
}
