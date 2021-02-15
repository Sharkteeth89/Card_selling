<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\User;
use App\Models\UserCard;
use App\Models\Collection;
use App\Models\CardCollection;

class CardController extends Controller
{
    public function Create_card(Request $request){

        $data = $request->getContent();

        $data = json_decode($data);       

        if ($data) {

            $key = MyJWT::getKey();
		    $headers = getallheaders();
		    $decoded = JWT::decode($headers['api_token'], $key, array('HS256'));

            $response="";
            $card = new Card();


            $card->name = $data->card_name;
            $card->description = $data->card_description;
            $card->admin_id = $decoded->id;
            
            $collection = Collection::where('name',$data->collection_name)->get()->first();

            try{
                $card->save();
                $response = "Card Created";               

            }catch(\Exception $e){
                $response = $e->getMessage();
            }
            $card_collection = new CardCollection();

            if (!$collection) {                    
                //Creo la coleccion y le asigno el nombre
                $new_collection = new Collection();
                $new_collection->name = $data->collection_name;
                //Guardo la nueva coleccion
                try {
                    $new_collection->save();
                }catch(\Exception $e){
                    $response = $e->getMessage();
                }                   

                //Creo la relacion entre la carta y la coleccion                  
                $card_collection->card_id = $card->id;
                $card_collection->collection_id = $new_collection->id;

                //Guardo la relacion entre la carta y la coleccion
                try {
                    $card_collection->save();
                }catch(\Exception $e){
                    $response = $e->getMessage();
                }  
                
            }else{
                //Asigno la carta a la coleccion
                $card_collection->card_id = $card->id;
                $card_collection->collection_id = $collection->id;
                $card_collection->save(); 
            }            
            
        }else{
            $response = "No valid data";
        }
        return response()->json($response);
    }

    public function Card_update(Request $request, $id){

        $data = $request->getContent();

        $data = json_decode($data);

        if ($data) {
            $response="";
            $card = Card::find($id);

            if ($card) {

                $card->name = $data->card_name;
                $card->description = $data->card_description;
                try{
                    $card->save();
                    $response = "Card updated";

                }catch(\Exception $e){
                    $response = $e->getMessage();
                } 
            }else{
                $response = "Not a valid card";
            }           
        }else{
            $response = "No valid data";
        }
        return response()->json($response);
    }

    public function Card_list(Request $request){
        $data = $request->getContent();

        $data = json_decode($data);

        $response=[];                                      

        $cards = Card::all();
        
        if (!$cards->isEmpty()){              

            for ($i=0; $i <count($cards) ; $i++) {                  

                $response[$i] = [
                    "id" => $cards[$i]->id,
                    "name" => $cards[$i]->name,
                    "description" => $cards[$i]->description,				               				
                ];

                for ($j=0; $j < count($cards[$i]->collection); $j++) {

                    $response[$i][$j]["Collection name"] = $cards[$i]->collection[$j]->name;                    
                }
            }
        }else{            
            $response = "No cards";
        }           
        
        return response()->json($response);
    }

    public function Card_list_by_name(Request $request){
        $data = $request->getContent();

        $data = json_decode($data);

        $response=[];  

        if (isset($data->card_name)) {                              

            $cards = Card::where('name',$data->card_name)->get();
            
            if (!$cards->isEmpty()){              

                for ($i=0; $i <count($cards) ; $i++) {                  

                    $response[$i] = [
                        "id" => $cards[$i]->id,
                        "name" => $cards[$i]->name,
                        "description" => $cards[$i]->description,				               				
                    ];

                    for ($j=0; $j < count($cards[$i]->collection); $j++) {

                        $response[$i][$j]["Collection name"] = $cards[$i]->collection[$j]->name;                    
                    }
                }
            }else{               
                $response = "No cards";
            }           
        }else{            
            $response = "No data";
        } 
        return response()->json($response);
    }

    public function get_card_by_ID($id){
        $response;  

        $card = Card::where('id', $id)->get()->first();

        if ($card) {
            $response = [
                "id" => $card->id,
                "name" => $card->name,
                "description" => $card->description,				               				
            ];                    
        }else{            
            $response = "No Card";
        } 
        return response()->json($response);
    }

    public function Sell_card(Request $request){
                
        $data = $request->getContent();

        $data = json_decode($data);

        if ($data) {

            $card = Card::find($data->card_id);
            $user = User::where('user_token',$data->user_token)->get()->first();

            if ($user) {
                if ($card) {
                    $selling = new UserCard();
                    $selling->card_id = $data->card_id;
                    $selling->user_id = $user->id;
                    $selling->total_price = $data->total_price;
                    $selling->quantity = $data->quantity;

                    try{
                        $selling->save();
                        $response = "The card is now in sale";               
        
                    }catch(\Exception $e){
                        $response = $e->getMessage();
                    }

                }else{
                    $response = "No valid card";
                }
            }else{
                $response = "No valid User";
            }
            

        }

        return response()->json($response);
    }

    public function Cards_in_sale(Request $request){
        $data = $request->getContent();

        $data = json_decode($data);

        if ($data) {
            $response=[];

            $cards = Card::where('name',$data->card_name)->get();

            for ($i=0; $i <count($cards) ; $i++) {  
                for ($j=0; $j < count($cards[$i]->user); $j++) {
                    $response[] = [
                    "Card_id" => $cards[$i]->id,
                    "Card name" => $cards[$i]->name,
                    "Quantity" => $cards[$i]->user[$j]->pivot->quantity,                    
                    "Total_price" => $cards[$i]->user[$j]->pivot->total_price,
                    "Seller username" => $cards[$i]->user[$j]->username                                      				
                    ];     
                }               
            }
        }else{
            $response= "no valid data"; 
        } 
        return response()->json($response);
    }
}
