<?php

namespace App\Http\Controllers\User;

use App\Models\Apartment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $apartments = Apartment::where('user_id', $user->id)->get();

        return view("user.apartments.index", compact('apartments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $services = Service::all();
        $promotions = Promotion::all();

        return view("user.apartments.create", compact('services', 'promotions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $data = $request->all();
        $via = urlencode($data['address']);
        
        $rawData = file_get_contents("https://api.tomtom.com/search/2/geocode/". $via . ".json?storeResult=false&view=Unified&limit=1&key=sGNJHBIkBGVklWlAnKDehryPD39qsJxn" );
        $rawData = json_decode($rawData);
        
        $apartment = new Apartment();
        
        $apartment->latitude = $rawData->results[0]->position->lat;
        $apartment->longitude = $rawData->results[0]->position->lon;

        if ($data['visibility']){
            $data['visibility'] = 1;
        } else{
            $data['visibility'] = 0;
        }

        


        $user = Auth::user();

        $apartment->fill($data);

        $apartment->user_id = $user->id;

        $path = Storage::put('cover_img', $data['img_cover']);

        $apartment->img_cover = $path?? 'cover_img/NoImageFound.jpg.png';

        $apartment->save();


        if ($request->has('services')) {
            $apartment->services()->attach($data['services']);
        }
        if ($request->has('promotions')) {
            $apartment->promotions()->attach($data['promotions']);
        }
        
        return redirect()->route("user.apartments.show", $apartment->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Apartment $apartment)
    {
        return view("user.apartments.show", compact("apartment"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Apartment $apartment)
    {
        return view("user.apartments.edit", compact("apartment"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Apartment $apartment)
    {

        $data = $request->all();

        $apartment->fill($data);

        if (key_exists('img_cover', $data)) {
            $path = Storage::put('cover_img', $data['img_cover']);
            Storage::delete($apartment->img_cover);
        }
        

        $apartment->img_cover = $path?? $apartment->img_cover;

        if ($request->has('services')) {
            $apartment->services()->sync($data['services']);
        }
        if ($request->has('promotions')) {
            $apartment->promotions()->sync($data['promotions']);
        }

        $apartment->save();

        return redirect()->route("user.apartment.show", compact("apartment"));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Apartment $apartment)
    {
        $apartment->services()->detach();
        $apartment->promotions()->detach();
        $apartment->user()->dissociate();
        $apartment->delete();

        return redirect()->route("dashboard");
    }
}