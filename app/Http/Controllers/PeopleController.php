<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\People;
use Response;
use App\Extensions\Pagination;
use App\Extensions\Oauth2;

class PeopleController extends Controller
{
    /**
     * authentacate the main tabel from the request
     * 
     * @return void
     */
    public function authMainTabel()
    {
        Oauth2::verifyResourceRequest('people');
    }

    /**
     * athentacate the tabel relations of the main tabel
     *
     * @param Illuminate\Http\Reqfuest $request
     * @param array $relations the relations to be authacented
     *
     * @return array the relation to be loaded to the in the response
     */
    public function authRelation($request, $relations)
    {
        $returnArr = [];


        foreach($relations as $i => $relation)
        {
           if ($request->has('return') && strpos($request->get('return'), $relation) !== false) {

                Oauth2::verifyResourceRequest($relation);
            } else if (Oauth2::tokenHasScope($relation)){

                $returnArr[] = $relation;
            }
        }
        return $returnArr;
    }
    
    /**
     * End Point
     * GET people
     *
     * @param Request $request
     *
     * @return json string
     */
    public function getPeople(Request $request)
    {   
        $this->authMainTabel();
        $relationships = $this->authRelation(
            $request,
            ['comments']
        );
        
        $refine  = new RefineController;
        $people = $refine->handelRefine('App\\People', $relationships);
        
        return Pagination::paginate($people, 20);
    }
}
