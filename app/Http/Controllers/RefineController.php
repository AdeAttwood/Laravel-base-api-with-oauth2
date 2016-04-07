<?php namespace App\Http\Controllers; 
use Illuminate\Http\Request;

use App\Http\Requests;
use OAuth2\HttpFoundationBridge\Response;

class RefineController extends Controller
{

    /**
     * The incoming request object
     *
     * @var Illuminate\Http\Request $request
     */
    protected $request;

    public function __construct()
    {
        $request = new Request;
        $this->request = $request->createFromGlobals();
    }

    /**
     * The main refine function
     * Uses the query param "return" to only return the selected fields
     * If the query param "return"
     *
     * @param string $modelClass
     * @param array  $relationships
     *
     * @return Illuminate\Database\Eloquent\Collection 
     *
     */
    public function handelRefine($modelClass, $relationships)
    {
        if ($this->request->has('return')) {
            return $this->refine($modelClass::all(), $relationships);
        } else {
            return $modelClass::with($relationships)->get();
        }
    }

    /**
     * refines a collection on the query string "return" 
     *
     * @param \Illuminate\Database\Eloquent\Collection $people
     * @param array $relationships
     *
     * @throws \Exception if there is not query string prameter "return"
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function refine(\Illuminate\Database\Eloquent\Collection $people, array $relationships)
    {
        if (!$this->request->has('return')) {
            throw new \Exception('Missing Pramiter: The query string dose not have the prameter "return"');
        }
        $fields = $this->parseFields();

        $people->each(function($item) use ($fields) {
            $item->setHidden(array_keys($item->getAttributes()));

            foreach($fields as $i => $field) {
                if(!is_array($field) && !$item->offsetExists($field)) {
                    $response = new Response();
                    $response->setError(400, 'Bad Request', "$field dose not exist");
                    $response->send(); 
                    die;
                } else if (is_array($field)) {
                    
                    if ($item->{$i} !== null) {
                        $item->{$i} = $this->handelSubRefine($item->{$i}, $field);
                        $item->addVisible($i);
                    } else {
                        $response = new Response();
                        $response->setError(400, 'Bad Request', "$i dose not exist");
                        $response->send();
                        die;
                    }
                } else {
                    $item->addVisible($field);
                }
            }

        });
        return $people;
    }

    /**
     * refine relations to the main tabel 
     *
     * @param \Illuminate\Database\Eloquent\Collection $subField
     * @param array $fields
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function handelSubRefine(\Illuminate\Database\Eloquent\Collection $subField, array $fields)
    {
        $subField->each(function ($item) use ($fields) {
            $item->setHidden(array_keys($item->getAttributes()));
            foreach ($fields as $key => $value) {
                if ($item->{$value} !== null) {
                    $item->addVisible($value);
                } else {
                    $responce = new Response();
                    $responce->setError(400, 'Bad Request', "$value dose not exist");
                    $responce->send();
                    die;
                }
            }
        });
        return $subField;
    }

    /**
     * parse the query string pramiter "return"
     * must be a comma seperated list with "-" seperating tabel relationships
     *
     * @return array
     */
    private function parseFields()
    {
        if ($this->request->has('return')) {
            $fields = explode(',', $this->request->return); 
            foreach ($fields as $key => $value) {
                if (strpos($value, '-') !== false) {
                    unset($fields[$key]);
                    $var = explode('-', $value);
                    if (!isset($fields[$var[0]])) {
                        $fields[$var[0]] = [];
                    }
                    $fields[$var[0]] = array_merge(array_slice($var, 1),$fields[$var[0]]) ;
                }
            } 
            return $fields;
        } else {
            return ['*'];
        }
    }
}

