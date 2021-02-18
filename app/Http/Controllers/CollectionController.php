<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Card;
use App\Models\User;
use App\Models\CardCollection;
use \Firebase\JWT\JWT;
use App\Http\Helpers\JWTtoken;

class CollectionController extends Controller
{
    public function Create_collection(Request $request){

        $data = $request->getContent();

        $data = json_decode($data);

        if ($data) {
            if (isset($data->collection_name) && isset($data->symbol_image) && isset($data->edition_date) && isset($data->card_name) && isset($data->card_description)) {
                if (!empty($data->collection_name) && !empty($data->symbol_image) && !empty($data->edition_date) && !empty($data->card_name) && !empty($data->card_description)) {
                    $response="";

                    $key = JWTtoken::getKey();
                    $headers = getallheaders();
                    $decoded = JWT::decode($headers['api_token'], $key, array('HS256'));
        
                    if ($decoded) {

                        $searchForCollection = Collection::where('name',$data->collection_name)->get()->first();

                        if (!$searchForCollection) {

                            $collection = new Collection();
                            $collection->name = $data->collection_name;
                            $collection->symbol_image = $data->symbol_image;
                            $collection->edition_date = $data->edition_date;
                            
                            try{
                                $collection->save();
                                $response = "Collection Created";               
                
                            }catch(\Exception $e){
                                $response = $e->getMessage();
                            }            
                
                            //Creo la carta nueva a partir de los datos introducidos por el usuario
                            $new_card = new Card();
                            $new_card->name = $data->card_name;
                            $new_card->description = $data->card_description;
                            $new_card->admin_id = $decoded->id;
                
                            //Guardo la nueva carta
                            try {
                                $new_card->save();
                            }catch(\Exception $e){
                                $response = $e->getMessage();
                            }
                            
                            //Relaciono la carta y la coleccion
                            $card_collection = new CardCollection();
                            $card_collection->card_id = $new_card->id;
                            $card_collection->collection_id = $collection->id;
                
                            //Guardo la relacion entre la carta y la coleccion
                            try {
                                $card_collection->save();
                            }catch(\Exception $e){
                                $response = $e->getMessage();
                            }
                        }else{
                            $response = "Collection already exists";
                        }
                    }                        
                }else{
                    $response = "Empty data";
                }
            }else{
                $response = "No valid data";
            }
        }else{
            $response = "No valid data";
        }
        return response()->json($response);
    }

    public function Collection_update(Request $request, $id){

        $data = $request->getContent();

        $data = json_decode($data);

        if ($data) {
            
            if (isset($data->symbol_image) && isset($data->edition_date)) {
                if (!empty($data->symbol_image) && !empty($data->edition_date)) {
                    $response="";
                    $collection = Collection::find($id);

                    if ($collection) {
                        $collection->symbol_image = $data->symbol_image;
                        $collection->edition_date = $data->edition_date;
                        try{
                            $collection->save();
                            $response = "Collection updated";

                        }catch(\Exception $e){
                            $response = $e->getMessage();
                        } 
                    }else{
                        $response = "No valid collection";
                    }
                }else{
                    $response = "Empty data";
                }
            }else{
                $response = "No valid";
            }                       
        }else{
            $response = "No valid data";
        }
        return response()->json($response);
    }

    public function Add_card_to_collection(Request $request){

        $data = $request->getContent();

        $data = json_decode($data);

        if ($data) {
            
            if (isset($data->card_id) && isset($data->collection_id)) {
                if (!empty($data->card_id) && !empty($data->collection_id)) {
                    $response="";
            
                    $collection = Collection::find($data->collection_id);
        
                    if ($collection){                
                        $card = Card::find($data->card_id);
                        if ($card) {
                            $card_collection = new CardCollection();                    
                            $card_collection->card_id = $data->card_id;
                            $card_collection->collection_id = $data->collection_id;
                            try{
                                $card_collection->save();
                                $response = "Card added to collection";    
                            }catch(\Exception $e){
                                $response = $e->getMessage();
                            } 
                        }else{
                            $response = "No valid card";
                        }               
                    }else{
                        $response = "No valid collection";
                    }
                }else{
                    $response = "Empty data";
                }
            }else{
                $response = "No valid data";
            }                       
        }else{
            $response = "No valid data";
        }
        return response()->json($response);
    }    
}
