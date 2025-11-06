<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Http\Discovery\Psr17Factory;
use Laminas\Diactoros\ServerRequestFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

class UserController extends BaseController
{
    //
    public function __construct() {}

    public function register(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;

        if (!$email || !$password) {
            return response()->json([
                'detail' => 'Email or password are required.'
            ], 400);
        }

        if (User::where('email', $email)->exists()) {
            return response()->json([
                'detail ' => "Email already exists."
            ], 400);
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        return $this->response($user, 'User registered successfully.', 201);
    }

    public function login(Request $request, AccessTokenController $accessTokenController)
    {
        $username = $request->username;
        $password = $request->password;

        $user = User::query()->where('email', $username)->first();
        if (!$user) return response()->json([
            'detail' => 'User not found.'
        ], 404);

        $authenticate = password_verify($password, $user->password);
        if (!$authenticate) response()->json([
            'detail' => 'Invalid password.'
        ], 401);

        if ($authenticate) {
            $post_data = [
                'grant_type' => 'password',
                'client_id' => config('services.CLIENT_ID'),
                'client_secret' => config('services.CLIENT_SECRET'),
                'username' => $username,
                'password' => $password,
            ];
        };

        $factory = new Psr17Factory();
        $new_request = $factory->createServerRequest('POST', '/oauth/token')->withHeader('Content-Type', 'application/x-www-form-urlencoded')->withParsedBody($post_data);

        $psrResponse = new \Laminas\Diactoros\Response();

        $tokenResponse = $accessTokenController->issueToken($new_request, $psrResponse);

        $content = $tokenResponse->getContent();
        $data = json_decode($content, true);
        $response = [
            'user' => $user,
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_in' => $data['expires_in']
        ];

        return response() -> json($response, 200);
    }
}
