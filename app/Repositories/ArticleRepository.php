<?php

namespace App\Repositories;

use App\Contracts\ArticleRepositoryImpl;
use App\Models\Article;

class ArticleRepository implements ArticleRepositoryImpl{

    protected $model;
    public function __construct(Article $article){
        $this->model = $article;
    }

    public function all(){
        return $this->model->all();
    }

    public function find($id){
        return $this->model->find($id);
    }

    public function create(array $data){
        return $this->model->create($data);
    }

    public function update($id, array $data){
        $article = $this->model->find($id);
        $article->update($data);
        return $article;
    }

    public function delete($id){
        $article = $this->model->find($id);
        $article->delete();
        return true;
    }

    public function findByLibelle($libelle){
        return $this->model->where('libelle', $libelle)->first();
    }

    public function findEtat($etat){
        return $this->model->where('etat', $etat)->get();
    }

    public function getArticles(array $filters = []){
        $query = $this->model->query();

        // Apply query scopes based on filters
        if (isset($filters['disponible'])) {
            $query->disponible($filters['disponible']);
        }

        if (isset($filters['surname'])) {
            $query->filterBySurname($filters['surname']);
        }

        if(isset($filters['surname'])) {
            $query->filterByUsername($filters['username']);
        }

        return $query->get();
    }
}