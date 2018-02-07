<?php
/**
 * Created by PhpStorm.
 * User: Moosley
 * Date: 06/02/2018
 * Time: 17:14
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use EnjinCoin\EnjinIdentity;
use EnjinCoin\EnjinIdentityField;
use Illuminate\Http\Request;

class IdentityController extends Controller
{

    /**
     * Enforce middleware.
     */
    public function __construct()
    {
        // Setup middleware so we require a Bearer Token for all routes,
        // other than the ones specified in the except clause.
        //$this->middleware('auth:api', ['except' => ['store', 'login', 'updateWallet']]);
    }

    protected $invalidKeyNames = ['identity_id', 'identity_code', 'ethereum_address'];

    /**
     * Display a listing of identities.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(EnjinIdentity::with('fields:key,field_value,searchable,displayable,unique')->get());
    }

    /**
     * Show the form for creating a new identity.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created identity.
     * This identity remains unattached from a user,
     * but can be linked to an ethereum address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Create a new Identity Model
        $identity = new EnjinIdentity();

        // If an ethereum address has been supplied then set it,
        // otherwise generate a linking code.
        if($request->filled('ethereum_address'))
        {
            $identity->ethereum_address = $request->input('ethereum_address');
        }
        else{
            $identity->linking_code = $identity->generateLinkingCode();
        }

        // Save the new identity
        $identity->save();

        // If identity fields have been supplied then add them.
        if($request->filled('fields'))
        {
            foreach ($request->input('fields') as $value)
            {
                // Check for keys which aren't allowed.
                if (in_array(strtolower(key($value)), $this->invalidKeyNames)) {
                    continue;
                }

                // Search to see if the current key is already in the database.
                $field = EnjinIdentityField::where('key', key($value))->first();

                // If not then create it.
                if(!isset($field))
                {
                    $field = new EnjinIdentityField();
                    $field->key = key($value);
                    $field->save();
                }

                // Attach to the identity
                $identity->fields()->attach($field, ['field_value' => $value[key($value)]]);
            }
        }

        return response()->json($identity);
    }

    /**
     * Display the specified identity.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(EnjinIdentity::with('fields:key,field_value,searchable,displayable,unique')->findOrFail($id));
    }

    /**
     * Show the form for editing the specified identity (needed?).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified identity (requires Bearer Token).
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified identity from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $identity = EnjinIdentity::findOrFail($id);
        $identity->fields;
        $identity->fields()->detach();
        return response()->json($identity->delete());
    }

    /**
     * Link a wallet with the specified linking code to an ethereum address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $linkingCode
     * @return \Illuminate\Http\Response
     */
    public function updateWallet(Request $request, $linkingCode)
    {
        // Check we have been supplied with an ethereum address
        // (Do we need to check if it's valid?)
        if($request->filled('ethereum_address'))
        {
            // Find the identity with the corresponding linking code,
            // or respond with a 404 if no identity was found.
            $identity = EnjinIdentity::where('linking_code', $linkingCode)->firstOrFail();

            // Update the ethereum_address field with the supplied address,
            // and set the linking code field to null.
            $identity->ethereum_address = $request->input('ethereum_address');
            $identity->linking_code = null;
            $identity->save();

            // Return the updated identity.
            return response()->json($identity);
        }

        // Respond with a Bad Request if no ethereum address was supplied.
        return response()->json(['error' => 'Bad Request'], 400);
    }
}