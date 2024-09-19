<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use Illuminate\Http\Request;

class ComprasController extends Controller
{
    public function index(){
        return Compra::where('estado', 1)->get();
    }
}
