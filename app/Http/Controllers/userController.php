<?php

namespace App\Http\Controllers;

use App\Enums\StateEnum;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class userController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $roleFilter = function ($query) use ($request) {
        if ($request->has('role')) {
            $role = $request->input('role');
            $query->where('roles.name', $role);
        }
    };

    $actives = $request->has('active');

    if ($actives && $request->input('active') == 'oui') {
        $field = 'users.etat'; // Specify the table name for 'etat'
        $value = 'true';
        $sign = '=';
    } else if ($actives && $request->input('active') == 'non') {
        $field = 'users.etat'; // Specify the table name for 'etat'
        $value = 'false';
        $sign = '=';
    } else {
        $field = 'users.id'; // Specify the table name for 'id'
        $value = 0;
        $sign = '>=';
    }

    $users = QueryBuilder::for(User::class)
        ->allowedIncludes(['role'])
        ->join('roles', 'roles.id', '=', 'users.role_id') // Corrected the table name to 'users'
        ->where($roleFilter)
        ->where($field, $sign, $value) // Specify the table name for the dynamic field
        ->get();

    return new UserCollection($users);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $request->validate();

        $user = User::create($request->all());
        return $this->sendResponse($user, StateEnum::SUCCESS, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendResponse([], StateEnum::ECHEC, 404);
        }

        return $this->sendResponse($user, StateEnum::SUCCESS, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $request->validate();

        $user = User::find($id);

        if (!$user) {
            return $this->sendResponse([], StateEnum::ECHEC, 404);
        }

        $user->update($request->all());
        return $this->sendResponse($user, StateEnum::SUCCESS, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendResponse([], StateEnum::ECHEC, 404);
        }

        $user->delete();
        return $this->sendResponse([], StateEnum::SUCCESS, 204);
    }
}
