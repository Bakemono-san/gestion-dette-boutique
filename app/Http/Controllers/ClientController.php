<?php

namespace App\Http\Controllers;

use App\Enums\StateEnum;
use App\Http\Requests\StoreClientRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\Dette;
use App\Models\User;
use App\Traits\RestResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\QueryBuilder;

class ClientController extends Controller
{
    use RestResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, User $user)
    {
        // $this->authorize('viewAny', $user);
        $StateFilter = function ($query) use ($request) {
            if ($request->has('active')) {
                $etat = $request->input('active');
                $etat = $etat == 'oui' ? 'true' : 'false';
                $query->where('users.etat', $etat);
            }
        };
        $compte = $request->has('compte');

        if ($compte && $request->input('compte') == 'oui') {
            $field = 'clients.user_id';
            $value = 0;
            $sign = '>';
        } else if ($compte && $request->input('compte') == 'non') {
            $field = 'clients.user_id';
            $value = null;
            $sign = '=';
        } else {
            $field = 'clients.id';
            $value = 0;
            $sign = '>=';
        }
        $clients = QueryBuilder::for(Client::class)
            ->allowedFilters(['surname', 'telephone'])
            ->allowedIncludes(['user'])
            ->join('users', 'users.id', '=', 'clients.user_id')
            ->where($StateFilter)
            ->where($field, $sign, $value)
            ->get();

        $message = $clients->count() . ' client(s) trouvé(s)';
        $data = $clients->count() > 0 ? new ClientCollection($clients) : [];
        return $this->sendResponse($data, StateEnum::SUCCESS, $message, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request, User $user)
    {
        $this->authorize('create', $user);
        try {

            DB::beginTransaction();

            // Extract request data
            $clientRequest = $request->only('surname', 'adress', 'telephone', 'photo');

            // Handle file upload
            $filePath = $request->file('photo')->store('photos', 'public');
            $clientRequest['photo'] = Storage::url($filePath);

            // Create client
            $client = Client::create($clientRequest);

            // Handle user creation if provided
            if ($request->has('user')) {
                $user = User::create([
                    'nom' => $request->input('user.nom'),
                    'prenom' => $request->input('user.prenom'),
                    'login' => $request->input('user.login'),
                    'password' => bcrypt($request->input('user.password')), // Ensure passwords are hashed
                    'role_id' => $request->input('user.role'),
                    'etat' => $request->input('user.etat')
                ]);

                $user->client()->save($client);
            }

            DB::commit();

            return $this->sendResponse(new ClientResource($client), StateEnum::SUCCESS, 'client creer avec succes', 201);
        } catch (\Exception $e) {
            DB::rollBack();

            // Ensure proper error response
            return $this->sendResponse(['error' => $e->getMessage()], StateEnum::ECHEC, 'erreur lors de la creation du client', 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id, User $user)
    {
        $this->authorize('view', $user);
        $client = Client::find($id);

        if (!$client) {
            return $this->sendResponse([], StateEnum::ECHEC, 'aucun client avec cet identifiant ', 400);
        }

        return $this->sendResponse(new ClientResource($client), StateEnum::SUCCESS, 'client retrouve avec success', 200);
    }

    public function get(string $id, User $user)
    {
        $this->authorize('view', $user);
        $clients = QueryBuilder::for(Client::class)
            ->allowedIncludes(['user'])
            ->with('user')
            ->where('id', $id)
            ->get();

        $message = $clients->count() . ' client(s) trouvé(s)';
        $data = $clients->count() > 0 ? new ClientCollection($clients) : [];

        return $this->sendResponse($data, StateEnum::SUCCESS, $message, 200);
    }

    public function getDettes(string $id, User $user)
    {
        $this->authorize('view', $user);

        $client = Client::find($id);

        if (!$client) {
            return $this->sendResponse(null, StateEnum::ECHEC, 'aucun client avec cet identifiant ', 400);
        }

        $dettes = QueryBuilder::for(Dette::class)
        ->where('client_id', $client->id)
        ->get();


        $message = $dettes->count() . ' client(s) trouvé(s)';
        $data = $dettes->count() > 0 ?$dettes : [];
        return $this->sendResponse($data, StateEnum::SUCCESS, $message, 200);
    }
}
