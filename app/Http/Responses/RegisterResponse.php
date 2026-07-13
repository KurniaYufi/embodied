<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request): mixed
    {
        if ($request->wantsJson()) {
            return response()->json(null, 201);
        }

        return redirect()->route('home');
    }
}
