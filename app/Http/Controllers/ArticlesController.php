<?php

namespace App\Http\Controllers;

use App\Enums\StateEnum;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\StorearticlesRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Requests\UpdatearticlesRequest;
use App\Http\Requests\UpdateMassArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Models\Article;
use App\Models\articles;
use App\Traits\RestResponseTrait;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticlesController extends Controller
{
    use RestResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //$this->authorize('viewAny', $request);
        $include = $request->has('include')?  [$request->input('include')] : [];

        $disponible = $request->has('disponible')?  $request->input('disponible') : true;

        if($disponible && $request->input('disponible') == 'false'){
            $quantite = 0;
            $sign = '=';
        }else if($disponible && $request->input('disponible') == 'true'){
            $quantite = 1;
            $sign = '>=';
        }else{
            $quantite = -1;
            $sign = '>';
        }

        // $data = Article::with($include)->whereNotNull('user_id')->get();
        //return  response()->json(['data' => $data]);
      //  return  ArticleResource::collection($data);
       // return new ArticleCollection($data);
        $Articles = QueryBuilder::for(Article::class)
            ->allowedFilters(['surname'])
            ->allowedIncludes(['user'])
            ->where('quantite', $sign, $quantite)
            ->get();
        return new ArticleCollection($Articles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        //$this->authorize('create', $request);

        $data = $request->validated();
        $article = Article::create($data);
        return $this->sendResponse($article, StateEnum::SUCCESS, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $article = null;
        // dd(is_numeric($id));
        if(is_numeric($id) == 'number'){

            $article = Article::find($id);
        }else{
            $article = Article::where('libelle',$id)->first();
        }

        if (!$article) {
            return $this->sendResponse([], StateEnum::ECHEC, 404);
        }

        return $this->sendResponse($article, StateEnum::SUCCESS, 200);


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request,$id)
    {
        //$this->authorize('update', $request);

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
        return $this->sendResponse($article, StateEnum::SUCCESS, 200);
    }

    public function massUpdate(Request $request){
        //$this->authorize('update', $request);

        if(!$request->has('articles') || empty($request->input('articles'))){
            return $this->sendResponse([], StateEnum::ECHEC, 'les donnees sont requises',400);
        }

        $errors = [];

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
            }

            $validatedData = $validator->validated();
            $validatedData['quantite'] = $articleData->quantite + $validatedData['quantite'];
            $articleData->update($validatedData);
        }

        if(empty($errors)) {
            return $this->sendResponse([], StateEnum::SUCCESS, 'les mises à jour ont été effectuées avec succès');
        }

        return $this->sendResponse($errors, StateEnum::ECHEC, 'Certains mis a jour on echoue', 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //$this->authorize('delete', $id);
        $article = Article::find($id);

        if (!$article) {
            return $this->sendResponse([], StateEnum::ECHEC, 404);
        }

        $article->delete();
        return $this->sendResponse([], StateEnum::SUCCESS, 200);
    }
}
