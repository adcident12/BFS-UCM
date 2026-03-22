<?php

namespace App\Http\Controllers;

class ApiDocsController extends Controller
{
    /** หน้า API Docs (custom — styled ตาม UCM) */
    public function index()
    {
        return view('api-docs.index');
    }
}
