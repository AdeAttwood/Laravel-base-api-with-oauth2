<?php 

namespace App\Extensions;

use Illuminate\Pagination\LengthAwarePaginator;

class Pagination
{
    public static function paginate($collection, $perPage)
    {
        $curretPage = LengthAwarePaginator::resolveCurrentPage(); 
        $currentPageSearchResults = $collection->slice($curretPage * $perPage, $perPage)->all(); 
        return new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);
    }
}
