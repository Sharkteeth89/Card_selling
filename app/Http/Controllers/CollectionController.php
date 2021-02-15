<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Card;
use App\Models\User;
use App\Models\CardCollection;

class CollectionController extends Controller
{
    public function Create_collection(Request $request){

        $data = $request->getContent();

        $data = json_decode($data);

        if ($data) {
            $response="";
            $collection = new Collection();

            $collection->name = $data->collection_name;
            $collection->symbol_image = $data->symbol_image;
            $collection->edition_date = $data->edition_date;

            $admin = User::where('user_token',$data->user_token)->get()->first();
            
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
            $new_card->admin_id = $admin->id;

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
            $response = "No valid data";
        }
        return response()->json($response);
    }
    
    public function Collection_update(Request $request, $id){

        $data = $request->getContent();

        $data = json_decode($data);

        if ($data) {
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
            $response = "No valid data";
        }
        return response()->json($response);
    }

    public function Add_card_to_collection(Request $request){

        $data = $request->getContent();

        $data = json_decode($data);

        if ($data) {
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
            $response = "No valid data";
        }
        return response()->json($response);
    }

    
}
