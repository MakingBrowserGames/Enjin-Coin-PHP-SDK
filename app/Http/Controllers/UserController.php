<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class UserController extends Controller
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

    /**
     * Display a listing of identities.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('identity')->get();

        return response()->json($users);
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
            $user = User::where('email', $request->input('email'))->first();
            if(isset($user))
            {
                return response()->json(['error' => 'Data Conflict (email already in use)'], 409);
            }

            // Create a new user.
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password')),
            ]);

            // Generate the access token so the user is 'logged in' after creating.
            $tokenStr = $user->createToken('Login Token')->accessToken;

            // Return the new user and the Bearer Token.
            return response()->json(['user' => $user, 'token' => $tokenStr]);
        }

        // Return a Bad Request status if not all the data was supplied.
        return response()->json(['error' => 'Bad Request'], 400);
    }

    /**
     * Display the specified user (requires Bearer Token).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Only allow the logged in user to access their own data.
        //if(Auth::id() == $id) {

            // Find the user in the database and return the model.
            $user = User::findOrFail($id);

            // Gather the associated EnjInWallet model.
            $user->identity;

            // Return the data as JSON.
            return response()->json($user);
        //}

        // If the check fails then the user isn't allowed to see anyone else's data.
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
        // Only allow the logged in user to update their own data.
        //if($request->user()->id == $id) {

            // Find the user in the database and return the model.
            $user = User::findOrFail($id);

            // Update the supplied fields (if any) and save.
            if($request->filled('name')){
                $user->name = $request->input('name');
            }
            if($request->filled('email')){
                $user->email = $request->input('email');
            }
            if($request->filled('password')){
                $user->password = bcrypt($request->input('password'));
            }

            // Save the updates to the database.
            $user->save();

            // Return the data as JSON.
            return response()->json($user);
        //}

        // If the check fails then the user isn't allowed to update anyone else's data.
        return response()->json(['error' => 'Unauthorized'], 401);
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
            $identity = User::where('email', $request->input('email'))->firstOrFail();

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
        // Detach fields from a linked identity and remove the identity too.
    }
}
