<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * POST /api/admin/login
     */
    public function login(AdminLoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error(
                'INVALID_CREDENTIALS',
                'Credenciales inválidas',
                null,
                401
            );
        }

        // Permitir login solo a ADMIN y BARBER
        if (!in_array($user->role, ['ADMIN', 'BARBER'])) {
            return $this->error(
                'FORBIDDEN',
                'No tienes acceso al panel',
                null,
                403
            );
        }

        // Si es BARBER, verificar que tenga perfil de barbero
        $barber = null;
        if ($user->role === 'BARBER') {
            $barber = \App\Models\Barber::where('user_id', $user->id)->first();
            if (!$barber) {
                return $this->error(
                    'FORBIDDEN',
                    'No se encontró perfil de barbero',
                    null,
                    403
                );
            }
        }

        // Create token
        $token = $user->createToken('admin-token')->plainTextToken;

        return $this->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'barber_id' => $barber ? $barber->id : null,
            ],
            'token' => $token,
        ]);
    }

    /**
     * POST /api/admin/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(['message' => 'Sesión cerrada exitosamente']);
    }
}
