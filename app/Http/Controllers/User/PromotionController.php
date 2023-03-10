<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Apartment;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index(){
        $promotions =Promotion::All();

        return view('user.promotions.index', compact('promotions'));
    }

    public function show(Promotion $promotion, Apartment $apartment){

        if (Auth::user()->id === $apartment->user_id) {
        return view('user.apartments.checkout', compact('promotion', 'apartment'));
        } else {
            return view('errorPage', ['message' => 'Non sei autorizzato a vedere questo appartamento']);
        }
    }

}
