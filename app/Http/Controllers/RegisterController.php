<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Managers\EmailVerificationManager;
use App\Models\Email;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller {

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        // The user must not be authenticated
        $this->middleware('guest');
    }

    public function register() {
        return view('myauth.register');
    }

    public function doRegister(Request $request) {
        // Validate
        $this->validate($request, [
            'first_name' => 'required|' . ValidationDefaults::FIRST_NAME,
            'last_name' => 'required|' . ValidationDefaults::LAST_NAME,
            'email' => 'required|' . ValidationDefaults::EMAIL . '|unique:emails',
            'password' => 'required|' . ValidationDefaults::PASSWORD_USER . '|confirmed',
            'accept_terms' => 'required',
        ], [
            'email.unique' => __('auth.emailUsed'),
            'accept_terms.required' => __('auth.mustAcceptTerms'),
        ]);

        // Create a new user
        $user = new User();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->password = Hash::make($request->input('password'));
        $user->locale = langManager()->getLocale(null);
        $user->save();

        // Create the email address
        $email = new Email();
        $email->user_id = $user->id;
        $email->email = $request->input('email');
        $email->save();

        // Make an email verification request
        EmailVerificationManager::createAndSend($email, true);

        // Create a user session and store the result
        $authResult = barauth()->getAuthenticator()->createSession($user);
        barauth()->setAuthState($authResult->getAuthState());

        // Show an error if session creation failed
        if($authResult->isErr())
            return redirect()
                ->back()
                ->with('error', __('general.serverError'));

        // Redirect the user to the dashboard
        return redirect()
            ->route('dashboard')
            ->with('success', __('auth.registeredAndLoggedIn'));
    }
}
