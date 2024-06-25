<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\BaseGridRequest;
use App\Http\Requests\CreateUserRequest;
use App\Repositories\Admin\Common\Roles\AbilitiesRepository;
use App\Repositories\RegistrationMailRepository;
use App\Repositories\UserDataRepository;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Validator;


class PassportController extends Controller
{
    public $successStatus = 200;

    /**
     * @param CreateUserRequest $request
     * @return ResponseFactory|Response
     * @throws \Exception
     */
    public function register(CreateUserRequest $request)
    {
        $requestData = $request->validated();

        $verificationCode = random_int(10000, 99999);

        /** @var UserDataRepository $userDataRepository */
        $userDataRepository = app(UserDataRepository::class);
        $user = $userDataRepository->register($requestData, $verificationCode);

        if (array_key_exists('send_email', $requestData) && $requestData['send_email']) {
            /** @var RegistrationMailRepository $mailRepository */
            $mailRepository = app(RegistrationMailRepository::class);
            $mailRepository->sendConfirmationEmail($user, $verificationCode);
        }

        return response(['status' => 'success'], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Exception
     */
    public function confirm(Request $request) {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|size:5',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $input = $request->all();

        try {
            $user = User::whereNotNull('verification_token')->where('email', $input['email'])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(response('Registration not exists', 401));
        }

        if (!Hash::check($input['token'], $user->verification_token)) {
            throw new HttpResponseException(response('Code is not valid', 401));
        }

        if ($user->password) {
            throw new HttpResponseException(response('Email already confirmed', 401));
        }

        $user->password = bcrypt($input['password']);
        $user->verification_token = null;
        $user->save();

        $data = $user->only(['email', 'phone', 'first_name', 'last_name']);
        $data['access_token'] = $user->createToken('personal')->accessToken;

        return response()->json(['data' => $data], $this->successStatus);
    }

    public function restorePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $user = User::where('email', $request->get('email'))->firstOrFail();

        $restoreCode = random_int(10000, 99999);

        $user->update([
            'remember_token' => Hash::make($restoreCode),
            'password' => null,
        ]);

        $mailRepository = app(RegistrationMailRepository::class);
        $mailRepository->sendRestoreConfirmationEmail($user, $restoreCode);

        return response(['status' => 'success'], 200);
    }

    /**
     * Check user email, return response with next step -> auth/confirmation
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function checkEmail(Request $request)
    {
        $user = User::where('email', $request->get('email'))->firstOrFail();

        switch ($user->status) {
            case User::STATUS_BLOCKED:
                $nextStep = User::STATUS_BLOCKED;
                break;
            case User::STATUS_RESTORE:
                $nextStep = User::STATUS_RESTORE;
                break;
            case User::STATUS_AUTH:
                $nextStep = 'auth';
                break;
            case User::STATUS_NOT_COMPLETED:
                $nextStep = User::STATUS_NOT_COMPLETED;
                break;
            default:
                $nextStep = 'confirmation';
        }

        return response(['next_step' => $nextStep], 200);
    }

    public function confirmRestorePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|size:5',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }

        $input = $request->all();

        try {
            $user = User::whereNotNull('remember_token')->where('email', $input['email'])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(response('Password recovery is not valid', 401));
        }

        if (!Hash::check($input['token'], $user->remember_token)) {
            throw new HttpResponseException(response('The code is not valid', 401));
        }

        $user->remember_token = null;
        $user->password = bcrypt($input['password']);
        $user->save();

        return response(['status' => 'success'], 200);
    }

    public function getCurrentUserAbilities(BaseGridRequest $request)
    {
        $user = auth()->user();

        return AbilitiesRepository::getUserAbilitiesList($user->id);
    }
}
