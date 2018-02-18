<?php

namespace App\Http\Controllers;

use Validator;
use EnjinCoin\EnjinToken;
use EnjinCoin\Exceptions\BadRequestException;
use EnjinCoin\Exceptions\DataConflictException;
use EnjinCoin\Exceptions\NotYetImplementedException;
use Illuminate\Http\Request;

class TokenController extends Controller
{

    /**
     * Enforce middleware.
     */
    public function __construct()
    {
        // Setup middleware so we require a Bearer Token for all routes,
        // other than the ones specified in the except clause.
        //$this->middleware('auth:api', ['except' => ['store']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(EnjinToken::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming data then check to see
        // if the token already exists in the database.
        // If it does then return a 409 Data Conflict.
        // If not proceed to make the token using the supplied token_id.
        // We will then need to pick up the rest of the data from then Ethereum blockchain.
        $validator = $this->validator($request->all());
        if($validator->passes())
        {
            EnjinToken::findAndFail($request->input('token_id'), (new DataConflictException())->setInfoMessage('Token already exists'));

            $token = new EnjinToken();
            $token->token_id = $request->input('token_id');
            $token->fill($request->all());
            $token->save();

            return response()->json($token);
        }

        throw (new BadRequestException())->setInfoMessage('Some required data is missing, or is the wrong type (e.g. a String was given when an Integer was expected).');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EnjinToken  $enjinToken
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(EnjinToken::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EnjinToken  $enjinToken
     * @return \Illuminate\Http\Response
     */
    public function edit(EnjinToken $enjinToken)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EnjinToken  $enjinToken
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Try to get the specified token or return a 404
        $token = EnjinToken::findOrFail($id);

        // Validate the supplied data is correct.
        // We manually set the required token_id and app_id fields
        // from the existing data if needed as they will fail
        // validation if not present.
        $data = $request->all();
        $data['token_id'] = $token->token_id;
        if(!$request->filled('app_id'))
            $data['app_id'] = $token->app_id;
        $validator = $this->validator($data);

        // If the data passes validation then update the
        // supplied fields.
        if($validator->passes())
        {
            $token->fill($request->all());
            $token->save();
            return response()->json($token);
        }

        throw (new BadRequestException())->setInfoMessage('Some data may be the wrong type (e.g. a String was given when an Integer was expected).');;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EnjinToken  $enjinToken
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(EnjinToken::findOrFail($id)->delete());
    }

    /**
     * Check the input data meets the requirements for the database.
     *
     * @param $data
     * @return mixed
     */
    protected function validator($data)
    {
        return Validator::make($data, [
            'token_id' => 'required|integer',
            'app_id' => 'required|integer',
            'creator' => 'nullable|string',
            'adapter' => 'nullable|string',
            'name' => 'nullable|string',
            'icon' => 'nullable|string',
            'totalSupply' => 'nullable|string',
            'exchangeRate' => 'nullable|string',
            'decimals' => 'nullable|integer',
            'maxMeltFee' => 'nullable|string',
            'meltFee' => 'nullable|string',
            'transferable' => 'nullable|integer'
        ]);
    }
}
