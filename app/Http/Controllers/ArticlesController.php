<?php

namespace App\Http\Controllers;

use App\Contracts\ArticleServiceInt;
use App\Enums\StateEnum;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Requests\UpdateMassArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Models\Article;
use App\Models\Role;
use App\Models\User;
use App\Traits\RestResponseTrait;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ArticlesController extends Controller
{
    use RestResponseTrait;

    private $articleService;
    public function __construct(ArticleServiceInt $articleService) {
        $this->articleService = $articleService;
        $this->authorizeResource(Article::class, 'article');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $filters = $request->only(['surname','username','disponible']);
        
        $articles = $this->articleService->getArticles($filters);

        $message = $articles->count().' article(s) trouvé(s)';
        $data = $articles->count() > 0 ? new ArticleCollection($articles) : [];
        
        return $this->sendResponse($data, StateEnum::SUCCESS, $message, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request,User $user)
    {
        $this->authorize('create', $user);

        $data = $request->validated();
        $article = $this->articleService->create($data);
        return $this->sendResponse(new ArticleCollection($article), StateEnum::SUCCESS, 'article created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id,User $user)
    {
        $this->authorize('view',$user);
        $article = null;
        // dd(is_numeric($id));
        if(is_numeric($id) == 'number'){

            $article = $this->articleService->find($id);
        }else{
            $article = $this->articleService->findByLibelle($id);
        }

        if (!$article) {
            return $this->sendResponse([], StateEnum::ECHEC, 'article non trouvé', 400);
        }

        return $this->sendResponse(new ArticleCollection($article), StateEnum::SUCCESS, 'ressource trouvée', 200);


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request,$id,User $user)
    {
        $this->authorize('update', $user);

        if(empty($request->all())){
            return $this->sendResponse([], StateEnum::ECHEC, 'pas de donnee fournit',400);
        }

        $article = Article::find($id);

        if (!$article) {
            return $this->sendResponse([], StateEnum::ECHEC, 404);
        }

        
        $data = $request->validated();
        if($request->has('quantite')){
            $request["quantite"] = $article->quantite + $request->quantite;
        }
        $article->update($data);
        return $this->sendResponse(new ArticleCollection($article), StateEnum::SUCCESS, 'article updated successfully', 200);
    }

    public function massUpdate(Request $request){
        //$this->authorize('update', $request);

        if(!$request->has('articles') || empty($request->input('articles'))){
            return $this->sendResponse([], StateEnum::ECHEC, 'les donnees sont requises',400);
        }

        $errors = [];
        $reussies = [];

        $articles = $request->input('articles');

        foreach($articles as $article){
            $articleData = Article::find($article['id']);

            if(!$articleData){
                $errors[] =  ['id' => $article['id'], 'message' => 'l\'article n\'existe pas'];
                continue;
            }

            $validationRequest = new UpdateMassArticleRequest();
            $validator = Validator::make($article, $validationRequest->rules());

            if($validator->fails()){
                array_push($errors, ['id' => $articleData['id'], 'message' => $validator->errors()->all()]);
                continue;
            }else{
                array_push($reussies, ['id' => $articleData['id'], 'message' => 'article mis à jour avec succès']);
            }

            $validatedData = $validator->validated();
            $validatedData['quantite'] = $articleData->quantite + $validatedData['quantite'];
            $articleData->update($validatedData);
        }

        if(empty($errors)) {
            return $this->sendResponse([], StateEnum::SUCCESS, 'les mises à jour ont été effectuées avec succès');
        }

        return $this->sendResponse(['errors'=>$errors,'reussies'=>$reussies], StateEnum::SUCCESS, 'Certains mis a jour on echoue', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, User $user)
    {
        $this->authorize('delete', $user);
        $article = Article::find($id);

        if (!$article) {
            return $this->sendResponse([], StateEnum::ECHEC, 404);
        }

        $article->delete();
        return $this->sendResponse([], StateEnum::SUCCESS, 200);
    }
}
