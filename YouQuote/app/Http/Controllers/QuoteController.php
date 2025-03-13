<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Quote::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            "content" => "required|string",
            "author" => "required|string",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "message" => "erreur lors de la creation",
                "data" => $validator->messages()
            ], 422);
        }
        $quote = Quote::create([
            'content' => $request->content,
            'author' => $request->author,
            'length' => str_word_count($request->content),
            'popularity' => 0
        ]);
        return response()->json([
            "message" => "la creation a ete bien effectuee ",
            "data" => $quote
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Quote  $quote)
    {
        return response()->json($quote);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quote $quote)
    {
        $validator = Validator::make($request->all(), [
            "content" => "required|string",
            "author" => "required|string",
            "popularity" => "nullable|numeric",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "message" => "erreur lors de la creation",
                "data" => $validator->messages()
            ], 422);
        }
        $quote->update([
            'content' => $request->content,
            'author' => $request->author,
            'length' => str_word_count($request->content),
            'popularity' => $request->popularity ?? $quote->popularity
        ]);
        return response()->json([
            "message" => "la modifiation a ete bien effectuee ",
            "data" => $quote
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quote $quote)
    {
        $quote->delete();
        return response()->json([
            "message" => "la suppression a ete bien effectuee ",
        ], 200);
    }
    public function rand($count=1)
    {
        if (!is_numeric($count) || $count <= 0) {
            return response()->json(['message' => 'le nombre doit etre positif et non nul !'], 422);
        }
        $quotes = Quote::all();
        if ($quotes->isEmpty()) {
            return response()->json(['message' => 'il y a aucune quote ici !!'], 422);
        }
        $quotes = $quotes->random($count);
        foreach ($quotes as $quote) {
            $quote->increment('popularity');
        }
        return response()->json($quotes);
    }
    


    public function filter($min, $max)
    {
        $quotes = Quote::whereBetween('length', [$min, $max])->get();
        if ($quotes->isEmpty()) {
            return response()->json(['message' => 'aucune quote convenable a cette longeur !!'], 200);
        }
        foreach ($quotes as $quote) {
            $quote->increment('popularity');
        }
        return response()->json($quotes, 200);
    }

    public function popularity()
    {
        $quotes = Quote::orderBy('popularity', 'desc')->get();
        if ($quotes->isEmpty()) {
            return response()->json(['message' => 'il y a aucune aucune quote !!'], 200);
        }
        return response()->json($quotes);
    }
}
