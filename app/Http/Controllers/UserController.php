<?php

namespace App\Http\Controllers;

use EnjinCoin\EnjinWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Identity;

class IdentityController extends Controller
{
    /**
     * Enforce middleware.
     */
    public function __construct()
    {
        // Setup middleware so we require a Bearer Token for all routes,
        // other than the ones specified in the except clause.
        $this->middleware('auth:api', ['except' => ['store', 'login', 'updateWallet']]);
    }

    /**
     * Display a listing of identities.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check we have been supplied with the name, email and password.
        if($request->filled('name') && $request->filled('email') && $request->filled('password'))
        {
            // Check if this email address has already been registered.
            $identity = Identity::where('email', $request->input('email'))->first();
            if(isset($identity))
            {
                return response()->json(['error' => 'Data Conflict (email already in use)'], 409);
            }

            // Create a new identity.
            $identity = Identity::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password')),
            ]);

            // If an ethereum address has been supplied at the same time then link it right away,
            // otherwise generate a linking code.
            if($request->filled('ethereum_address')) {
                $identity->enjinWallet = EnjinWallet::create(['ethereum_address' => $request->input('ethereum_address')]);
            }
            else {
                $identity->enjinWallet = EnjinWallet::create(['linking_code' => $identity->generateLinkingCode()]);
            }

            // Generate the access token so the identity is 'logged in' after creating.
            $tokenStr = $identity->createToken('Token Name')->accessToken;

            // Return the new identity and the Bearer Token.
            return response()->json(['identity' => $identity, 'token' => $tokenStr]);
        }

        // Return a Bad Request status if not all the data was supplied.
        return response()->json(['error' => 'Bad Request'], 400);
    }

    /**
     * Display the specified identity (requires Bearer Token).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Only allow the logged in identity to access their own data.
        if(Auth::id() == $id) {

            // Find the identity in the database and return the model.
            $identity = Identity::findOrFail($id);

            // Gather the associated EnjInWallet model.
            $identity->enjinWallet;

            // Return the data as JSON.
            return response()->json($identity);
        }

        // If the check fails then the identity isn't allowed to see anyone else's data.
        return response()->json(['error' => 'Unauthorized'], 401);
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
        // Only allow the logged in identity to update their own data.
        if($request->user()->id == $id) {

            // Find the identity in the database and return the model.
            $identity = Identity::findOrFail($id);

            // Gather the associated EnjInWallet model.
            $identity->enjinWallet;

            // Update the supplied fields (if any) and save.
            if($request->filled('name')){
                $identity->name = $request->input('name');
            }
            if($request->filled('email')){
                $identity->email = $request->input('email');
            }
            if($request->filled('password')){
                $identity->password = bcrypt($request->input('password'));
            }
            if($request->filled('ethereum_address')){
                $identity->enjinWallet->ethereum_address = $request->input('ethereum_address');
                $identity->enjinWallet->save();
            }

            // If the user wants to change which ethereum address their wallet is linked to
            // then we can generate a new linking code for them.
            if($request->filled('relink_wallet') && $request->input('relink_wallet') == 1){
                $identity->enjinWallet->ethereum_address = null;
                $identity->enjinWallet->linking_code = $identity->generateLinkingCode();
                $identity->enjinWallet->save();
            }

            // Save the updates to the database.
            $identity->save();

            // Return the data as JSON.
            return response()->json($identity);
        }

        // If the check fails then the identity isn't allowed to update anyone else's data.
        return response()->json(['error' => 'Unauthorized'], 401);
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
            // Find the wallet with the corresponding linking code,
            // or respond with a 404 if no wallet was found.
            $wallet = EnjinWallet::where('linking_code', $linkingCode)->firstOrFail();

            // Update the ethereum_address field with the supplied address,
            // and set the linking code field to null.
            $wallet->ethereum_address = $request->input('ethereum_address');
            $wallet->linking_code = null;
            $wallet->save();

            // Return the updated wallet.
            return response()->json($wallet);
        }

        // Respond with a Bad Request if no ethereum address was supplied.
        return response()->json(['error' => 'Bad Request'], 400);
    }

    /**
     * Login the user via the API and generate another access token.
     * This is useful where a player may want to use different devices.
     *
     * We may want to extend this to include a device ID so we can
     * auto prune the token database.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Check the email and password were supplied.
        if($request->filled('email') && $request->filled('password'))
        {
            // Get the identity from the email address,
            // or respond with a 404 if no identity was found.
            $identity = Identity::where('email', $request->input('email'))->firstOrFail();

            // Check the password and return the identity (including wallet),
            // or respond with an Unauthorized status.
            if(\Hash::check($request->input('password'), $identity->password)){
                $identity->enjinWallet;
                $tokenStr = $identity->createToken('EnjinCoin Token')->accessToken;
                return response()->json(['identity' => $identity, 'token' => $tokenStr]);
            }

            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(['error' => 'Bad Request'], 400);
    }

    /**
     * Remove the specified identity from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
