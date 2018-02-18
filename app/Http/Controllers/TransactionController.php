<?php

namespace App\Http\Controllers;

use Validator;
use EnjinCoin\EnjinTransaction;
use EnjinCoin\EnjinIdentity;
use EnjinCoin\EnjinToken;
use EnjinCoin\Exceptions\BadRequestException;
use EnjinCoin\TransactionType;
use Illuminate\Http\Request;

class TransactionController extends Controller
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
        return response()->json(EnjinTransaction::with(['identity', 'recipient', 'token'])->get());
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
        $validator = $this->validator($request->all());
        if($validator->passes())
        {
            $identity = EnjinIdentity::findOrFail($request->input('identity_id'));
            $token = EnjinToken::findOrFail($request->input('token_id'));
            if($request->filled('recipient_id')){
                $recipient = EnjinIdentity::findOrFail($request->input('recipient_id'));
            }

            $transaction = new EnjinTransaction();
            $transaction->app_id = $request->input('app_id');
            if($request->filled('type')) {
                $transaction->type = new TransactionType($request->input('type'));
            }
            else {
                $transaction->type = new TransactionType(TransactionType::SEND);
            }
            if($request->filled('title'))
                $transaction->title = $request->input('title');
            if($request->filled('icon'))
                $transaction->icon = $request->input('icon');
            if($request->filled('value'))
                $transaction->value = $request->input('value');

            $transaction->identity()->associate($identity);

            if(isset($recipient)){
                $transaction->recipient()->associate($recipient);
            }
            $transaction->token()->associate($token);

            $transaction->save();

            return response()->json($transaction);
        }

        throw (new BadRequestException())->setInfoMessage('Some required data is missing, or is the wrong type (e.g. a String was given when an Integer was expected).');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = EnjinTransaction::findOrFail($id);
        $transaction->identity;
        $transaction->recipient;
        $transaction->token;
        return response()->json($transaction);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
            'identity_id' => 'required|integer',
            'token_id' => 'required|integer',
            'app_id' => 'required|integer',
            'recipient_id' => 'nullable|integer',
            'type' => 'string',
            'title' => 'nullable|string',
            'icon' => 'nullable|string',
            'value' => 'string',
        ]);
    }
}
