<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PqrsController extends Controller
{
    public function index()
    {
        $pqrs = session('pqrs_list', []);
        return view('dashboard.pqrs', compact('pqrs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'    => 'required|in:Pregunta,Queja,Reclamo,Sugerencia',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        $pqrs   = session('pqrs_list', []);
        $pqrs[] = [
            'id'        => uniqid(),
            'type'      => $request->type,
            'subject'   => $request->subject,
            'message'   => $request->message,
            'status'    => 'open',
            'createdAt' => now()->toISOString(),
            'response'  => null,
        ];

        session(['pqrs_list' => $pqrs]);

        return back()->with('success', 'Solicitud enviada. Te responderemos pronto.');
    }
}
