<?php

namespace App\Http\Controllers;

use App\Models\Categry;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function registrationPage(){
        return view('user.registration');
    }

    public function authPage(){
        return view('user.auth');
    }

    public function categoriesPage(){
        $categories = Categry::all();
        return view('admin.categories');
    }

    public function cabinetPage(){
        return view('user.cabinet');
    }

    public function editCategoryPage(Categry $category){
        return view('admin.editCategory', ['category'=>$category]);
    }

    public function productsPage(){
        return view('admin.products');
    }
}
