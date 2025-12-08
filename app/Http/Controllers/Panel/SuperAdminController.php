<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function index()
    {
        // Obtener todos los usuarios con métricas agregadas
        // Sumamos tokens de todas las organizaciones donde el usuario es owner/admin
        $users = User::with('organizations')->get()->map(function ($u) {
            $orgIds = $u->organizations->pluck('id');

            // Tokens
            $stats = DB::table('analytics_events')
                ->whereIn('organization_id', $orgIds)
                ->selectRaw('SUM(tokens_in) as t_in, SUM(tokens_out) as t_out, COUNT(*) as interactions')
                ->first();

            $u->total_tokens = ($stats->t_in ?? 0) + ($stats->t_out ?? 0);
            $u->interactions = $stats->interactions ?? 0;
            $u->bots_count = DB::table('bots')->whereIn('organization_id', $orgIds)->count();

            return $u;
        });

        return view('panel.admin.index', compact('users'));
    }

    public function toggleUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Evitar auto-bloqueo
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta de desarrollador.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activado' : 'desactivado';
        return back()->with('success', "Usuario {$user->email} ha sido {$status}.");
    }
}
