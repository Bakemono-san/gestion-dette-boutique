<?php

namespace App\Http\Controllers;

use App\Enums\StateEnum;
use App\Http\Requests\StoreClientRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Models\Client;
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
    public function index(Request $request)
    {
        $compte = $request->has('compte');

        if($compte && $request->input('compte') == 'oui'){
            $field = 'user_id';
            $value = 0;
            $sign = '>';
        }else if($compte && $request->input('compte') == 'non'){
            $field = 'user_id';
            $value = null;
            $sign = '=';
        }else{
            $field = 'id';
            $value = 0;
            $sign = '>=';
        }

        //return  response()->json(['data' => $data]);
        //  return  ClientResource::collection($data);
        // return new ClientCollection($data);
        $clients = QueryBuilder::for(Client::class)
            ->allowedFilters(['surname','telephone'])
            ->allowedIncludes(['user'])
            ->where($field, $sign, $value)
            ->get();
        return new ClientCollection($clients);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        // $request->validate();
        // dd("hello");
        try {

            DB::beginTransaction();

            // Extract request data
            $clientRequest = $request->only('surname', 'adress', 'telephone', 'photo');

            // Handle file upload
            $filePath = $request->file('photo')->store('photos', 'public');
            $clientRequest['photo'] = Storage::url($filePath);
            // dd($clientRequest);

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

            return $this->sendResponse(new ClientResource($client), StateEnum::SUCCESS, 'client creer avec succes',201);
        } catch (\Exception $e) {
            DB::rollBack();

            // Ensure proper error response
            return $this->sendResponse(['error' => $e->getMessage()], StateEnum::ECHEC, 'erreur lors de la creation du client',500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $client = Client::find($id);

        if (!$client) {
            return $this->sendResponse([], StateEnum::ECHEC,'aucun client avec cet identifiant ', 404);
        }

        return $this->sendResponse(new ClientResource($client), StateEnum::SUCCESS,'client retrouve avec success', 200);
    }

    public function get(string $id){
        $clients = QueryBuilder::for(Client::class)
            ->allowedIncludes(['user'])
            ->with('user')
            ->where('id', $id)
            ->get();
            
        return new ClientCollection($clients);
    }

    public function getDettes(string $id){
        $clients = QueryBuilder::for(Client::class)
            ->allowedIncludes(['dette'])
            ->with('dette')
            ->where('id', $id)
            ->get();
            
        return new ClientCollection($clients);
    }
}
